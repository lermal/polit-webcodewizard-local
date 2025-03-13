document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.button-toggle-menu');
    const leftSideMenu = document.querySelector('.leftside-menu');

    const overlay = document.createElement('div');
    overlay.className = 'menu-overlay';
    document.body.appendChild(overlay);

    menuToggle.addEventListener('click', function() {
        leftSideMenu.classList.toggle('show');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function() {
        leftSideMenu.classList.remove('show');
        overlay.classList.remove('show');
    });
});
