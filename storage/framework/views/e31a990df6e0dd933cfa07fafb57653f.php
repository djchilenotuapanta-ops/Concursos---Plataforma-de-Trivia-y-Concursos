

<?php $__env->startSection('title','Mis resultados'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h3 class="mb-0">Mis resultados</h3>
    <div class="text-muted">Premios que te hayan asignado (cuando el admin publique ganadores).</div>
  </div>
  <div class="d-flex gap-2">
    <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-outline-secondary btn-sm btn-nav">← Volver al panel</a>
    <a href="<?php echo e(route('user.contests.list')); ?>" class="btn btn-outline-primary">Ver concursos</a>
  </div>
</div>

<?php if(!isset($prizes) || !$prizes->count()): ?>
  <div class="alert alert-warning">Aún no tienes premios registrados.</div>
<?php else: ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Concurso</th>
              <th>Premio</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th class="text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $prizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td>
                  <div class="fw-semibold"><?php echo e($p->contest->title ?? '—'); ?></div>
                  <div class="text-muted small">Tipo: <?php echo e(($p->contest->type ?? '') === 'trivia' ? 'Trivia' : 'Trivia'); ?></div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <?php if($p->image_path): ?>
                      <img src="<?php echo e(asset('storage/' . $p->image_path)); ?>" alt="premio" style="width:48px;height:48px;object-fit:cover" class="rounded border">
                    <?php else: ?>
                      <div class="rounded border bg-light" style="width:48px;height:48px"></div>
                    <?php endif; ?>
                    <div>
                      <div class="fw-semibold"><?php echo e($p->name ?? ('Premio #' . $p->id)); ?></div>
                      <?php if($p->position): ?>
                        <div class="text-muted small">Lugar: 
                          <?php if((int)$p->position === 1): ?> 🥇 1er
                          <?php elseif((int)$p->position === 2): ?> 🥈 2do
                          <?php elseif((int)$p->position === 3): ?> 🥉 3er
                          <?php else: ?> #<?php echo e($p->position); ?>

                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                      <div class="text-muted small">Cantidad: <?php echo e($p->quantity); ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if($p->delivered_at): ?>
                    <span class="badge bg-success">Entregado</span>
                  <?php elseif($p->claimed_at): ?>
                    <span class="badge bg-primary">Reclamado</span>
                  <?php else: ?>
                    <span class="badge bg-warning text-dark">Pendiente</span>
                  <?php endif; ?>
                  <div class="text-muted small mt-1">
                    <?php if($p->claimed_at): ?> Reclamado: <?php echo e($p->claimed_at->format('Y-m-d H:i')); ?> <?php endif; ?>
                    <?php if($p->delivered_at): ?> <br>Entregado: <?php echo e($p->delivered_at->format('Y-m-d H:i')); ?> <?php endif; ?>
                  </div>
                </td>
                <td class="small text-muted"><?php echo e(optional($p->won_at ?? $p->created_at)->format('Y-m-d H:i')); ?></td>
                <td class="text-end">
                  <?php if(!$p->claimed_at): ?>
                    <form method="POST" action="<?php echo e(route('user.prizes.claim', $p)); ?>">
                      <?php echo csrf_field(); ?>
                      <button class="btn btn-sm btn-primary" type="submit">Reclamar</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/user/results.blade.php ENDPATH**/ ?>