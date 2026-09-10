

<?php $__env->startSection('title','Panel Usuario'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h3 class="mb-0">Panel de Usuario</h3>
    <div class="text-muted">Concursos activos y tus métricas de participación.</div>
  </div>

  <div class="d-flex gap-2">
    <a href="<?php echo e(route('user.contests.list')); ?>" class="btn btn-primary">Ver concursos</a>
    <a href="<?php echo e(route('user.results')); ?>" class="btn btn-outline-primary">Mis resultados</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Concursos activos</div>
        <div class="display-6"><?php echo e($activeContests ?? 0); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Mis participaciones</div>
        <div class="display-6"><?php echo e($myParticipations ?? 0); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">Premios ganados</div>
        <div class="display-6"><?php echo e($myPrizes ?? 0); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Mis estadísticas (gráfico de barras)</h5>
      <span class="text-muted small">Resumen rápido</span>
    </div>
    <div class="ratio ratio-21x9">
      <canvas id="userStatsBar" aria-label="Gráfico de barras de estadísticas" role="img"></canvas>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Últimos concursos activos</h5>
      <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('user.contests.list')); ?>">Ver todos</a>
    </div>

    <?php if(isset($latestContests) && $latestContests->count()): ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Concurso</th>
              <th>Tipo</th>
              <th>Empresa</th>
              <th>Vigencia</th>
              <th class="text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $latestContests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?php echo e($c->title); ?></div>
                  <div class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($c->description, 80)); ?></div>
                </td>
                <td>
                  <span class="badge bg-<?php echo e($c->type === 'trivia' ? 'info' : 'secondary'); ?>">
                    <?php echo e($c->type === 'trivia' ? 'Trivia' : 'Trivia'); ?>

                  </span>
                </td>
                <td><?php echo e($c->company->name ?? '—'); ?></td>
                <td class="small text-muted">
                  <?php echo e(optional($c->start_at)->format('Y-m-d H:i')); ?> → <?php echo e(optional($c->end_at)->format('Y-m-d H:i')); ?>

                </td>
                <td class="text-end">
                  <a href="<?php echo e(route('user.contests.list')); ?>" class="btn btn-sm btn-primary">Entrar</a>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-warning mb-0">No hay concursos activos en este momento.</div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script>
    window.__userBarChart = <?php echo json_encode($barChart ?? ['labels'=>[], 'values'=>[]], 512) ?>;
  </script>
  <script src="<?php echo e(asset('backend/assets/js/dashboard-charts.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/user/dashboard.blade.php ENDPATH**/ ?>