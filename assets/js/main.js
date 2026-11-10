document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.menu-toggle');
    const navigation = document.getElementById('mobile-nav');
    if (toggle && navigation) {
        const closeMenu = () => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Buka navigasi');
            navigation.hidden = true;
        };
        toggle.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Tutup navigasi' : 'Buka navigasi');
            navigation.hidden = !open;
        });
        document.addEventListener('keydown', event => { if (event.key === 'Escape') { closeMenu(); toggle.focus(); } });
        window.matchMedia('(min-width: 769px)').addEventListener('change', event => { if (event.matches) closeMenu(); });
    }
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });
    document.querySelectorAll('input[type="file"][accept]').forEach(input => {
        input.addEventListener('change', () => {
            const file = input.files[0];
            input.setCustomValidity(file && file.size > 2 * 1024 * 1024 ? 'Ukuran gambar maksimal 2 MB.' : '');
            input.reportValidity();
        });
    });
});
