<?php

namespace Mawufemor\Techandfun\controller;

use Mawufemor\Techandfun\model\User;
use Mawufemor\Techandfun\services\NotificationServices;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;

class AuthController
{
    private User $userModel;
    private NotificationServices $notificationService;

    public function __construct(private PDO $pdo)
    {
        $this->userModel = new User($this->pdo);
        $this->notificationService = new NotificationServices();
    }

    public function forgotPassword(): void
    {
        if (isLoggedIn()) {
            header('Location: ?page=home');
            exit();
        }

        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        validateCSRF($_POST['csrf_token'] ?? '');

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }


        $_SESSION['reset_email'] = $email;

        if ($this->userModel->isRequestLocked($email)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Too many reset requests. Please try again later.']);
            exit;
        }

        $user = $this->userModel->getUser($email);

        if ($user) {
            $userId = $user['id'];
            $phone  = $user['telephone'];
            $name   = $user['full_name'];

            $rawToken  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $otpCode   = (string) random_int(100000, 999999);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->userModel->saveResetToken($userId, $tokenHash, $otpCode, $expiresAt);
            $this->userModel->recordResetRequest($email);

            $resetLink = "https://techandfun.com/reset-password?token=$rawToken";

            $emailSubject = "Password Reset Request";
            $emailMessage = "Hello $name,<br><br>We received a request to reset your password. Please click <a href=\"$resetLink\">here</a> to reset your password.";
            $this->notificationService->sendEmail($email, $emailSubject, $emailMessage);

            $smsMessage = "Hello $name, your password reset OTP is: $otpCode. It will expire in 30 minutes.";
            $this->notificationService->sendSMS($phone, $smsMessage);
        }

        echo json_encode(['success' => true, 'message' => 'If an account exists for that email, reset instructions have been sent.']);
        exit;
    }

    public function verfytoken(): void
    {
        if (isLoggedIn()) {
            header('Location: ?page=home');
            exit;
        }

        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            exit;
        }

        validateCSRF($_POST['csrf_token'] ?? '');

        $email   = $_SESSION['reset_email'] ?? '';
        $otpCode = trim($_POST['otpCode'] ?? '');

        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Reset session expired. Please start again.']);
            exit;
        }

        if (empty($otpCode)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'One Time Password is required']);
            exit;
        }

        try {
            if ($this->userModel->isResetLockedByEmail($email)) {
                http_response_code(429);
                echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please request a new code.']);
                exit;
            }

            $resetRow = $this->userModel->getResetTokenByOtp($email, $otpCode);

            if (!$resetRow) {
                $this->userModel->incrementAttemptsByEmail($email);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid or expired code.']);
                exit;
            }

            $_SESSION['reset_user_id']  = $resetRow['user_id'];
            $_SESSION['reset_token_id'] = $resetRow['id'];

            echo json_encode(['success' => true, 'message' => 'Code verified.']);
            exit;
        } catch (\PDOException $e) {
            error_log('OTP verification failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
            exit;
        }
    }
    public function resetPassword(): void
    {
        // Must have completed OTP verification first — no session, no access.
        if (empty($_SESSION['reset_user_id']) || empty($_SESSION['reset_token_id'])) {
            header('Location: ?page=forgot-password');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require ROOT . '/app/view/auth/reset_password.php';
            return;
        }

        header('Content-Type: application/json');

        validateCSRF($_POST['csrf_token'] ?? '');

        $userId  = (int) $_SESSION['reset_user_id'];
        $tokenId = (int) $_SESSION['reset_token_id'];

        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit;
        }

        if ($password !== $confirmPassword) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            exit;
        }

        try {

            // Re-check the token is STILL valid right now 
            $resetRow = $this->userModel->getValidResetToken($tokenId, $userId);

            if (!$resetRow) {
                unset($_SESSION['reset_user_id'], $_SESSION['reset_token_id'], $_SESSION['reset_email']);
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Your reset session has expired. Please start again.']);
                exit;
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $this->userModel->updatePassword($userId, $hash);

            // Burn every outstanding reset token for this user, not just this one 
            $this->userModel->invalidateAllResetTokens($userId);

            $user = $this->userModel->getUserById($userId); 
            if ($user) {
                $this->notificationService->sendEmail(
                    $user['email'],
                    'Your password was changed',
                    "Hello {$user['full_name']},<br><br>Your password was just reset. If this wasn't you, please contact support immediately."
                );
            }

            // Clear reset flow session state now that the reset is complete.
            unset($_SESSION['reset_user_id'], $_SESSION['reset_token_id'], $_SESSION['reset_email']);

            // Regenerate the session ID. 
            session_regenerate_id(true);

            echo json_encode(['success' => true, 'message' => 'Your password has been reset. Please log in.']);
            exit;
        } catch (\PDOException $e) {
            error_log('Password reset failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
            exit;
        }
    }
}
