<?php

/**
 * @file index.php
 * @brief Displays a list of all published posts.
 * @var array $posts An array of posts to display.
 * @var array $categories An array of categories for filtering.
 * @var string $activeCategory The currently selected category slug (if any).
 */

if (!defined('ROOT')) {
    die("Direct access not allowed");
}

$page_title = "Posts | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";

if (!function_exists('readingTime')) {
    function readingTime(string $html): int
    {
        $words = str_word_count(strip_tags($html));
        return max(1, (int) ceil($words / 200));
    }
}
?>

<body class="blog">

    <header class="blog-masthead blog-container">
        <p class="blog-eyebrow">TechAndFun / Journal</p>
        <h1 class="blog-title">Notes from the classroom, and the code behind it.</h1>
        <p class="blog-subtitle">
            Tutorials, course updates, and things we learned building TechAndFun —
            written by the people teaching and shipping it.
        </p>
    </header>

    <div class="blog-container">
        <nav class="blog-filters" aria-label="Filter by category">
            <a href="index.php?page=blog"
                class="blog-filter <?= $activeCategory === '' ? 'is-active' : '' ?>">All</a>
            <?php foreach ($categories as $category): ?>
                <a href="index.php?page=blog&category=<?= urlencode($category['slug']) ?>"
                    class="blog-filter <?= $activeCategory === $category['slug'] ? 'is-active' : '' ?>">
                    <?= htmlspecialchars($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if (empty($posts)): ?>
            <p class="blog-empty">No posts here yet — check back soon.</p>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): ?>
                    <a class="post-card" href="index.php?page=blog-post&slug=<?= urlencode($post['slug']) ?>">
                        <span class="post-card__tag"><?= htmlspecialchars($post['category_name'] ?? 'General') ?></span>
                        <h2 class="post-card__title"><?= htmlspecialchars($post['title']) ?></h2>
                        <p class="post-card__excerpt">
                            <?= htmlspecialchars(mb_strimwidth(strip_tags($post['content']), 0, 160, '…')) ?>
                        </p>
                        <div class="post-card__meta">
                            <span><?= htmlspecialchars($post['author_name'] ?? 'TechAndFun') ?></span>
                            <span><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                            <span><?= readingTime($post['content'] ?? '') ?> min read</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($pagination) && $pagination['total'] > 1): ?>
            <div class="blog-pagination">
                <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                    <?php if ($i === $pagination['current']): ?>
                        <span class="is-current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="index.php?page=blog&p=<?= $i ?><?= $activeCategory ? '&category=' . urlencode($activeCategory) : '' ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>