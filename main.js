document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('nav a');
    const currentPage = window.location.pathname.split('/').pop();

    links.forEach((link) => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.style.backgroundColor = '#153243';
            link.style.color = '#ffffff';
        }
    });

    const slider = document.querySelector('[data-slider]');
    if (slider) {
        const slides = slider.querySelectorAll('.slide');
        let activeIndex = 0;

        if (slides.length > 1) {
            window.setInterval(() => {
                slides[activeIndex].classList.remove('is-active');
                activeIndex = (activeIndex + 1) % slides.length;
                slides[activeIndex].classList.add('is-active');
            }, 3500);
        }
    }

    const navs = document.querySelectorAll('.top-nav, nav');
    navs.forEach((nav) => {
        const toggle = nav.querySelector('.menu-toggle');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('menu-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
});
