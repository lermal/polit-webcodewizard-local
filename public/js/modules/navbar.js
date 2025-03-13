export default class Navbar {
    constructor() {
        this.navbar = document.getElementById('navbar');
        this.mainNav = document.getElementById('mainNav');
        this.mobileMenuButton = document.querySelector('.mobile-menu-button');
        this.mainContent = document.querySelector('.main');
        this.isHomePage = this.mainContent.classList.contains('home-page'); // Проверяем, главная ли это страница

        this.init();
    }

    init() {
        // Инициализация обработчиков событий
        this.handleMobileMenu();
        this.handleScroll();

        // Обновляем padding только если это не главная страница
        if (!this.isHomePage) {
            this.updateMainContentPadding();

            window.addEventListener('resize', () => {
                this.updateMainContentPadding();
            });

            window.addEventListener('load', () => {
                this.updateMainContentPadding();
            });
        }
    }

    handleMobileMenu() {
        this.mobileMenuButton?.addEventListener('click', () => {
            this.mainNav.classList.toggle('active');
            this.mobileMenuButton.classList.toggle('active');
            // Обновляем padding при открытии/закрытии мобильного меню только если это не главная
            if (!this.isHomePage) {
                this.updateMainContentPadding();
            }
        });
    }

    handleScroll() {
        window.addEventListener('scroll', () => {
            if (this.navbar.classList.contains('navbar-transparent')) {
                if (window.scrollY > 1) {
                    this.navbar.classList.add('navbar-scrolled');
                } else {
                    this.navbar.classList.remove('navbar-scrolled');
                }
            }
        });
    }

    updateMainContentPadding() {
        if (this.navbar && this.mainContent && !this.isHomePage) {
            // Получаем актуальную высоту навбара
            const navbarHeight = this.navbar.getBoundingClientRect().height;
            // Устанавливаем padding для основного контента
            this.mainContent.style.paddingTop = `${navbarHeight}px`;
        }
    }
}
