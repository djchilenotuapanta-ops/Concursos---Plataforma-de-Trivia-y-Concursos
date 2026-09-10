<?php $__env->startSection('title','Resultado Trivia'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h3 class="mb-0">Resultado: <?php echo e($contest->title); ?></h3>
    \1
    <?php if(isset($duration_seconds)): ?>
      <div class=\"text-muted\">Tiempo total: <b><?php echo e((int)$duration_seconds); ?></b> segundos</div>
    <?php endif; ?>
  </div>
  <div class="d-flex gap-2">
    <a href="<?php echo e(route('user.contests.list')); ?>" class="btn btn-outline-primary">Ver concursos</a>
    <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-outline-secondary btn-sm btn-nav">Volver al panel</a>
  </div>
</div>

<?php if(isset($leader) && $leader && $leader->user): ?>
<div class="alert alert-info">
  🏆 <b>Primer lugar actual:</b> <?php echo e($leader->user->name ?? $leader->user->email); ?> · Aciertos: <?php echo e((int)$leader->last_correct); ?>/<?php echo e((int)$total); ?> <?php if($leader->last_duration_seconds !== null): ?> · Tiempo: <?php echo e((int)$leader->last_duration_seconds); ?>s <?php endif; ?>
</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if($total === 0): ?>
      <div class="alert alert-warning mb-0">Esta trivia no tiene preguntas registradas.</div>
    <?php else: ?>
      <?php
        $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
      ?>

      <div class="mb-3">
        <div class="mb-2">Tu puntaje: <b><?php echo e($percent); ?>%</b></div>
        <div class="progress" role="progressbar" aria-valuenow="<?php echo e($percent); ?>" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar" style="width: <?php echo e($percent); ?>%"></div>
        </div>
      </div>

      <div class="table-responsive mb-3">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Total preguntas</th>
              <th class="text-success">Correctas</th>
              <th class="text-danger">Incorrectas</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><b><?php echo e($total); ?></b></td>
              <td class="text-success"><b><?php echo e($correct); ?></b></td>
              <td class="text-danger"><b><?php echo e($wrong); ?></b></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-3 text-muted small">
        * Nota: que ganes o no un premio depende de la lógica del admin/empresa (por ejemplo, mayor puntaje, sorteo, etc.).
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/trivia/result.blade.php ENDPATH**/ ?>