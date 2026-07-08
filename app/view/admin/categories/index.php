<?php

/** @var array $categories */
$page_title = "Categories | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";
?>

<section id="main">
    <div class="layout-container">
        <div class="layout">
            <div class="main-layout">
                <div class="layout-card">
                    <div class="layout-content">
                        <div class="layout-header">
                            <h2>Categories</h2>
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
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['id']); ?></td>
                                        <td><?= htmlspecialchars($category['name']); ?></td>
                                        <td><?= htmlspecialchars($category['slug']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="?page=edit-category&id=<?= $category['id']; ?>" class="btn btn-edit">Edit</a>
                                                <form action="?page=delete-category&id=<?= $category['id']; ?>" method="POST">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                                                </form>
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
                        <h3>Category Management</h3>
                        <p>Here you can manage your categories. You can create new categories, edit existing ones, or delete them as needed.</p>
                        <a href="?page=create-categories" class="btn">Create New Category</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>