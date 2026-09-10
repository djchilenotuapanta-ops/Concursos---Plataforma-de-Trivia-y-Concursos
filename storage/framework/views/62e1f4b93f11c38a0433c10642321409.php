<?php
  $user = auth()->user();
  $role = strtolower(trim((string) optional($user)->role));
  $role = match ($role) {
    'administrator','administrador','admin' => 'admin',
    'moderador','moderator' => 'moderator',
    'empresa','company' => 'company',
    default => $role,
  };

  $areaPrefix = match($role) {
    'admin' => 'admin',
    'moderator' => 'moderator',
    'company' => 'company',
    default => 'admin',
  };

  $pageTitle = $pageTitle ?? match($role) {
    'admin' => 'Preguntas (Admin)',
    'moderator' => 'Preguntas (Moderador)',
    'company' => 'Preguntas de Trivia',
    default => 'Preguntas',
  };

  $backRoute = match($role) {
    'admin' => 'admin.contests_adv.index',
    'moderator' => 'moderator.contests.index',
    'company' => 'company.contests.index',
    default => 'admin.contests_adv.index',
  };

  $manualQuestionsEnabled = (bool) config('ganafacil.trivia_manual_questions_enabled', true);
  $hasQuestions = (($questions ?? collect())->count() > 0);
?>



<?php $__env->startSection('content'); ?>
<div class="container py-3">

  <?php if($role === 'company' && ($contest->status ?? null) === 'draft'): ?>
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div>
        <b>Paso 1 de 2:</b> Carga preguntas (Excel/CSV<?php echo e($manualQuestionsEnabled ? ' o manual' : ''); ?>).<br>
        <span class="text-muted">Luego ve al Paso 2 para configurar reglas, premio y tiempos, y finalmente publica.</span>
      </div>
    </div>
  <?php endif; ?>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h3 class="mb-0"><?php echo e($pageTitle); ?></h3>
      <div class="text-muted small">Trivia: <b><?php echo e($contest->title); ?></b></div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a href="<?php echo e(route($backRoute)); ?>" class="btn btn-outline-secondary btn-sm btn-nav">
        ⬅ Volver
      </a>

      <a href="<?php echo e(route($areaPrefix.'.trivia.questions.import.form', $contest)); ?>" class="btn btn-outline-success">
        📥 Importar
      </a>

      <?php if($hasQuestions): ?>
        <a href="<?php echo e(route($areaPrefix.'.trivia.rules.edit', $contest)); ?>" class="btn btn-warning">
          ➡ <?php echo e($role === 'company' ? 'Paso 2:' : ''); ?> Reglas y premio
        </a>
      <?php else: ?>
        <button type="button" class="btn btn-warning" disabled title="Primero carga preguntas">
          ➡ <?php echo e($role === 'company' ? 'Paso 2:' : ''); ?> Reglas y premio
        </button>
      <?php endif; ?>

      <?php if($manualQuestionsEnabled): ?>
        <a href="<?php echo e(route($areaPrefix.'.trivia.questions.create', $contest)); ?>" class="btn btn-primary">
          ➕ Nueva pregunta
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <?php if(!$hasQuestions): ?>
        <div class="alert alert-info mb-0">
          Aún no has cargado preguntas.
          <b>Tip:</b> si tienes muchas, usa <b>Importar (CSV/Excel)</b>.
        </div>
      <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted small">
            Total: <b><?php echo e($questions->count()); ?></b>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:70px">Orden</th>
                <th>Pregunta</th>
                <th style="width:120px">Correcta</th>
                <th style="width:120px" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td>
                    <span class="badge bg-secondary"><?php echo e($q->order ?? $q->id); ?></span>
                  </td>
                  <td>
                    <div class="fw-semibold"><?php echo e($q->question); ?></div>
                    <div class="text-muted small">
                      A) <?php echo e($q->option_a); ?> &nbsp;|&nbsp;
                      B) <?php echo e($q->option_b); ?>

                      <?php if($q->option_c): ?> &nbsp;|&nbsp; C) <?php echo e($q->option_c); ?> <?php endif; ?>
                      <?php if($q->option_d): ?> &nbsp;|&nbsp; D) <?php echo e($q->option_d); ?> <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-success"><?php echo e(strtoupper($q->correct_option)); ?></span>
                  </td>
                  <td class="text-end">
                    <form method="POST" action="<?php echo e(route($areaPrefix.'.trivia.questions.destroy', [$contest, $q])); ?>" onsubmit="return confirm('¿Eliminar esta pregunta?')" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/shared/trivia/questions/index.blade.php ENDPATH**/ ?>