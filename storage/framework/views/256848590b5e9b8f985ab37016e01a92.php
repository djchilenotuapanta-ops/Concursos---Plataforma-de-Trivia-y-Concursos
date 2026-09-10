<?php
  /**
   * Parcial: alerts.blade.php
   *
   * Objetivo:
   * - Mostrar mensajes flash (success/error/warning/info) de forma consistente.
   * - Evitar que se repitan (usa SOLO este parcial en las vistas).
   * - Evitar XSS: usamos {{ }} en lugar de {!! !!}.
   */
?>

<?php if(session('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if(session('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if(session('warning')): ?>
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <?php echo e(session('warning')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if(session('info')): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <?php echo e(session('info')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<?php if($errors->any()): ?>
  <div class="alert alert-danger" role="alert">
    <strong>Revisa lo siguiente:</strong>
    <ul class="mb-0">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($e); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\concursos\resources\views/partials/alerts.blade.php ENDPATH**/ ?>