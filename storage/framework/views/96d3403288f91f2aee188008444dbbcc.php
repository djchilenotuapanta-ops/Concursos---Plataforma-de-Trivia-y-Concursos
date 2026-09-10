<?php $__env->startSection('content'); ?>
<div class="container py-3">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
      <h3 class="mb-1">Historial de concursos</h3>
      <div class="text-muted small">Aquí ves todos los concursos en los que participaste (sorteos y trivias).</div>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('user.contests.list')); ?>">Ver concursos</a>
      <a class="btn btn-outline-primary" href="<?php echo e(route('user.results')); ?>">Mis resultados</a>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th class="text-nowrap">Fecha</th>
              <th>Concurso</th>
              <th>Tipo</th>
              <th>Estado</th>
              <th class="text-end">Tickets</th>
              <th>Resultado</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $participations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $c = $p->contest;
                $won = $c ? (($wonByContest[$c->id] ?? collect())->count() > 0) : false;
                $winnerPrize = $c ? (($winnerByContest[$c->id] ?? collect())->first()): null;
                $winnerUser = $winnerPrize?->winner;
                $endingSoon = false;
                if ($c?->end_at && ($c->status ?? null) === 'active') {
                  $minutesLeft = now()->diffInMinutes(\Carbon\Carbon::parse($c->end_at), false);
                  $endingSoon = ($minutesLeft >= 0 && $minutesLeft <= 15);
                }
              ?>
              <tr>
                <td class="text-nowrap">
                  <?php echo e($p->joined_at ? \Carbon\Carbon::parse($p->joined_at)->format('Y-m-d H:i') : ($p->created_at?->format('Y-m-d H:i') ?? '—')); ?>

                </td>
                <td>
                  <div class="fw-semibold"><?php echo e($c->title ?? 'Concurso eliminado'); ?></div>
                  <?php if($c?->end_at): ?>
                    <div class="text-muted small">Fin: <?php echo e(\Carbon\Carbon::parse($c->end_at)->format('Y-m-d H:i')); ?></div>
                  <?php endif; ?>
                </td>
                <td class="text-capitalize"><?php echo e($c->type ?? '—'); ?></td>
                <td>
                  <?php
                    $now = \Carbon\Carbon::now();
                    $status = $c->status ?? 'draft';
                    $isExpired = $c->end_at && $now->greaterThan($c->end_at);
                    if ($status === 'active' && $isExpired) {
                      $status = 'ended';
                    }
                  ?>
                  <?php if($status === 'active'): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php elseif($status === 'ended' || $status === 'finished'): ?>
                    <span class="badge bg-secondary">Finalizado</span>
                  <?php else: ?>
                    <span class="badge bg-light text-dark border"><?php echo e($status ?? '—'); ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-end"><?php echo e($p->tickets ?? 1); ?></td>
                <td>
                  <?php
                    $isMerit = $p->winner_type === 'merit';
                    $isLottery = $p->winner_type === 'lottery';
                  ?>
                  <?php if($isMerit): ?>
                    <span class="badge bg-warning text-dark">Ganaste por mérito 🎉</span>
                  <?php elseif($isLottery): ?>
                    <span class="badge bg-info text-dark">Ganaste por sorteo 🎉</span>
                  <?php elseif($winnerUser): ?>
                    <span class="small">Ganador: <span class="fw-semibold"><?php echo e($winnerUser->name); ?></span></span>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>

                  <?php if($endingSoon): ?>
                    <div class="small text-danger mt-1">⏳ Cerca de finalizar</div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Aún no tienes participaciones registradas.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <?php echo e($participations->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/user/history.blade.php ENDPATH**/ ?>