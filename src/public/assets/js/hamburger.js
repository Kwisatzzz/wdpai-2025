document.addEventListener('DOMContentLoaded', function () {
  const hamburger = document.getElementById('hamburger');
  const menuPanel = document.getElementById('menu-panel');
  const overlay = document.getElementById('menu-overlay');

  hamburger.addEventListener('click', function () {
    menuPanel.classList.toggle('active');
    overlay.classList.toggle('active');
  });

  overlay.addEventListener('click', function () {
    menuPanel.classList.remove('active');
    overlay.classList.remove('active');
  });
});
