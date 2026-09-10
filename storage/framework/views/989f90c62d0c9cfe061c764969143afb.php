

<?php $__env->startSection('content'); ?>
<div class="container py-3">
  <h3 class="mb-3">Notificaciones</h3>
  <div class="card">
    <div class="card-body">
      <?php ($notifs = $notifications ?? (auth()->user()?->notifications ?? collect())); ?>
      <?php $__empty_1 = true; $__currentLoopData = $notifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border rounded p-3 mb-2">
          <div class="d-flex justify-content-between">
            <div class="fw-bold"><?php echo e($n->data['title'] ?? 'Notificación'); ?></div>
            <div class="text-muted small"><?php echo e($n->created_at); ?></div>
          </div>
          <div><?php echo e($n->data['message'] ?? ''); ?></div>

          <?php if(!empty($n->data['url'])): ?>
            <a class="btn btn-sm btn-outline-primary mt-2" href="<?php echo e($n->data['url']); ?>">Ver</a>
          <?php endif; ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-muted">No tienes notificaciones todavía.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/notifications/index.blade.php ENDPATH**/ ?>