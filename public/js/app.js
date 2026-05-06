/* EcoRide — JavaScript principal */

document.addEventListener('DOMContentLoaded', () => {

    // Toggle menu mobile
    const toggle = document.querySelector('.navbar-toggle');
    const nav    = document.querySelector('.navbar-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => nav.classList.toggle('open'));
    }

    // Auto-fermeture des messages flash après 5s
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Confirmation pour les boutons sensibles (data-confirm)
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // Date minimale = aujourd'hui pour les inputs date
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"][data-min-today]').forEach(input => {
        input.min = today;
    });

});
