<?php $__env->startSection('titulo', 'Aplicativo'); ?>

<?php $__env->startSection('contenido'); ?>

<!-- Masthead -->
<header class="masthead">
    <div class="container">
        <div class="masthead-subheading reveal-on-scroll">¡Bienvenido a Gana Fácil!</div>
        <div class="masthead-heading text-uppercase reveal-on-scroll">
            Participa en <span style="color: var(--gf-primary);">trivias</span> y concursos<br class="d-none d-lg-block">
            y gana premios reales
        </div>

        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center reveal-on-scroll">
            <a class="btn btn-primary btn-xl text-uppercase" href="<?php echo e(route('public.contests.index')); ?>">
                <i class="fas fa-bolt me-2"></i>Ver concursos
            </a>
            <?php if(auth()->guard()->guest()): ?>
                <a class="btn btn-outline-light btn-xl text-uppercase" href="<?php echo e(route('register')); ?>" style="border-radius:999px;">
                    <i class="fas fa-user-plus me-2"></i>Crear cuenta
                </a>
            <?php else: ?>
                <a class="btn btn-outline-light btn-xl text-uppercase" href="<?php echo e(route('user.contests.list')); ?>" style="border-radius:999px;">
                    <i class="fas fa-play me-2"></i>Ir a mis trivias
                </a>
            <?php endif; ?>
        </div>

        <!-- Stats -->
        <div class="row g-3 mt-5 justify-content-center">
            <div class="col-12 col-md-4 reveal-on-scroll">
                <div class="gf-card p-4 text-center h-100">
                    <div class="gf-stat" data-count-to="<?php echo e($deliveredPrizes->count()); ?>" data-count-dur="800">0</div>
                    <div class="gf-subtle">Premios entregados (vitrina)</div>
                </div>
            </div>
            <div class="col-12 col-md-4 reveal-on-scroll">
                <div class="gf-card p-4 text-center h-100">
                    <div class="gf-stat" data-count-to="1000" data-count-dur="900">0</div>
                    <div class="gf-subtle">Participaciones registradas</div>
                    <div class="small gf-subtle">(demo visual)</div>
                </div>
            </div>
            <div class="col-12 col-md-4 reveal-on-scroll">
                <div class="gf-card p-4 text-center h-100">
                    <div class="gf-stat" data-count-to="60" data-count-dur="900">0</div>
                    <div class="gf-subtle">Segundos para jugar una trivia</div>
                    <div class="small gf-subtle">(depende del concurso)</div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Services -->
<section class="page-section" id="services">
    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Cómo funciona</h2>
            <h3 class="section-subheading text-muted">En 3 pasos: elige un concurso, responde la trivia y revisa resultados.</h3>
        </div>

        <div class="row text-center">
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fas fa-circle fa-stack-2x text-primary"></i>
                    <i class="fas fa-gift fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="my-3">Concursos activos</h4>
                <p class="text-muted">Gana premios increíbles cada semana participando en nuestros sorteos exclusivos.</p>
            </div>

            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fas fa-circle fa-stack-2x text-primary"></i>
                    <i class="fas fa-users fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="my-3">Concursos Populares</h4>
                <p class="text-muted">Únete a los concursos más populares y aumenta tus oportunidades de ganar premios únicos.</p>
            </div>

            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                    <i class="fas fa-circle fa-stack-2x text-primary"></i>
                    <i class="fas fa-trophy fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="my-3">Premios Garantizados</h4>
                <p class="text-muted">Participa y asegúrate de recibir premios emocionantes de forma justa y divertida.</p>
            </div>
        </div>
    </div>
</section>

