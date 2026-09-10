<?php
  /**
   * Partial compartido: Admin / Moderador / Empresa
   *
   * Variables esperadas:
   * - $backRoute  (string) : nombre de ruta para volver
   * - $storeRoute (string) : nombre de ruta para guardar
   * - $pageTitle  (string) : título (opcional)
   * - $subtitle   (string) : subtítulo (opcional)
   * - $companies  (Collection<User>) : si existe, se mostrará selector de empresa
   */

  $pageTitle = $pageTitle ?? 'Crear concurso';
  $subtitle  = $subtitle  ?? 'Crea una TRIVIA usando el flujo de 2 pasos: primero datos básicos, luego preguntas, reglas y premio.';
?>

<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0"><?php echo e($pageTitle); ?></h3>
      <div class="text-muted small"><?php echo e($subtitle); ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="<?php echo e(route($backRoute)); ?>">⬅ Volver</a>
  </div>

  <?php if(session('error')): ?>
    <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($e); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route($storeRoute)); ?>" class="card">
    <?php echo csrf_field(); ?>

    <div class="card-body">

      <?php if(isset($companies)): ?>
        <div class="mb-3">
          <label class="form-label">Asignar a empresa</label>
          <select name="company_id" class="form-select" required>
            <option value="">-- Selecciona --</option>
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($c->id); ?>" <?php echo e(old('company_id') == $c->id ? 'selected' : ''); ?>>
                <?php echo e(($c->company_name ? $c->company_name.' - ' : '')); ?><?php echo e($c->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <div class="form-text">El concurso quedará asociado a esa empresa.</div>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Tipo de concurso</label>
        <select name="type" id="contestType" class="form-select" required>
          <option value="trivia" <?php echo e(old('type','trivia')==='trivia' ? 'selected' : ''); ?>>TRIVIA (preguntas)</option>
        </select>
        <div class="form-text">Las TRIVIAS se participan respondiendo preguntas.</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción (opcional)</label>
        <textarea name="description" class="form-control" rows="3"><?php echo e(old('description')); ?></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Cierre rápido (opcional)</label>
          <select name="quick_close_minutes" id="quick_close_minutes" class="form-select">
            <option value="" <?php echo e(old('quick_close_minutes') ? '' : 'selected'); ?>>Elegir fecha y hora</option>
            <option value="10" <?php echo e(old('quick_close_minutes') == '10' ? 'selected' : ''); ?>>Cerrar en 10 minutos</option>
            <option value="30" <?php echo e(old('quick_close_minutes') == '30' ? 'selected' : ''); ?>>Cerrar en 30 minutos</option>
            <option value="60" <?php echo e(old('quick_close_minutes') == '60' ? 'selected' : ''); ?>>Cerrar en 1 hora</option>
            <option value="180" <?php echo e(old('quick_close_minutes') == '180' ? 'selected' : ''); ?>>Cerrar en 3 horas</option>
          </select>
          <div class="form-text">Si eliges cierre rápido, no necesitas la fecha/hora manual.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha y hora de cierre</label>
          <input type="datetime-local" name="end_at" id="end_at" class="form-control"
                 value="<?php echo e(old('end_at')); ?>"
                 min="<?php echo e(now()->format('Y-m-d\\TH:i')); ?>">
        </div>
      </div>

      <div class="alert alert-warning mt-3 mb-0">
        <b>Orden obligatorio:</b> Paso 1 crear ➜ cargar preguntas ➜ Paso 2 reglas/premio ➜ publicar.
      </div>
    </div>

    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary">Guardar y cargar preguntas</button>
      <a class="btn btn-secondary" href="<?php echo e(route($backRoute)); ?>">Cancelar</a>
    </div>
  </form>
</div>

<script>
  (function () {
    const quick = document.getElementById('quick_close_minutes');
    const endAt = document.getElementById('end_at');

    function toggleEndAt() {
      if (!quick || !endAt) return;
      const hasQuick = !!quick.value;
      endAt.disabled = hasQuick;
      if (hasQuick) endAt.value = '';
    }

    if (quick) quick.addEventListener('change', toggleEndAt);
    toggleEndAt();
  })();
</script>
<?php /**PATH C:\laragon\www\concursos\resources\views/contests/_create_form.blade.php ENDPATH**/ ?>