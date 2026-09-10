<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'Concurso1')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(route('home')); ?>"><?php echo e(config('app.name', 'Concurso1')); ?></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">

            <ul class="navbar-nav me-auto">

                <?php if(auth()->guard()->check()): ?>

                    <?php if(in_array(auth()->user()->role, ['company'])): ?>
                        <?php
                            $u = auth()->user();
                            $companyApproved = ($u->company_status ?? null) === 'approved';
                            $canManageContests = $companyApproved;
                        ?>

                        <?php if(Route::has('company.dashboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('company.dashboard')); ?>">Panel empresa</a>
                            </li>
                        <?php endif; ?>

                        <?php if($canManageContests): ?>
                            <?php if(Route::has('company.contests.index')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('company.contests.index')); ?>">Mis concursos</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(auth()->user()->role === 'admin'): ?>
                        <?php if(Route::has('admin.companies.index')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo e(route('admin.companies.index')); ?>">Empresas</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </ul>

            <div class="d-flex gap-2">
                <?php if(auth()->guard()->check()): ?>
                    <a class="btn btn-outline-light btn-sm" href="<?php echo e(route('dashboard')); ?>">Dashboard</a>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-danger btn-sm" type="submit">Salir</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="<?php echo e(route('login')); ?>">Login</a>
                    <a class="btn btn-warning btn-sm" href="<?php echo e(route('register')); ?>">Registro</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<main class="container pb-5">


    <?php echo $__env->yieldContent('content'); ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\concursos\resources\views/layouts/app.blade.php ENDPATH**/ ?>