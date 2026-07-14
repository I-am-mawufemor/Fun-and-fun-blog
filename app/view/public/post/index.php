<?php
/**
 * @file index.php
 * @brief Displays a list of all posts.
 * @var array $posts An array of posts to display.
 */
$page_title = "Posts | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";
?>

<div class="main">
    <?php foreach( $posts as $post ): ?>
        <div class="post">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <p><?php echo htmlspecialchars($post['content']); ?></p>
            <p>Author: <?php echo htmlspecialchars($post['author_name']); ?></p>
            <p>Category: <?php echo htmlspecialchars($post['category_name']); ?></p>
            <p>Created at: <?php echo htmlspecialchars($post['created_at']); ?></p>
            <p>Updated at: <?php echo htmlspecialchars($post['updated_at']); ?></p>
        </div>
    <?php endforeach; ?>
</div>