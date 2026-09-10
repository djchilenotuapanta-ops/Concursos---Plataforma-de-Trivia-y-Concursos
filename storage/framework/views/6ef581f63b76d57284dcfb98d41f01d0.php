<?php
  $user = auth()->user();
  $role = strtolower(trim((string) optional($user)->role));
  $role = match ($role) {
    'administrator','administrador','admin' => 'admin',
    'moderador','moderator' => 'moderator',
    'empresa','company' => 'company',
    default => $role,
  };

  $pageTitle = $pageTitle ?? match($role) {
    'admin' => 'Concursos (Admin)',
    'moderator' => 'Concursos (Moderador)',
    'company' => 'Mis concursos',
    default => 'Concursos',
  };

  $createRoute = $createRoute ?? match($role) {
    'admin' => (\Illuminate\Support\Facades\Route::has('admin.contests.create')
      ? route('admin.contests.create')
      : route('admin.contests_adv.create')),
    'moderator' => route('moderator.contests.create'),
    'company' => route('company.contests.create'),
    default => '#',
  };

  $areaPrefix = match($role) {
    'admin' => 'admin',
    'moderator' => 'moderator',
    'company' => 'company',
    default => 'admin',
  };
?>



<?php $__env->startSection('content'); ?>
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><?php echo e($pageTitle); ?></h3>
    <div class="d-flex gap-2">
      <a class="btn btn-primary" href="<?php echo e($createRoute); ?>">+ Crear</a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
      <table class="table table-sm align-middle table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Título</th>
            <th>Empresa</th>
            <th>Premio</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fin</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = ($contests ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($c->id); ?></td>
              <td>
                <div class="fw-semibold"><?php echo e($c->title ?? 'Sin título'); ?></div>
                <?php if(!empty($c->description)): ?>
                  <div class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($c->description, 80)); ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php
                  $companyLabel = $c->company?->company_name ?: ($c->company?->razon_social ?: $c->company?->name);
                ?>
                <span class="small"><?php echo e($companyLabel ?? '—'); ?></span>
              </td>
              <td>
                <?php
                  $p = $c->prizes->sortBy('position')->first();
                ?>
                <?php if($p): ?>
                  <div class="d-flex align-items-center gap-2">
                    <?php if($p->image_path): ?>
                      <img src="<?php echo e(asset('storage/' . $p->image_path)); ?>" alt="premio" style="width:44px;height:44px;object-fit:cover" class="rounded border">
                    <?php else: ?>
                      <div class="rounded border bg-light" style="width:44px;height:44px"></div>
                    <?php endif; ?>
                    <div>
                      <div class="fw-semibold small"><?php echo e($p->name); ?></div>
                      <div class="text-muted small">x<?php echo e($p->quantity); ?></div>
                    </div>
                  </div>
                <?php else: ?>
                  <span class="text-muted small">—</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-<?php echo e(($c->type ?? '') === 'trivia' ? 'info' : 'secondary'); ?>">
                  <?php echo e(strtoupper($c->type ?? '—')); ?>

                </span>
              </td>
              <td>
                <?php
                  $now = \Carbon\Carbon::now();
                  $status = $c->status ?? 'draft';
                  $isExpired = $c->end_at && $now->greaterThan($c->end_at);
                  if ($status === 'active' && $isExpired) {
                    $status = 'ended';
                  }
                  $statusLabel = match($status) {
                    'draft' => 'Borrador',
                    'active' => 'Activo',
                    'ended' => 'Finalizado',
                    'cancelled' => 'Cancelado',
                    default => 'Inactivo',
                  };
                  $badgeClass = match($status) {
                    'draft' => 'bg-secondary',
                    'active' => 'bg-success',
                    'ended' => 'bg-info',
                    'cancelled' => 'bg-danger',
                    default => 'bg-secondary',
                  };
                ?>
                <span class="badge <?php echo e($badgeClass); ?>">
                  <?php echo e($statusLabel); ?>

                </span>
              </td>
              <td><?php echo e(optional($c->end_at)->format('Y-m-d') ?? '—'); ?></td>
              <td class="text-end">
                <?php if(($c->type ?? '') === 'trivia'): ?>
                  <?php if($role === 'admin'): ?>
                    <a class="btn btn-sm btn-outline-info" href="<?php echo e(route('admin.contests_adv.show', $c)); ?>">
                      👥 Participantes
                    </a>
                  <?php endif; ?>
                  <a class="btn btn-sm btn-success" href="<?php echo e(route($areaPrefix.'.trivia.questions.index', $c)); ?>">
                    📋 Preguntas
                  </a>
                  <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route($areaPrefix.'.trivia.rules.edit', $c)); ?>">
                    ⚙️ Reglas/Premio
                  </a>
                  <a class="btn btn-sm btn-outline-success" href="<?php echo e(route($areaPrefix.'.trivia.questions.import.form', $c)); ?>">
                    📥 Importar
                  </a>

                  <?php if(($c->status ?? null) === 'draft'): ?>
                    <form method="POST" action="<?php echo e(route($areaPrefix.'.contests.publish', $c)); ?>" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('¿Publicar esta trivia? Se hará visible para los usuarios.');">
                        📣 Publicar
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php
                    $showPublishWinners = false;
                    if (($c->status ?? null) === 'ended') {
                      $showPublishWinners = true;
                    } elseif (($c->status ?? null) === 'active' && $isExpired) {
                      $showPublishWinners = true;
                    }
                  ?>
                  <?php if($showPublishWinners): ?>
                    <form method="POST" action="<?php echo e(route($areaPrefix.'.contests.winners.publish_manual', $c)); ?>" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('¿Publicar ganadores? Se notificará a los usuarios.');">
                        🏆 Publicar ganadores
                      </button>
                    </form>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">
                No hay concursos registrados.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/shared/contests/index.blade.php ENDPATH**/ ?>