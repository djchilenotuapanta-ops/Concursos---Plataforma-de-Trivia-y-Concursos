<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-shrink" id="mainNav">
    <div class="container">
        <a class="navbar-brand ms-0" href="<?php echo e(route('home')); ?>" style="padding: 0;">
            <img src="<?php echo e(asset('frontend/assets/img/navbar-logo.jpg')); ?>"
                 alt="Logo"
                 style="height: 90px; width: 90px; object-fit: cover; border-radius: 50%;
                        border: 2px solid #ffc800; margin-left: -15px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarResponsive">
            Menú <i class="fas fa-bars ms-1"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">

                <li class="nav-item"><a class="nav-link" href="#services">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="#premios">Premios entregados</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Sobre Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#ganadores">Ganadores</a></li>
                <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contacto</a></li>

                <?php if(auth()->guard()->guest()): ?>
                    <li class="nav-item d-none d-lg-block"><span class="nav-link">|</span></li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('login')); ?>" style="color:#ffc800;">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-4 py-2 ms-lg-2"
                           href="<?php echo e(route('register')); ?>" style="border-radius:50px;">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item d-none d-lg-block"><span class="nav-link">|</span></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown"
                           role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i> <?php echo e(Auth::user()->name); ?>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo e(url('/dashboard')); ?>">
                                    <i class="fas fa-tachometer-alt"></i> Mi Panel
                                </a>
                            </li>

                            <li>
                                <?php ($unread = auth()->user()?->unreadNotifications?->count() ?? 0); ?>
                                <a class="dropdown-item" href="<?php echo e(url('/notifications')); ?>">
                                    <i class="fas fa-bell"></i> Notificaciones
                                    <?php if($unread > 0): ?>
                                        <span class="badge bg-danger ms-2"><?php echo e($unread); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="<?php echo e(url('/logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\laragon\www\concursos\resources\views/partials/menu.blade.php ENDPATH**/ ?>