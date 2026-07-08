<?php

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

function generateCSRF(): void
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function validateCSRF(string $token): bool
{
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }

    $valid = hash_equals($_SESSION['csrf_token'], $token);

    // Regenerate token after each use to prevent replay attacks
    unset($_SESSION['csrf_token']);
    generateCSRF();

    return $valid;
}