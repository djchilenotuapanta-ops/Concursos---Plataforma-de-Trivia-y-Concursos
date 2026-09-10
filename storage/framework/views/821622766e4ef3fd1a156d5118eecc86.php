<footer class="footer py-4 bg-dark text-light">
    <?php
        /*
        |--------------------------------------------------------------------------
        | Enlaces de redes sociales (Front-End)
        |--------------------------------------------------------------------------
        | Importante:
        | - Dejamos el mismo estilo visual (clases Bootstrap existentes).
        | - Solo agregamos enlaces reales.
        | - Si más adelante quieres poner tus enlaces oficiales, puedes definir:
        |   APP_SOCIAL_TWITTER, APP_SOCIAL_FACEBOOK, APP_SOCIAL_LINKEDIN en .env
        |   y leerlos aquí con config('app....').
        */

        $twitter  = config('app.social_twitter', 'https://twitter.com/');
        $facebook = config('app.social_facebook', 'https://facebook.com/');
        $linkedin = config('app.social_linkedin', 'https://www.linkedin.com/');
    ?>
    <div class="container">
        <div class="row align-items-center">
            <!-- Derechos de autor -->
            <div class="col-lg-4 text-center text-lg-start mb-3 mb-lg-0">
                &copy; 2025 Sorteos y Concursos. Todos los derechos reservados.
            </div>

            <!-- Redes sociales -->
            <div class="col-lg-4 text-center mb-3 mb-lg-0">
                <a class="btn btn-dark btn-social mx-2" href="<?php echo e($twitter); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter de Sorteos y Concursos">
                    <i class="fab fa-twitter"></i>
                </a>
                <a class="btn btn-dark btn-social mx-2" href="<?php echo e($facebook); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook de Sorteos y Concursos">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="btn btn-dark btn-social mx-2" href="<?php echo e($linkedin); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de Sorteos y Concursos">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>

            <!-- Enlaces legales -->
            <div class="col-lg-4 text-center text-lg-end">
                <a class="link-light text-decoration-none me-3" href="#!">Política de Privacidad</a>
                <a class="link-light text-decoration-none" href="#!">Términos y Condiciones</a>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\laragon\www\concursos\resources\views/partials/footer.blade.php ENDPATH**/ ?>