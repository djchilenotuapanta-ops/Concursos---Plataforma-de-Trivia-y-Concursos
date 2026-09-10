<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Plataforma de concursos y trivias: participa, gana y revisa ganadores reales." />
        <meta name="author" content="" />
        <title><?php echo $__env->yieldContent('titulo','concursos'); ?></title>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('frontend/assets/favicon.ico')); ?>" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('frontend/assets/css/styles.css')); ?>" rel="stylesheet" />
    </head>
    <body id="page-top">

        <!-- Navigation menuuuuuuuuu-->
        <?php echo $__env->make('partials.menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

         <?php echo $__env->yieldContent('contenido'); ?>

        <!-- Footer-->
        <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('scripts'); ?>
    </body>
</html><?php /**PATH C:\laragon\www\concursos\resources\views/layouts/template.blade.php ENDPATH**/ ?>