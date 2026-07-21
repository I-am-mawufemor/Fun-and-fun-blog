// Reading progress bar — fills as the reader scrolls through the article.
// Mirrors the hover-fill signature on the blog listing cards.
(function () {
  const fill = document.getElementById('readingProgress');
  if (!fill) return;

  function update() {
    const doc = document.documentElement;
    const scrollable = doc.scrollHeight - doc.clientHeight;
    const progress = scrollable > 0 ? (doc.scrollTop / scrollable) * 100 : 0;
    fill.style.width = progress + '%';
  }

  document.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
  update();
})();
