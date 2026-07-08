<?php

use Mawufemor\Techandfun\model\User;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}


class AuthController
{
    public function __construct(private PDO $pdo) {}

    public function login(): void
    {
        if (isLoggedIn()) {
            header('Location: ?page=home');
            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password =      $_POST['password'] ?? '';

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email or password.";
            } else {
                $userModel = new User($this->pdo);   // pass DB, not email
                $user      = $userModel->getUser($email);

                if ($user !== null && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['email']     = $email;
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role']      = $user['role'];

                    $redirect = $user['role'] === 'admin' ? 'admin-dashboard' : 'home';
                    header("Location: ?page=$redirect");
                    exit();
                }

                // Same message whether email or password was wrong
                $error = "Invalid email or password.";
            }
        }

        // Renders on GET, and on POST when validation fails
        require ROOT . '/app/Views/login.php';
    }


    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header('Location: ?page=login');
        exit();
    }

public function verifyEmail(): void
{
    // This page is for users who are NOT logged in
    if (isLoggedIn()) {
        header('Location: ?page=home');
        exit();
    }

    $error   = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            $userModel = new User($this->pdo);  
            $user = $userModel->getUser($email);
            if ($user !== null) {
                // Generate a cryptographically secure random token
                $token   = bin2hex(random_bytes(32)); // 64 char hex string
                $expiry  = date('Y-m-d H:i:s', time() + 3600); // 1 hour

                $userModel->saveResetToken($user['id'], $token, $expiry);

                // Send the email — use your mailer of choice here
                $resetLink = "https://yoursite.com/?page=change-password&token=$token";
                mail(
                    $email,
                    "Password reset request",
                    "Click the link below to reset your password.\n\n$resetLink\n\nThis link expires in 1 hour."
                );
            }

            // Same message regardless — prevents email enumeration
            $success = true;
        }
    }

    require ROOT . '/app/Views/verifyEmail.php';
}


public function changePassword(): void
{
    // This page is for users who are NOT logged in — they have a token
    if (isLoggedIn()) {
        header('Location: ?page=home');
        exit();
    }

    // The token must be present in the URL
    $token = trim($_GET['token'] ?? '');

    if (empty($token)) {
        header('Location: ?page=login');
        exit();
    }

    // Validate the token before showing the form at all
    $userModel = new User($this->pdo);  
    $resetRecord = $userModel->getResetToken($token);

    if ($resetRecord === null) {
        // Token is invalid, expired, or already used
        $error = "This reset link is invalid or has expired. Please request a new one.";
        require ROOT . '/app/Views/changePassword.php';
        return;
    }

    $error   = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['new_password']     ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirm) {
            $error = "Passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $error = "Password must be at least 8 characters.";
        } else {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updated = $userModel->updatePassword($newHash, $resetRecord['user_id']);

            if ($updated) {
                // Invalidate the token so it cannot be used again
                $userModel->markTokenUsed($resetRecord['id']);
                $success = true;
            } else {
                $error = "Could not update password. Please try again.";
            }
        }
    }

    require ROOT . '/app/Views/changePassword.php';
}
    }
