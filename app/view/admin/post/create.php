<?php

/** @var array $categories **/
$page_title = "Create Post | " . APP_NAME;
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
                        <form action="?page=store-post" method="POST" class="layout-form" id="categoryForm">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                            <h2 class="layout-title">Create Post</h2>

                            <div id="feedback" class="alert"></div>

                            <div class="layout-form-group">
                                <label for="post_title">Post Title</label>
                                <input
                                    type="text"
                                    id="post_title"
                                    name="post_title"
                                    placeholder="Enter post title"
                                    required>
                            </div>

                            <div class="layout-form-group">
                                <label for="category_id">Category <span class="req">*</span></label>
                                <select id="category_id" name="category_id" required>
                                    <option value="" disabled <?= empty($_POST['category_id']) ? 'selected' : '' ?>>
                                        Select a category
                                    </option>
                                    <?php foreach ($categories as $category): ?>
                                        <option
                                            value="<?= htmlspecialchars($category['id']) ?>"
                                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="layout-form-group">
                                <label for="post_body">
                                    Body <span class="req">*</span>
                                </label>
                                <textarea
                                    id="post_body"
                                    name="body"
                                    rows="16"
                                    placeholder="Write your full post here…"
                                    maxlength="20000"
                                    required><?php echo htmlspecialchars($_POST['body'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <div class="char-row">
                                    <span class="field-hint">Max 20,000 characters</span>
                                    <span class="char-count" id="charCount">0 / 20,000</span>
                                </div>
                            </div>

                            <button type="submit" name="save" class="login-btn" id="submitBtn">
                                Create Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="side-layout">
                <div class="layout-card">
                    <div class="layout-content">
                        <h3>Post Tips</h3>
                        <p>Choose a clear, descriptive title. Make sure your post content is original and provides value to readers.</p>
                        <a href="#" class="btn">Back to Posts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.getElementById('post_body');
        const charCount = document.getElementById('charCount');
        const max = 20000;

        function updateCount() {
            const len = body.value.length;
            charCount.textContent = `${len} / ${max.toLocaleString()}`;
            charCount.style.color = len > max * 0.9 ? '#dc3545' : '';
        }

        body.addEventListener('input', updateCount);
        updateCount(); // run once on load in case of repopulated value after a failed submit
    });
</script>