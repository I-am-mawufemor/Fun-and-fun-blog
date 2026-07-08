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
            "SELECT id, full_name, password, role FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    // method to reset password
    public function updatePassword(string $newHash, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET password = ? WHERE user_id = ?"
        );

        $stmt->execute([$newHash, $userId]);

        return true;
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
    public function saveResetToken(int $userId, string $token, string $expiry): bool
    {
        // Invalidate any existing unused tokens for this user first
        $del = $this->pdo->prepare(
            "DELETE FROM password_resets WHERE user_id = ?"
        );
        $del->execute([$userId]);

        $stmt = $this->pdo->prepare(
            "INSERT INTO password_resets (user_id, token, expires_at)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$userId, $token, $expiry]);

        return true;
    }

    // Look up a valid (unused, unexpired) token
    public function getResetToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, user_id, expires_at FROM password_resets
             WHERE token = ? AND used = 0 AND expires_at > NOW()
             LIMIT 1"
        );

        $stmt->execute([$token]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Mark the token as used so it cannot be replayed
    public function markTokenUsed(int $tokenId): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE password_resets SET used = 1 WHERE id = ?"
        );

        $stmt->execute([$tokenId]);
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
            // 23000 is the SQLSTATE code for duplicate entry
            if ($e->getCode() === '23000') {
                return false; // duplicate email — let the controller handle the message
            }
            throw $e; // rethrow anything else unexpected
        }
    }
}