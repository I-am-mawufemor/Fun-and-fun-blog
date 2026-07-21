<?php
/** @var array $getAllPosts */
if (!defined('ROOT')) {
    die("Direct access not allowed");
}

$page_title = "Posts | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";
generateCSRF();
?>

<section id="main">
    <div class="layout-container">
        <div class="layout">
            <div class="main-layout">
                <div class="layout-card">
                    <div class="layout-content">
                        <div class="layout-header">
                            <h2>Posts</h2>
                        </div>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($_SESSION['success']);
                                unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-error">
                                <?= htmlspecialchars($_SESSION['error']);
                                unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getAllPosts as $_POST): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($_POST['id']); ?></td>
                                        <td><?= htmlspecialchars($_POST['title']); ?></td>
                                        <td><?= htmlspecialchars($_POST['author_name']); ?></td>
                                         <td><?= htmlspecialchars($_POST['status']); ?></td>
                                        <td><?= date('F j, Y', strtotime($_POST['created_at'])); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?page=edit-post&id=<?= htmlspecialchars($_POST['id']); ?>" class="btn btn-edit">Edit</a>

                                                <button
                                                    type="button"
                                                    class="btn btn-delete"
                                                    onclick="openDeleteModal('<?= htmlspecialchars($_POST['id']); ?>', '<?= htmlspecialchars($_POST['title'], ENT_QUOTES); ?>')">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="side-layout">
                <div class="layout-card">
                    <div class="layout-content">
                        <h3>Post Management</h3>
                        <p>Here you can manage your posts. You can create new posts, edit existing ones, or delete them as needed.</p>
                        <a href="?page=create-post" class="btn">Create New Post</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Single shared delete modal (outside the loop) -->
<div class="modal-overlay" id="deleteModalOverlay">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button type="button" class="modal-close" onclick="closeModal('deleteModalOverlay')">✕</button>
        </div>

        <form id="deleteCategoryForm" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="modal-body">
                <p class="delete-msg">
                    Are you sure you want to delete <strong id="delete_category_name"></strong>?
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('deleteModalOverlay')">Cancel</button>
                <button type="submit" class="btn-confirm-delete">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteModal(id, name) {
        const form = document.getElementById('deleteCategoryForm');
        form.action = '?page=delete-category&id=' + encodeURIComponent(id);
        document.getElementById('delete_category_name').textContent = name;
        document.getElementById('deleteModalOverlay').classList.add('active');
    }

    function closeModal(overlayId) {
        document.getElementById(overlayId).classList.remove('active');
    }
</script>

