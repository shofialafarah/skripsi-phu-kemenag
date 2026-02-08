document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('nav ul');

    // Toggle Mobile Menu
    mobileMenuToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        
        // Animasi hamburger menu
        this.classList.toggle('open');
        this.querySelectorAll('span').forEach((span, index) => {
            span.style.transition = '0.3s';
            if (this.classList.contains('open')) {
                if (index === 0) {
                    span.style.transform = 'rotate(45deg) translate(5px, 5px)';
                } else if (index === 1) {
                    span.style.opacity = '0';
                } else {
                    span.style.transform = 'rotate(-45deg) translate(5px, -5px)';
                }
            } else {
                span.style.transform = 'none';
                span.style.opacity = '1';
            }
        });
    });

    // Header Scroll Effect
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.style.padding = '10px 0';
            header.style.boxShadow = '0 8px 16px rgba(0,0,0,0.2)';
        } else {
            header.style.padding = '15px 0';
            header.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        }
    });

    // Dropdown Hover Effect
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('mouseenter', () => {
            dropdown.querySelector('.dropdown-content').style.display = 'block';
        });
        dropdown.addEventListener('mouseleave', () => {
            dropdown.querySelector('.dropdown-content').style.display = 'none';
        });
    });
});