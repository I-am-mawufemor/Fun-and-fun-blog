<?php

namespace Mawufemor\Techandfun\model;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;
use PDOException;

class User
{
    public function __construct(private PDO $pdo) {}

    // method to fetch user
    public function getUser(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, full_name, telephone, password, role FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function getUserById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, full_name, email, telephone, gender, role FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // method to fetch password
    public function getPassword(int $id): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT password FROM users WHERE user_id = ? LIMIT 1"
        );

        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['password'] ?? null;
    }

    // Store a reset token against a user
    public function saveResetToken(int $userId, string $tokenHash, string $otpCode, string $expiry): bool
    {
        $del = $this->pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $del->execute([$userId]);

        $stmt = $this->pdo->prepare(
            "INSERT INTO password_resets (user_id, token_hash, otp_code, expires_at)
         VALUES (?, ?, ?, ?)"
        );

        return $stmt->execute([$userId, $tokenHash, $otpCode, $expiry]);
    }


    public function incrementAttemptsByEmail(string $email): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE password_resets pr
         JOIN users u ON u.id = pr.user_id
         SET pr.attempts = pr.attempts + 1
         WHERE u.email = ?
         AND pr.used = 0
         AND pr.expires_at > NOW()"
        );
        $stmt->execute([$email]);
    }

    // Check whether the current active reset record has exceeded the allowed attempts
    public function isResetLockedByEmail(string $email, int $maxAttempts = 5): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT pr.attempts FROM password_resets pr
         JOIN users u ON u.id = pr.user_id
         WHERE u.email = ?
         AND pr.used = 0
         AND pr.expires_at > NOW()
         ORDER BY pr.created_at DESC
         LIMIT 1"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row && $row['attempts'] >= $maxAttempts;
    }

    // Look up a valid, unlocked OTP, scoped to the user's email
    public function getResetTokenByOtp(string $email, string $otpCode): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT pr.id, pr.user_id FROM password_resets pr
         JOIN users u ON u.id = pr.user_id
         WHERE u.email = ?
         AND pr.otp_code = ?
         AND pr.used = 0
         AND pr.expires_at > NOW()
         LIMIT 1"
        );
        $stmt->execute([$email, $otpCode]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Check  throttle how often password resets can be REQUESTED (not guessed)
    public function isRequestLocked(string $email, int $maxRequests = 5, int $windowHours = 24): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT reset_request_count, reset_request_window_start
         FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !$row['reset_request_window_start']) {
            return false;
        }

        $windowStart = new \DateTime($row['reset_request_window_start']);
        $now         = new \DateTime();
        $hoursElapsed = ($now->getTimestamp() - $windowStart->getTimestamp()) / 3600;

        if ($hoursElapsed >= $windowHours) {
            return false;
        }

        return $row['reset_request_count'] >= $maxRequests;
    }

    public function recordResetRequest(string $email, int $windowHours = 24): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT reset_request_count, reset_request_window_start
         FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $windowExpired = true;
        if ($row && $row['reset_request_window_start']) {
            $windowStart  = new \DateTime($row['reset_request_window_start']);
            $now          = new \DateTime();
            $hoursElapsed = ($now->getTimestamp() - $windowStart->getTimestamp()) / 3600;
            $windowExpired = $hoursElapsed >= $windowHours;
        }

        if ($windowExpired) {
            $stmt = $this->pdo->prepare(
                "UPDATE users SET reset_request_count = 1, reset_request_window_start = NOW() WHERE email = ?"
            );
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE users SET reset_request_count = reset_request_count + 1 WHERE email = ?"
            );
        }

        $stmt->execute([$email]);
    }

    // Re-verify the reset session is still valid at the moment of submission —
    public function getValidResetToken(int $tokenId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, user_id FROM password_resets
         WHERE id = ?
         AND user_id = ?
         AND used = 0
         AND expires_at > NOW()
         LIMIT 1"
        );
        $stmt->execute([$tokenId, $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET password = ? WHERE id = ?"
        );

        return $stmt->execute([$passwordHash, $userId]);
    }

    // Invalidate ALL outstanding reset tokens for this user once a reset completes

    public function invalidateAllResetTokens(int $userId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0"
        );
        $stmt->execute([$userId]);
    }

    // method to create user
    public function createUser(
        string $fullName,
        string $email,
        string $telephone,
        string $gender,
        string $password,
        string $role = 'user'  // default to 'user'
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (full_name, email, telephone, gender, password, role)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        try {
            $stmt->execute([$fullName, $email, $telephone, $gender, $password, $role]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return false; 
            }
            throw $e; 
        }
    }
}
