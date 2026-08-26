const toggle = document.querySelector('[data-nav-toggle]');
const navigation = document.querySelector('[data-nav]');

if (toggle && navigation) {
    const closeNavigation = () => {
        toggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
        document.body.classList.remove('nav-open');
    };

    toggle.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', String(!isOpen));
        navigation.classList.toggle('is-open', !isOpen);
        document.body.classList.toggle('nav-open', !isOpen);
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeNavigation);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNavigation();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            closeNavigation();
        }
    });
}
