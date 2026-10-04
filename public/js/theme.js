(function () {
  const $ = s => document.querySelector(s);
  const savedTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', savedTheme);

  function toggleTheme() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
  }

  window.toggleTheme = toggleTheme;

  // Add theme toggle button to header
  const header = document.querySelector('header .w');
  if (header) {
    const btn = document.createElement('button');
    btn.className = 'theme-toggle';
    btn.innerHTML = '🌓';
    btn.setAttribute('aria-label', 'Changer le thème');
    btn.onclick = toggleTheme;
    btn.style.cssText = 'background:none;border:none;font-size:1.2rem;cursor:pointer;padding:8px;margin-left:12px;';
    header.appendChild(btn);
  }
})();
