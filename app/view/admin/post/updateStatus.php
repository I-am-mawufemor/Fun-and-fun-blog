<?php
/** @var array $post */
if (!defined('ROOT')) {
    die("Direct access not allowed");
}

$page_title = "Update Post Status | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";
generateCSRF();
?>

<div class="table-actions">
    <?php if ($post['status'] !== 'published'): ?>
        <form action="?page=update-post-status" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="status" value="published">
            <button type="submit" class="btn btn-success">Publish</button>
        </form>
    <?php endif; ?>

    <?php if ($post['status'] !== 'archived'): ?>
        <form action="?page=update-post-status" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="status" value="archived">
            <button type="submit" class="btn btn-warning">Archive</button>
        </form>
    <?php endif; ?>

    <?php if ($post['status'] !== 'draft'): ?>
        <form action="?page=update-post-status" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <input type="hidden" name="status" value="draft">
            <button type="submit" class="btn btn-secondary">Revert to Draft</button>
        </form>
    <?php endif; ?>
</div>