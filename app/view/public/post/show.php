<?php
/**@var array $post */
/**@var array $relatedPosts */
$page_title = htmlspecialchars($post['title']) . " | " . APP_NAME;
require_once __DIR__ . "/../../../include/header.php";
require_once __DIR__ . "/../../../include/navbar.php";

$wordCount = str_word_count(strip_tags($post['content']));
$readingMinutes = max(1, (int) ceil($wordCount / 200));
?>

<body class="blog">

  <!-- Signature motif: reading progress, same visual language as the
       hover-fill on listing cards, now driven by scroll position. -->
  <div class="progress-rail"><div class="progress-rail__fill" id="readingProgress"></div></div>

  <article>
    <header class="post-header">
      <p class="post-header__tag"><?= htmlspecialchars($post['category_name'] ?? 'General') ?></p>
      <h1 class="post-header__title"><?= htmlspecialchars($post['title']) ?></h1>
      <div class="post-header__meta">
        <span><?= htmlspecialchars($post['author_name'] ?? 'TechAndFun') ?></span>
        <span><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
        <span><?= $readingMinutes ?> min read</span>
      </div>
    </header>

    <div class="post-body">
      <?= $post['content'] /* stored as sanitized HTML from the editor */ ?>
    </div>

    <footer class="post-footer">
      <a class="post-footer__back" href="index.php?page=blog">&larr; Back to all posts</a>
      <?php if (!empty($relatedPosts)): ?>
        <p style="font-family:var(--font-mono); font-size:12.5px; color:var(--color-slate); margin-top:18px;">
          Next up:
          <a href="index.php?page=blog&slug=<?= urlencode($relatedPosts[0]['slug']) ?>" style="color:var(--color-teal);">
            <?= htmlspecialchars($relatedPosts[0]['title']) ?>
          </a>
        </p>
      <?php endif; ?>
    </footer>
  </article>

  <script src="/assets/js/blog.js"></script>
</body>
</html>