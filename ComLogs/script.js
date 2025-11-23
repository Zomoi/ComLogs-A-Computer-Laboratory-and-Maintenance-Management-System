document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.sidebar nav ul li');
  const pages = document.querySelectorAll('.page');

  navItems.forEach(item => {
    item.addEventListener('click', () => {
      navItems.forEach(i => i.classList.remove('active'));
      pages.forEach(page => page.classList.remove('active'));

      item.classList.add('active');
      const target = item.getAttribute('data-target'); // ← uses data-target directly
      const targetPage = document.querySelector(`[data-page="${target}"]`);
      if (targetPage) targetPage.classList.add('active');
    });
  });
});
