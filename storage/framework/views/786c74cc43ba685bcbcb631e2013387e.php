

<?php $__env->startSection('content'); ?>
<div class="container py-3">
  <h3 class="mb-3">Detalle del Concurso</h3>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-md-6">
          <strong>Título:</strong> <?php echo e($contest->title); ?><br>
          <strong>Empresa:</strong> <?php echo e($contest->company?->company_name ?? $contest->company?->name); ?><br>
          <strong>Tipo:</strong> <?php echo e(strtoupper($contest->type)); ?><br>
          <strong>Estado:</strong> <?php echo e(ucfirst($contest->status)); ?><br>
          <strong>Fin:</strong> <?php echo e($contest->end_at); ?>

        </div>
        <div class="col-md-6">
          <strong>Premio:</strong> <?php echo e($contest->prizes->first()?->name ?? '—'); ?><br>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">Participantes</div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Puntaje</th>
              <th>Tiempo (s)</th>
              <th>Tipo Ganador</th>
            </tr>
          </thead>
          <tbody>
            <?php
              // Ordenar igual que el backend: primero por score descendente, luego por tiempo ascendente
              $ganador = $participants->filter(function($p) {
                return $p->score > 0;
              })->sort(function($a, $b) {
                if ($b->score !== $a->score) {
                  return $b->score <=> $a->score;
                }
                // Si empatan en score, gana el de menor tiempo
                if ($a->seconds === null && $b->seconds === null) return 0;
                if ($a->seconds === null) return 1;
                if ($b->seconds === null) return -1;
                return $a->seconds <=> $b->seconds;
              })->first();
            ?>
            <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr <?php if($ganador && $ganador->user->id === $p->user->id): ?> style="background:#d4edda;font-weight:bold;" <?php endif; ?>>
                <td><?php echo e($i+1); ?></td>
                <td><?php echo e($p->user->name); ?></td>
                <td><?php echo e($p->user->email); ?></td>
                <td><?php echo e($p->score); ?></td>
                <td><?php echo e($p->seconds); ?></td>
                <td>
                  <?php if($ganador && $ganador->user->id === $p->user->id): ?>
                    Ganador
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php if($contest->status === 'ended' && !$contest->winner_published_at && $ganador): ?>
    <div class="alert alert-info mb-3">
      <strong>Ganador:</strong> <?php echo e($ganador->user->name); ?> (<?php echo e($ganador->user->email); ?>)<br>
      <strong>Puntaje:</strong> <?php echo e($ganador->score); ?><br>
      <strong>Tiempo:</strong> <?php echo e($ganador->seconds); ?> s
    </div>
    <form method="POST" action="<?php echo e(route('admin.contests.winners.publish_manual', $contest)); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-success">Publicar ganador</button>
    </form>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/admin/contests_adv/show.blade.php ENDPATH**/ ?>