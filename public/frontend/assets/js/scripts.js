/*!
* Start Bootstrap - Agency v7.0.12 (https://startbootstrap.com/theme/agency)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-agency/blob/master/LICENSE)
*/
//
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Navbar shrink function
    var navbarShrink = function () {
        const navbarCollapsible = document.body.querySelector('#mainNav');
        if (!navbarCollapsible) {
            return;
        }
        if (window.scrollY === 0) {
            navbarCollapsible.classList.remove('navbar-shrink')
        } else {
            navbarCollapsible.classList.add('navbar-shrink')
        }

    };

    // Shrink the navbar 
    navbarShrink();

    // Shrink the navbar when page is scrolled
    document.addEventListener('scroll', navbarShrink);

    //  Activate Bootstrap scrollspy on the main nav element
    const mainNav = document.body.querySelector('#mainNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#mainNav',
            rootMargin: '0px 0px -40%',
        });
    };

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    const responsiveNavItems = [].slice.call(
        document.querySelectorAll('#navbarResponsive .nav-link')
    );
    responsiveNavItems.map(function (responsiveNavItem) {
        responsiveNavItem.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });

});

(function() {
    const form = document.getElementById('contactForm');
    const submitButton = document.getElementById('submitButton');
    const successMessage = document.getElementById('submitSuccessMessage');
    const errorMessage = document.getElementById('submitErrorMessage');

    form.addEventListener('submit', function(e) {
        // Si el formulario tiene action (Laravel), dejamos que envíe normalmente.
        // Solo aplicamos validación visual de Bootstrap.
        if (!form.checkValidity()) {
            e.preventDefault();
            form.classList.add('was-validated');
            return;
        }
        // Dejar que el submit siga su curso (POST a /contact)
        submitButton.disabled = true;
    });
})();


// --- Animaciones suaves y contadores (sin librerías extra) ---
window.addEventListener('DOMContentLoaded', () => {
    // Reveal on scroll
    const revealEls = document.querySelectorAll('.reveal-on-scroll');
    if ('IntersectionObserver' in window && revealEls.length) {
        const io = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(el => io.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('reveal-visible'));
    }

    // Contadores (data-count-to)
    const counters = document.querySelectorAll('[data-count-to]');
    if ('IntersectionObserver' in window && counters.length) {
        const cio = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const to = parseInt(el.getAttribute('data-count-to') || '0', 10);
                const dur = parseInt(el.getAttribute('data-count-dur') || '900', 10);
                const start = 0;
                const startTime = performance.now();
                const step = (now) => {
                    const t = Math.min(1, (now - startTime) / dur);
                    const val = Math.floor(start + (to - start) * (t));
                    el.textContent = val.toLocaleString('es-EC');
                    if (t < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
                obs.unobserve(el);
            });
        }, { threshold: 0.4 });
        counters.forEach(el => cio.observe(el));
    }
});
