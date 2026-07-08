<?php

namespace Mawufemor\Techandfun\Controller;

use Mawufemor\Techandfun\model\User;

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

use PDO;

class UserController
{
    private User $userModel;
    public function __construct(private PDO $pdo)
    {
        $this->userModel = new User($this->pdo);
    }
    public function register(): void
    {
        if (isLoggedIn()) {
            header('Location: ?page=home');
            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            validateCSRF($_POST['csrf_token'] ?? '');

            $fullName        = trim($_POST['full_name']        ?? '');
            $email           = trim($_POST['email']            ?? '');
            $telephone       = trim($_POST['telephone']        ?? '');
            $gender          = trim($_POST['gender']           ?? '');
            $password        =      $_POST['password']         ?? '';
            $confirmPassword =      $_POST['confirm_password'] ?? '';

            $allowedGenders = ['male', 'female', 'other'];

            if (empty($fullName)) {
                $error = "Full name is required.";
            } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "A valid email address is required.";
            } elseif (empty($telephone)) {
                $error = "Telephone is required.";
            } elseif (!in_array($gender, $allowedGenders, true)) {
                $error = "Please select a valid gender.";
            } elseif (strlen($password) < 8) {
                $error = "Password must be at least 8 characters.";
            } elseif ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
            } else {
                $existing = $this->userModel->getUser($email);

                if ($existing !== null) {
                    $error = "An account with that email already exists.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $created = $this->userModel->createUser(
                        $fullName,
                        $email,
                        $telephone,
                        $gender,
                        $hash
                    );

                    if ($created) {
                        header('Location: ?page=login');
                        exit();
                    }

                    $error = "Could not create account. Please try again.";
                }
            }
        }

        require ROOT . '/app/view/public/register.php';
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            header('Location: ?page=home');
            exit();
        }

        $error = '';
        // validate CSRF token

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            validateCSRF($_POST['csrf_token'] ?? '');

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Please fill in all fields.";
            } else {
                $user = $this->userModel->getUser($email);
                // var_dump($user); die();

                if ($user !== null && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role']      = $user['role'];

                    $redirect = $user['role'] === 'admin' ? 'admin-dashboard' : 'home';

                    header('Location: ?page=' . $redirect);
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            }
        }

        require ROOT . '/app/view/public/login.php';
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: ?page=login");
        exit();
    }
}
