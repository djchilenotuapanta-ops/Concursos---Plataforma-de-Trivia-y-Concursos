<?php $__env->startSection('title','Jugar Trivia'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <h3 class="mb-0">Trivia: <?php echo e($contest->title); ?></h3>
    <div class="text-muted small">
      Pregunta <?php echo e($progress->current_order); ?>

      • Tiempo por pregunta: <?php echo e($rules->seconds_per_question ?? '—'); ?>s
      • Tiempo total: <?php echo e($rules->total_time_minutes ?? $rules->expires_after_join_minutes ?? '—'); ?> min
    </div>
  </div>
  <div class="d-flex gap-2">
    <a href="<?php echo e(route('user.contests.list')); ?>" class="btn btn-outline-secondary btn-sm btn-nav">← Volver a concursos</a>
    <a href="<?php echo e(route('user.trivia.result', $contest)); ?>" class="btn btn-outline-primary">Ver resultado</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">

    <?php
      $qSeconds = (int)($rules->seconds_per_question ?? 0);
      $warnSeconds = (int)($rules->warning_seconds ?? 10);
      $totalLeft = $participation->attempt_expires_at
        ? max(0, $participation->attempt_expires_at->diffInSeconds(now()))
        : null;
    ?>

    <div id="timers" class="d-flex flex-wrap gap-2 mb-3">
      <?php if($qSeconds): ?>
        <div class="alert alert-info py-2 mb-0">
          ⏱️ <b>Tiempo por pregunta:</b> <span id="qTimer"><?php echo e($qSeconds); ?></span>s
        </div>
      <?php endif; ?>
      <?php if(!is_null($totalLeft)): ?>
        <div class="alert alert-secondary py-2 mb-0">
          🧭 <b>Tiempo total restante:</b> <span id="totalTimer"><?php echo e($totalLeft); ?></span>s
        </div>
      <?php endif; ?>
    </div>
    <h5 class="mb-3"><?php echo e($question->question); ?></h5>

    <form method="POST" action="<?php echo e(route('user.trivia.answer', $contest)); ?>">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="question_id" value="<?php echo e($question->id); ?>">

      <?php
        $options = [
          'a' => $question->option_a,
          'b' => $question->option_b,
          'c' => $question->option_c,
          'd' => $question->option_d,
        ];
      ?>

      <div class="list-group mb-3">
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if(!is_null($val) && $val !== ''): ?>
            <label class="list-group-item d-flex gap-2 align-items-center">
              <input class="form-check-input" type="radio" name="selected_option" value="<?php echo e($key); ?>" required>
              <div>
                <div class="fw-semibold text-uppercase"><?php echo e($key); ?>)</div>
                <div><?php echo e($val); ?></div>
              </div>
            </label>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <button class="btn btn-primary" type="submit">Enviar respuesta</button>
      <div class="text-muted small mt-2">
        Si se te acaba el tiempo, pasas automáticamente a la siguiente pregunta.
      </div>
    </form>

    <form id="timeoutForm" method="POST" action="<?php echo e(route('user.trivia.timeout', $contest)); ?>" class="d-none">
      <?php echo csrf_field(); ?>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Cronómetro: auto-avanza cuando se acaba el tiempo
(function(){
  const qSeconds = <?php echo e($qSeconds); ?>;
  let qLeft = qSeconds;
  const qEl = document.getElementById('qTimer');

  const totalLeftStart = <?php echo e(is_null($totalLeft) ? 'null' : (int)$totalLeft); ?>;
  let totalLeft = totalLeftStart;
  const totalEl = document.getElementById('totalTimer');

  const timeoutForm = document.getElementById('timeoutForm');
  const warnSeconds = <?php echo e((int)($rules->warning_seconds ?? 10)); ?>;
  const warned = new Set();

  function showWarning(msg, type){
    const wrap = document.getElementById('timers');
    if(!wrap) return;
    const div = document.createElement('div');
    div.className = 'alert alert-' + (type || 'warning') + ' py-2 mb-0';
    div.textContent = msg;
    wrap.appendChild(div);
  }

  // Tiempo por pregunta
  if (qSeconds && qEl && timeoutForm) {
    const qTimer = setInterval(() => {
      qLeft -= 1;
      if (qLeft < 0) qLeft = 0;
      qEl.textContent = qLeft;

      if (warnSeconds && qLeft === warnSeconds) {
        showWarning('⚠️ Ojo: quedan ' + warnSeconds + ' segundos para responder esta pregunta.', 'warning');
      }

      if (qLeft === 0) {
        clearInterval(qTimer);
        showWarning('⏱️ Se terminó el tiempo de esta pregunta. Pasando a la siguiente...', 'danger');
        setTimeout(() => timeoutForm.submit(), 600);
      }
    }, 1000);
  }

  // Tiempo total + avisos
  if (totalLeftStart !== null && totalEl) {
    const totalTimer = setInterval(() => {
      totalLeft -= 1;
      if (totalLeft < 0) totalLeft = 0;
      totalEl.textContent = totalLeft;

      const marks = warnSeconds ? [warnSeconds] : [];
      for (const m of marks) {
        if (totalLeft === m && !warned.has(m)) {
          warned.add(m);
          showWarning('⚠️ Tu tiempo total está por terminar: quedan ' + m + ' segundos.', 'warning');
        }
      }

      if (totalLeft === 0) {
        clearInterval(totalTimer);
        showWarning('⛔ Se terminó tu tiempo total. No podrás seguir respondiendo.', 'danger');
      }
    }, 1000);
  }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/trivia/play.blade.php ENDPATH**/ ?>