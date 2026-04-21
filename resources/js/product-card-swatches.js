(() => {
    const onClick = (e) => {
        const btn = e.target?.closest?.('[data-fc-swatch]');
        if (!btn) return;

        const card = btn.closest('.fc-product-card');
        if (!card) return;

        const img = card.querySelector('.fc-media img');
        if (!img) return;

        const next = btn.getAttribute('data-image') || '';
        if (!next) return;

        e.preventDefault();

        img.style.opacity = '0.2';
        img.src = next;
        setTimeout(() => (img.style.opacity = '1'), 80);

        card.querySelectorAll('[data-fc-swatch]').forEach((b) => b.classList.remove('is-active'));
        btn.classList.add('is-active');
    };

    document.addEventListener('click', onClick, { passive: false });
})();

