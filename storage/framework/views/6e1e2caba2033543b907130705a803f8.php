<?php $__env->startSection('title','Concursos disponibles'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h3 class="mb-0">Concursos disponibles</h3>
    <div class="text-muted">Elige un concurso activo y participa.</div>
  </div>

  <div class="d-flex gap-2">
    <a href="<?php echo e(route('user.dashboard')); ?>" class="btn btn-outline-secondary btn-sm btn-nav">← Volver al panel</a>
    <a href="<?php echo e(route('user.results')); ?>" class="btn btn-outline-primary">Mis resultados</a>
  </div>
</div>

<?php if(!empty($needsBirthdateForAge)): ?>
  <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
      <div class="fw-semibold">Completa tu perfil para ver trivias por edad</div>
      <div class="small">Necesitamos tu <b>fecha de nacimiento</b> para calcular tu edad y mostrarte trivias adecuadas (por ejemplo, trivias 18+ no se muestran a menores).</div>
    </div>
    <a class="btn btn-primary btn-sm" href="<?php echo e(route('profile.edit')); ?>">Completar perfil</a>
  </div>
<?php endif; ?>

<?php if(!isset($contests) || !$contests->count()): ?>
  <div class="alert alert-warning">No hay concursos activos en este momento.</div>
<?php else: ?>
    <div class="row g-3">
      <?php $__currentLoopData = $contests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $status = $c->status ?? 'draft';
          $statusLabel = match($status) {
            'draft' => 'Borrador',
            'active' => 'Activo',
            'ended' => 'Finalizado',
            'cancelled' => 'Cancelado',
            default => 'Inactivo',
          };
        ?>
      <?php
        $rules = $c->rules;

        // Por seguridad: si en BD existe algo que NO sea trivia, no lo mostramos aquí.
        if (($c->type ?? null) !== 'trivia') { continue; }

        $myPart = \App\Models\Participation::where('contest_id', $c->id)
          ->where('user_id', auth()->id())
          ->first();
      ?>

      <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="mb-1"><?php echo e($c->title); ?></h5>
                <div class="text-muted small">Empresa: <b><?php echo e($c->company->name ?? '—'); ?></b></div>
              </div>
              <span class="badge bg-info">Trivia</span>
            </div>

            <?php if($c->description): ?>
              <p class="mt-2 mb-2"><?php echo e(\Illuminate\Support\Str::limit($c->description, 160)); ?></p>
            <?php endif; ?>

            <?php
              $p = ($c->prizes ?? collect())->sortBy('position')->first();
            ?>
            <?php if($p): ?>
              <div class="d-flex align-items-center gap-2 mb-3">
                <?php if($p->image_path): ?>
                  <img src="<?php echo e(asset('storage/' . $p->image_path)); ?>" alt="premio" style="width:64px;height:64px;object-fit:cover" class="rounded border">
                <?php else: ?>
                  <div class="rounded border bg-light" style="width:64px;height:64px"></div>
                <?php endif; ?>
                <div>
                  <div class="fw-semibold">Premio: <?php echo e($p->name); ?></div>
                  <div class="text-muted small">Cantidad: <?php echo e($p->quantity); ?></div>
                </div>
              </div>
            <?php endif; ?>

            <div class="small text-muted mb-3">
              Vigencia: <?php echo e(optional($c->start_at)->format('Y-m-d H:i')); ?> → <?php echo e(optional($c->end_at)->format('Y-m-d H:i')); ?><br>
              Estado: <span class="badge bg-<?php echo e($status === 'ended' ? 'info' : ($status === 'active' ? 'success' : 'secondary')); ?>"><?php echo e($statusLabel); ?></span>
            </div>

            <div class="border rounded p-2 bg-light mb-3">
              <div class="fw-semibold mb-1">Reglas</div>
              <?php if(!$rules): ?>
                <div class="text-muted small">Aún no se han configurado reglas para esta trivia.</div>
              <?php else: ?>
                <ul class="small mb-0">
                  <?php
                    $method = $c->winner_method ?? 'ranking';
                    $wc = (int)($rules->winners_count ?? 1);
                    $topN = (int)($rules->eligible_top_n ?? 0);
                    $methodLabel = $method === 'lottery'
                      ? 'Sorteo aleatorio'
                      : 'Top de méritos (Ranking)';
                  ?>
                  <li>Método de calificación: <b><?php echo e($methodLabel); ?></b></li>
                  <?php if($method === 'lottery'): ?>
                    <li>Ganadores: <b><?php echo e($wc); ?></b></li>
                    <li>Participan en el sorteo: <b><?php echo e($topN <= 0 ? 'Todos los inscritos' : 'Solo Top '.$topN.' (por méritos)'); ?></b></li>
                  <?php else: ?>
                    <li>Ganadores: <b>
                      <?php if($wc === 1): ?>
                        🥇 1er lugar
                      <?php elseif($wc === 2): ?>
                        🥇 1er y 🥈 2do lugar
                      <?php elseif($wc === 3): ?>
                        🥇 1er, 🥈 2do y 🥉 3er lugar
                      <?php else: ?>
                        Top <?php echo e($wc); ?>

                      <?php endif; ?>
                    </b></li>
                    <li>Criterio: mayor <b>aciertos</b>. Desempate: <b>menor tiempo</b></li>
                  <?php endif; ?>
                  <?php if($rules->max_participants): ?>
                    <li>Máx. participantes: <?php echo e($rules->max_participants); ?></li>
                  <?php endif; ?>

                  <li>Segundos por pregunta: <?php echo e($rules->seconds_per_question ?? '—'); ?></li>
                  <li>Intentos: <?php echo e($rules->attempts ?? '—'); ?></li>
                  <li>Tiempo total (min): <?php echo e($rules->total_time_minutes ?? $rules->expires_after_join_minutes ?? '—'); ?></li>
                </ul>
              <?php endif; ?>
            </div>

            <?php if($myPart): ?>
              <div class="alert alert-success py-2 mb-2">
                Ya estás inscrito.
              </div>
            <?php endif; ?>

            <div class="d-flex flex-wrap gap-2">
              <form method="POST" action="<?php echo e(route('user.contests.participate', $c->id)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-primary" type="submit">Inscribirme</button>
              </form>

              <form method="POST" action="<?php echo e(route('user.trivia.start', $c)); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-primary" type="submit">Iniciar Trivia</button>
              </form>
            </div>
            <div class="form-text mt-2">* “Iniciar Trivia” cuenta como un intento (si hay límite).</div>

            <div class="mt-3">
              <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#reportModal<?php echo e($c->id); ?>">
                🚩 Reportar contenido
              </button>
            </div>

            <!-- Modal Reporte -->
            <div class="modal fade" id="reportModal<?php echo e($c->id); ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Reportar contenido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <form method="POST" action="<?php echo e(route('reports.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                      <input type="hidden" name="contest_id" value="<?php echo e($c->id); ?>">
                      <div class="mb-2">
                        <label class="form-label">Motivo (opcional)</label>
                        <input type="text" name="reason" class="form-control" placeholder="Ej: contenido obsceno / ilegal / engañoso">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Describe la queja</label>
                        <textarea name="message" class="form-control" rows="4" required
                          placeholder="Cuéntanos qué está pasando y por qué lo reportas."></textarea>
                      </div>
                      <div class="text-muted small">
                        Este reporte será enviado al administrador para revisión.
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button class="btn btn-danger">Enviar reporte</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/user/contests.blade.php ENDPATH**/ ?>