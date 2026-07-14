<?php

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
}

function requireRole(string $role): void
{
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        http_response_code(403);
        require ROOT . '/app/view/errors/403.php';
        exit;
    }
}