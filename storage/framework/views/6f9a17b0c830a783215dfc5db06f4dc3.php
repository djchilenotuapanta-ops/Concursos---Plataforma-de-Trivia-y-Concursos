<?php $__env->startSection('content'); ?>
<div class="container py-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h3 class="mb-0">Configurar Reglas y Premio (Admin) — Paso 2 de 2</h3>
      <div class="text-muted small">Trivia: <b><?php echo e($contest->title); ?></b> — Preguntas cargadas: <b><?php echo e($questionsCount); ?></b></div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.trivia.questions.index', $contest)); ?>">⬅ Volver a preguntas</a>
      <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.contests.index')); ?>">🏠 Concursos</a>
    </div>
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

  <form method="POST" action="<?php echo e(route('admin.trivia.rules.update', $contest)); ?>" class="card" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <div class="card-body">

      <div class="alert alert-info">
        <b>Importante:</b> El número de preguntas se toma automáticamente de las que ya cargaste (<b><?php echo e($questionsCount); ?></b>).
      </div>

      <div class="p-3 border rounded mb-4">
        <h5 class="mb-3">Reglas de tiempo</h5>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Tiempo por pregunta (segundos)</label>
            <input type="number" name="seconds_per_question" class="form-control"
                   min="1" max="600" value="<?php echo e(old('seconds_per_question', $rule?->seconds_per_question ?? 20)); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Aviso cuando falten X segundos</label>
            <input type="number" name="warning_seconds" class="form-control"
                   min="0" max="600" value="<?php echo e(old('warning_seconds', $rule?->warning_seconds ?? 10)); ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Intentos permitidos</label>
            <input type="number" name="attempts" class="form-control"
                   min="1" max="10" value="<?php echo e(old('attempts', $rule?->attempts ?? 1)); ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Tiempo total para completar (minutos) (opcional)</label>
            <input type="number" name="total_time_minutes" class="form-control"
                   min="1" max="240" value="<?php echo e(old('total_time_minutes', $rule?->total_time_minutes)); ?>">
            <div class="form-text">Si lo dejas vacío, solo aplica el tiempo por pregunta.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Restricción de edad</label>
            <select name="age_group" class="form-select">
              <?php $ag = old('age_group', $rule?->age_group ?? 'all'); ?>
              <option value="all" <?php echo e($ag === 'all' ? 'selected' : ''); ?>>Todas las edades</option>
              <option value="kids" <?php echo e($ag === 'kids' ? 'selected' : ''); ?>>Niños (hasta 13 años)</option>
              <option value="teens" <?php echo e($ag === 'teens' ? 'selected' : ''); ?>>Adolescentes (13-18 años)</option>
              <option value="adults" <?php echo e($ag === 'adults' ? 'selected' : ''); ?>>Adultos (18+ años)</option>
            </select>
            <div class="form-text">El usuario debe tener la edad registrada en su perfil.</div>
          </div>
        </div>
      </div>

      <div class="p-3 border rounded bg-light mb-4">
        <h5 class="mb-3">Método de ganadores</h5>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">¿Cómo se elegirá el ganador?</label>
            <?php
              $wm = old('winner_method', $contest->winner_method ?? 'ranking');
              $scope = old('eligible_scope', ($rule && (int)($rule->eligible_top_n ?? 0) > 0) ? 'top' : 'all');
            ?>
            <select name="winner_method" id="winner_method" class="form-select" required>
              <option value="ranking" <?php echo e($wm === 'ranking' ? 'selected' : ''); ?>>Ranking (más aciertos y menor tiempo)</option>
              <option value="lottery" <?php echo e($wm === 'lottery' ? 'selected' : ''); ?>>Sorteo (ganadores al azar)</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Cantidad de ganadores</label>
            <input type="number" name="winners_count" class="form-control" min="1" max="100"
                   value="<?php echo e(old('winners_count', $rule?->winners_count ?? 1)); ?>" required>
            <div class="form-text">Número de participantes que ganarán.</div>
          </div>
        </div>

        <div class="row g-3 mt-2" id="lottery_box" style="display:none">
          <div class="col-md-12">
            <label class="form-label">Configuración del sorteo</label>
            <div class="row g-2">
              <div class="col-12">
                <select name="eligible_scope" id="eligible_scope" class="form-select">
                  <option value="all" <?php echo e($scope === 'all' ? 'selected' : ''); ?>>Todos los inscritos</option>
                  <option value="top" <?php echo e($scope === 'top' ? 'selected' : ''); ?>>Solo Top N (por méritos)</option>
                </select>
                <div class="form-text">Quiénes entran al sorteo</div>
              </div>
              <div class="col-6" id="eligible_top_n_wrap" style="display:none">
                <input type="number" name="eligible_top_n" class="form-control" min="1" max="500"
                       value="<?php echo e(old('eligible_top_n', $rule?->eligible_top_n ?? 10)); ?>">
                <div class="form-text">Top N elegibles</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-3 border rounded mb-4">
        <h5 class="mb-3">Premio principal</h5>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre del premio</label>
            <input type="text" name="prize_name" class="form-control"
                   value="<?php echo e(old('prize_name', $mainPrize?->name)); ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Cantidad</label>
            <input type="number" name="prize_quantity" class="form-control"
                   min="1" max="1000" value="<?php echo e(old('prize_quantity', $mainPrize?->quantity ?? 1)); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Imagen (opcional)</label>
            <input type="file" name="prize_image" class="form-control" accept="image/*">
            <?php if($mainPrize && $mainPrize->image_path): ?>
              <div class="form-text">Ya hay una imagen cargada.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

    <div class="card-footer d-flex gap-2">
      <button class="btn btn-primary">Guardar Paso 2</button>
      <a class="btn btn-secondary" href="<?php echo e(route('admin.trivia.questions.index', $contest)); ?>">Cancelar</a>
    </div>
  </form>
</div>

<script>
  function toggleLottery() {
    const wm = document.getElementById('winner_method').value;
    document.getElementById('lottery_box').style.display = (wm === 'lottery') ? 'block' : 'none';
  }

  function toggleEligibleTopN() {
    const scope = document.getElementById('eligible_scope');
    const wrap = document.getElementById('eligible_top_n_wrap');
    if (!scope || !wrap) return;
    wrap.style.display = (scope.value === 'top') ? 'block' : 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    toggleLottery();
    toggleEligibleTopN();

    document.getElementById('winner_method').addEventListener('change', () => {
      toggleLottery();
      toggleEligibleTopN();
    });

    const scope = document.getElementById('eligible_scope');
    if (scope) scope.addEventListener('change', toggleEligibleTopN);
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/admin/trivia/rules/edit.blade.php ENDPATH**/ ?>