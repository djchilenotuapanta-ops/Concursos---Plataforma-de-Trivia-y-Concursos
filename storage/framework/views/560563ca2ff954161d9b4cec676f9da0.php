<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $__env->yieldContent('title','Panel'); ?> - <?php echo e(config('app.name','Concurso1')); ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="<?php echo e(asset('backend/assets/css/dashboard.css')); ?>" rel="stylesheet">

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

  <div class="dash-wrap">
    <?php echo $__env->make('partials.dashboard.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="dash-main">
      <?php echo $__env->make('partials.dashboard.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

      <main class="dash-content">
        <div class="container-fluid">
          <?php echo $__env->yieldContent('content'); ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <script src="<?php echo e(asset('backend/assets/js/dashboard.js')); ?>"></script>

  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\concursos\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>