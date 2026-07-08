<?php
$page_title = "Create Category | " . APP_NAME;
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
                        <form action="?page=store-category" method="POST" class="layout-form" id="categoryForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                            <h2 class="layout-title">Create Category</h2>

                            <div id="feedback" class="alert"></div>

                            <div class="layout-form-group">
                                <label for="category_name">Category Name</label>
                                <input
                                    type="text"
                                    id="category_name"
                                    name="category_name"
                                    placeholder="Enter category name"
                                    required>
                            </div>

                            <button type="submit" name="save" class="login-btn" id="submitBtn">
                                Create Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="side-layout">
                <div class="layout-card">
                    <div class="layout-content">
                        <h3>Category Tips</h3>
                        <p>Choose a clear, descriptive name. Duplicate category names (case-insensitive) aren't allowed, so check the list first if you're unsure.</p>
                        <a href="?page=categories" class="btn">Back to Categories</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>>
</html>


