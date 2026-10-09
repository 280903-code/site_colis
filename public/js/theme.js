(function () {
  const savedTheme = localStorage.getItem('theme') === 'dark' ? 'dark' : 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeButton(next);
  }

  function updateThemeButton(theme) {
    const button = document.getElementById('themeToggle');

    if (button) {
      const isDark = theme === 'dark';
      button.setAttribute('aria-pressed', String(isDark));
      button.setAttribute('aria-label', isDark ? 'Activer le mode clair' : 'Activer le mode sombre');
    }
  }

  updateThemeButton(savedTheme);
  document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
})();