<!-- Premios entregados -->
<section class="page-section bg-light" id="premios">
    <span id="portafolio" style="position:relative; top:-90px;"></span>

    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Premios entregados</h2>
            <h3 class="section-subheading text-muted">Resultados reales. Transparencia para que participes con confianza.</h3>
        </div>

        <?php if($deliveredPrizes->isEmpty()): ?>
            <div class="gf-card p-4 text-center reveal-on-scroll">
                <div class="gf-badge mx-auto"><i class="fas fa-info-circle"></i> Aún no hay premios publicados</div>
                <p class="mt-3 mb-0 text-muted">Cuando se entreguen premios, aquí verás el historial.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php $__currentLoopData = $deliveredPrizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-sm-6 col-lg-4 reveal-on-scroll">
                        <div class="gf-card h-100 overflow-hidden">
                            <div class="position-relative">
                                <img src="<?php echo e($p->image_url); ?>" alt="Premio" class="img-fluid" style="width:100%; height:220px; object-fit:cover;">
                                <div class="position-absolute top-0 start-0 p-3">
                                    <span class="gf-badge">
                                        <i class="fas fa-trophy"></i>
                                        Entregado
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="mb-2"><?php echo e($p->name ?? 'Premio'); ?></h5>

                                <div class="d-flex flex-column gap-2 gf-subtle">
                                    <div>
                                        <i class="fas fa-gamepad me-2"></i>
                                        <span class="text-white-50">Concurso:</span>
                                        <span class="text-white"><?php echo e($p->contest?->title ?? '—'); ?></span>
                                    </div>
                                    <div>
                                        <i class="fas fa-user-check me-2"></i>
                                        <span class="text-white-50">Ganador:</span>
                                        <span class="text-white"><?php echo e($p->winner?->name ?? '—'); ?></span>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar-check me-2"></i>
                                        <span class="text-white-50">Fecha:</span>
                                        <span class="text-white">
                                            <?php echo e(\Illuminate\Support\Carbon::parse($p->delivered_at)->locale('es')->translatedFormat('d M Y')); ?>

                                        </span>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex gap-2">
                                    <a href="<?php echo e(route('public.contests.index')); ?>" class="btn btn-sm btn-primary" style="border-radius:999px;">
                                        <i class="fas fa-bolt me-1"></i>Participar
                                    </a>
                                    <a href="#contact" class="btn btn-sm btn-outline-light" style="border-radius:999px;">
                                        <i class="fas fa-envelope me-1"></i>Contacto
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="text-center mt-5 reveal-on-scroll">
                <a class="btn btn-outline-dark btn-lg" href="<?php echo e(route('public.contests.index')); ?>" style="border-radius:999px;">
                    <i class="fas fa-search me-2"></i>Explorar más concursos
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if(($deliveredPrizes ?? collect())->count() > 0): ?>
    <?php $__currentLoopData = ($deliveredPrizes ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="portfolio-modal modal fade" id="deliveredPrizeModal<?php echo e($p->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="close-modal" data-bs-dismiss="modal">
                        <img src="<?php echo e(asset('frontend/assets/img/close-icon.svg')); ?>" alt="Close modal" />
                    </div>

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="modal-body">
                                    <h2 class="text-uppercase"><?php echo e($p->name ?? ('Premio #' . $p->id)); ?></h2>
                                    <p class="item-intro text-muted"><?php echo e($p->contest->title ?? 'Concurso'); ?></p>

                                    <img class="img-fluid d-block mx-auto" src="<?php echo e($p->image_url); ?>" alt="Premio entregado" />

                                    <?php if($p->winner): ?>
                                        <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                                            <img
                                                src="<?php echo e($p->winner->avatar_url); ?>"
                                                alt="Ganador"
                                                style="width:44px;height:44px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;"
                                            >
                                            <div class="text-start">
                                                <div class="fw-semibold"><?php echo e($p->winner->name); ?></div>
                                                <div class="text-muted small"><?php echo e($p->winner->email); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <ul class="list-inline mt-3">
                                        <li><strong>Entregado:</strong> <?php echo e(optional($p->delivered_at)->format('Y-m-d H:i')); ?></li>
                                        <li><strong>Tipo:</strong> <?php echo e(($p->contest->type ?? '') === 'trivia' ? 'Trivia' : 'Trivia'); ?></li>
                                    </ul>

                                    <a class="btn btn-primary btn-xl text-uppercase mt-2" href="<?php echo e(route('login')); ?>">
                                        ¿Quieres participar? Inicia sesión
                                    </a>

                                    <button class="btn btn-secondary btn-xl text-uppercase ms-2" data-bs-dismiss="modal" type="button">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>

<!-- About -->
<section class="page-section" id="about">
    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Nuestra Historia</h2>
            <h3 class="section-subheading text-muted">Conoce cómo hemos crecido y evolucionado a lo largo de los años.</h3>
        </div>

        <ul class="timeline">
            <li>
                <div class="timeline-image">
                    <img class="rounded-circle img-fluid" src="<?php echo e(asset('frontend/assets/img/about/1.jpg')); ?>" alt="Nuestros Inicios" />
                </div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4>2009-2011</h4>
                        <h4 class="subheading">Nuestros humildes comienzos</h4>
                    </div>
                    <div class="timeline-body">
                        <p class="text-muted">Iniciamos como un pequeño equipo con grandes ideas...</p>
                    </div>
                </div>
            </li>

            <li class="timeline-inverted">
                <div class="timeline-image">
                    <img class="rounded-circle img-fluid" src="<?php echo e(asset('frontend/assets/img/about/2.jpg')); ?>" alt="Nacimiento de la Agencia" />
                </div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4>Marzo 2011</h4>
                        <h4 class="subheading">Nacimiento de la Agencia</h4>
                    </div>
                    <div class="timeline-body">
                        <p class="text-muted">Formalizamos nuestra agencia...</p>
                    </div>
                </div>
            </li>

            <li>
                <div class="timeline-image">
                    <img class="rounded-circle img-fluid" src="<?php echo e(asset('frontend/assets/img/about/3.jpg')); ?>" alt="Servicio Completo" />
                </div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4>Diciembre 2015</h4>
                        <h4 class="subheading">Transición a servicio completo</h4>
                    </div>
                    <div class="timeline-body">
                        <p class="text-muted">Ampliamos nuestra oferta de servicios...</p>
                    </div>
                </div>
            </li>

            <li class="timeline-inverted">
                <div class="timeline-image">
                    <img class="rounded-circle img-fluid" src="<?php echo e(asset('frontend/assets/img/about/4.jpg')); ?>" alt="Expansión" />
                </div>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4>Julio 2020</h4>
                        <h4 class="subheading">Fase de expansión</h4>
                    </div>
                    <div class="timeline-body">
                        <p class="text-muted">Gracias al crecimiento sostenido...</p>
                    </div>
                </div>
            </li>

            <li class="timeline-inverted">
                <div class="timeline-image">
                    <h4>¡Sé parte<br />de nuestra<br />historia!</h4>
                </div>
            </li>
        </ul>
    </div>
</section>

<!-- Ganadores de la Semana (por ahora estático) -->
<section class="page-section bg-light" id="ganadores">
    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Ganadores de la Semana</h2>
            <h3 class="section-subheading text-muted">
                Estos son los afortunados que participaron y ganaron en nuestros concursos y trivias semanales.
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="team-member">
                    <img class="mx-auto rounded-circle" src="<?php echo e(asset('frontend/assets/img/team/1.jpg')); ?>" alt="Ganador 1" />
                    <h4>Juan Pérez</h4>
                    <p class="text-muted">Concurso: Threads</p>
                    <p class="text-primary">Premio: Smartwatch</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="team-member">
                    <img class="mx-auto rounded-circle" src="<?php echo e(asset('frontend/assets/img/team/2.jpg')); ?>" alt="Ganador 2" />
                    <h4>María Gómez</h4>
                    <p class="text-muted">Concurso: Explore</p>
                    <p class="text-primary">Premio: Auriculares Inalámbricos</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="team-member">
                    <img class="mx-auto rounded-circle" src="<?php echo e(asset('frontend/assets/img/team/3.jpg')); ?>" alt="Ganador 3" />
                    <h4>Carlos Rodríguez</h4>
                    <p class="text-muted">Concurso: Finish</p>
                    <p class="text-primary">Premio: Tarjeta de regalo</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <p class="large text-muted">¡Felicidades a todos los ganadores! Participa la próxima semana y tú podrías ser uno de ellos.</p>
            </div>
        </div>
    </div>
</section>

<!-- Patrocinadores -->
<div class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 col-sm-6 my-3">
                <a href="#!"><img class="img-fluid img-brand d-block mx-auto" src="<?php echo e(asset('frontend/assets/img/logos/microsoft.svg')); ?>" alt="Microsoft Logo" /></a>
            </div>
            <div class="col-md-3 col-sm-6 my-3">
                <a href="#!"><img class="img-fluid img-brand d-block mx-auto" src="<?php echo e(asset('frontend/assets/img/logos/google.svg')); ?>" alt="Google Logo" /></a>
            </div>
            <div class="col-md-3 col-sm-6 my-3">
                <a href="#!"><img class="img-fluid img-brand d-block mx-auto" src="<?php echo e(asset('frontend/assets/img/logos/facebook.svg')); ?>" alt="Facebook Logo" /></a>
            </div>
            <div class="col-md-3 col-sm-6 my-3">
                <a href="#!"><img class="img-fluid img-brand d-block mx-auto" src="<?php echo e(asset('frontend/assets/img/logos/ibm.svg')); ?>" alt="IBM Logo" /></a>
            </div>
        </div>
    </div>
</div>


<!-- FAQ -->
<section class="page-section" id="faq">
    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Preguntas frecuentes</h2>
            <h3 class="section-subheading text-muted">Respuestas rápidas antes de participar.</h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9 reveal-on-scroll">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1" aria-expanded="true" aria-controls="a1">
                                ¿Cómo participo en una trivia?
                            </button>
                        </h2>
                        <div id="a1" class="accordion-collapse collapse show" aria-labelledby="q1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Entra a <b>Ver concursos</b>, elige una trivia, responde dentro del tiempo y envía tus respuestas.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2" aria-expanded="false" aria-controls="a2">
                                ¿Cómo se define el ganador?
                            </button>
                        </h2>
                        <div id="a2" class="accordion-collapse collapse" aria-labelledby="q2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Normalmente se considera <b>cantidad de aciertos</b> y, en caso de empate, el <b>menor tiempo</b>.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="q3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3" aria-expanded="false" aria-controls="a3">
                                ¿Los premios son reales?
                            </button>
                        </h2>
                        <div id="a3" class="accordion-collapse collapse" aria-labelledby="q3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Sí. En <b>Premios entregados</b> mostramos el historial público de entregas para mayor confianza.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section class="page-section" id="contact">
    <div class="container">
        <div class="text-center">
            <h2 class="section-heading text-uppercase">Contáctanos</h2>
            <h3 class="section-subheading text-muted">Estamos aquí para ayudarte. Envía tus consultas y nos pondremos en contacto contigo.</h3>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="contactForm" method="POST" action="<?php echo e(route('contact.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="row align-items-stretch mb-5">
                <div class="col-md-6">

                    <div class="form-group">
                        <input
                            class="form-control"
                            id="name"
                            type="text"
                            name="name"
                            placeholder="Tu Nombre *"
                            required
                            value="<?php echo e(old('name')); ?>"
                        >
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>

                    <div class="form-group">
                        <input
                            class="form-control"
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Tu Correo *"
                            required
                            value="<?php echo e(old('email')); ?>"
                        >
                        <div class="invalid-feedback">El correo es obligatorio y debe ser válido.</div>
                    </div>

                    <div class="form-group mb-md-0">
                        <input
                            class="form-control"
                            id="phone"
                            type="tel"
                            name="phone"
                            placeholder="Tu Teléfono *"
                            required
                            value="<?php echo e(old('phone')); ?>"
                        >
                        <div class="invalid-feedback">El teléfono es obligatorio.</div>
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="form-group mb-md-0">
                        <textarea
                            class="form-control"
                            id="message"
                            name="message"
                            placeholder="Tu Mensaje *"
                            required
                        ><?php echo e(old('message')); ?></textarea>
                        <div class="invalid-feedback">El mensaje es obligatorio.</div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button class="btn btn-primary btn-xl text-uppercase" id="submitButton" type="submit">
                    Enviar Mensaje
                </button>
            </div>
        </form>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('frontend/assets/js/scripts.js')); ?>"></script>
<script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
<script src="<?php echo e(asset('frontend/js/contact.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.template', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/app/front/index.blade.php ENDPATH**/ ?>