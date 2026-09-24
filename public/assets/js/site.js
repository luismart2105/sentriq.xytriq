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

document.querySelectorAll('a[href*="wa.me/"]').forEach((link) => {
    link.addEventListener('click', () => {
        const parts = window.location.pathname.split('/').filter(Boolean);
        const service = parts[0] === 'servicios' && parts[1] ? parts[1] : '';
        const params = new URLSearchParams(window.location.search);
        const data = new FormData();
        data.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        data.append('page', window.location.pathname.slice(0, 120) || '/');
        if (service) data.append('service', service);
        ['utm_source', 'utm_medium', 'utm_campaign'].forEach((key) => {
            const value = params.get(key) || sessionStorage.getItem(`sentriq_${key}`);
            if (value) data.append(key, value.slice(0, 160));
        });
        navigator.sendBeacon?.('/eventos/whatsapp', data);
    });
});

['utm_source', 'utm_medium', 'utm_campaign'].forEach((key) => {
    const value = new URLSearchParams(window.location.search).get(key);
    if (value) sessionStorage.setItem(`sentriq_${key}`, value.slice(0, 160));
});
