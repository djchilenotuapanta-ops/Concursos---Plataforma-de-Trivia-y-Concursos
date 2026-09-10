

<?php $__env->startSection('title','Reporte de metricas'); ?>

<?php $__env->startSection('content'); ?>
<?php
  // Helpers simples para UI
  $pct = function($now, $prev) {
    $prev = (float)($prev ?? 0);
    $now  = (float)($now ?? 0);
    if ($prev == 0) {
      return $now == 0 ? 0 : 100;
    }
    return (($now - $prev) / $prev) * 100;
  };

  $fmtMoney = function($v) {
    return '$' . number_format((float)$v, 2, '.', ',');
  };

  $kpiCard = function($title, $now, $prev, $isMoney=false, $suffix='') use ($pct, $fmtMoney) {
    $delta = $pct($now, $prev);
    $badgeClass = $delta >= 0 ? 'bg-success' : 'bg-danger';
    $badgeText  = ($delta >= 0 ? '+' : '') . number_format($delta, 1) . '%';
    $value = $isMoney ? $fmtMoney($now) : number_format((float)$now, 0, '.', ',');
    if ($suffix) $value .= ' ' . $suffix;
    return [
      'title' => $title,
      'value' => $value,
      'badgeClass' => $badgeClass,
      'badgeText' => $badgeText,
    ];
  };

  $cards = [
    $kpiCard('Total Participantes', $kpis['participants']['now'] ?? 0, $kpis['participants']['prev'] ?? 0),
    $kpiCard('Ingresos Totales', $kpis['income']['now'] ?? 0, $kpis['income']['prev'] ?? 0, true),
    $kpiCard('Margen de Ganancia', $kpis['margin']['now'] ?? 0, $kpis['margin']['prev'] ?? 0, true),
  ];

  // Satisfaccion (rating) puede ser null si no hay datos
  $ratingNow = $kpis['rating']['now'] ?? null;
  $ratingPrev = $kpis['rating']['prev'] ?? null;
  $ratingDelta = ($ratingNow === null && $ratingPrev === null) ? null : $pct($ratingNow ?? 0, $ratingPrev ?? 0);
?>

<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
  <div>
    <h3 class="mb-1">Reporte <?php echo e($period === 'day' ? 'Diario' : 'Mensual'); ?></h3>
    <div class="text-muted small">
      Periodo: <b><?php echo e($label); ?></b> · Comparacion: <b><?php echo e($compareLabel); ?></b>
    </div>
  </div>

  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('admin.metrics', ['period' => 'day', 'date' => now()->toDateString()])); ?>">Hoy</a>
    <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('admin.metrics', ['period' => 'day', 'date' => now()->subDay()->toDateString()])); ?>">Ayer</a>
    <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('admin.metrics', ['period' => 'month', 'date' => now()->toDateString()])); ?>">Este mes</a>
    <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('admin.metrics', ['period' => 'month', 'date' => now()->subMonth()->toDateString()])); ?>">Mes anterior</a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="GET" action="<?php echo e(route('admin.metrics')); ?>">
      <div class="col-12 col-md-4">
        <label class="form-label">Tipo de reporte</label>
        <select class="form-select" name="period" id="period">
          <option value="month" <?php echo e($period === 'month' ? 'selected' : ''); ?>>Mensual</option>
          <option value="day" <?php echo e($period === 'day' ? 'selected' : ''); ?>>Diario</option>
        </select>
      </div>

      <div class="col-12 col-md-4" id="dateWrap">
        <label class="form-label">Fecha base</label>
        <input class="form-control" type="date" name="date" value="<?php echo e($date); ?>">
        <div class="form-text">Para mensual, solo se toma el mes de esta fecha.</div>
      </div>

      <div class="col-12 col-md-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Ver reporte</button>
        <a class="btn btn-outline-secondary btn-nav" href="<?php echo e(route('admin.dashboard')); ?>">Volver al panel</a>
      </div>
    </form>
  </div>
</div>

<?php if(!empty($alerts)): ?>
  <div class="mb-3">
    <h5 class="mb-2">Alertas</h5>
    <div class="row g-2">
      <?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-12 col-lg-6">
          <div class="alert alert-<?php echo e($a['type'] ?? 'info'); ?> mb-0">
            <div class="fw-semibold"><?php echo e($a['title'] ?? 'Alerta'); ?></div>
            <div class="small"><?php echo e($a['message'] ?? ''); ?></div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
<?php endif; ?>

<div class="row g-3 mb-3">
  <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div class="text-muted"><?php echo e($c['title']); ?></div>
            <span class="badge <?php echo e($c['badgeClass']); ?>"><?php echo e($c['badgeText']); ?></span>
          </div>
          <div class="display-6 fw-semibold mt-2"><?php echo e($c['value']); ?></div>
          <div class="text-muted small">vs. periodo anterior</div>
        </div>
      </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div class="text-muted">Satisfaccion del Cliente</div>
          <?php if($ratingDelta === null): ?>
            <span class="badge bg-secondary">Sin datos</span>
          <?php else: ?>
            <span class="badge <?php echo e($ratingDelta >= 0 ? 'bg-success' : 'bg-danger'); ?>">
              <?php echo e(($ratingDelta >= 0 ? '+' : '') . number_format($ratingDelta, 1)); ?>%
            </span>
          <?php endif; ?>
        </div>
        <div class="display-6 fw-semibold mt-2">
          <?php if($ratingNow === null): ?>
            —
          <?php else: ?>
            <?php echo e(number_format((float)$ratingNow, 2)); ?> / 5
          <?php endif; ?>
        </div>
        <div class="text-muted small">Promedio de calificaciones en el periodo</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-xl-4">
    <div class="card h-100">
      <div class="card-header bg-white">
        <b>Resumen operativo</b>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>Usuarios que ingresaron</div>
          <div class="fw-semibold"><?php echo e(number_format((float)$loginsNow, 0, '.', ',')); ?></div>
        </div>
        <hr>
        <div class="small text-muted">
          <div>Suscripciones aprobadas en el periodo: <b><?php echo e($subsNow['count'] ?? 0); ?></b></div>
          <div>Ingreso estimado por suscripcion: <b>$<?php echo e(number_format((float)$subscriptionFee, 2, '.', ',')); ?></b></div>
          <div class="mt-2">

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <b>Top <?php echo e($topN ?? 5); ?> concursos más participados</b>
        <span class="badge bg-primary">Top <?php echo e($topN ?? 5); ?></span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Titulo</th>
                <th class="text-end">Participaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $topByParticipants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?php echo e($row->title); ?></div>
                    <div class="text-muted small"><?php echo e($row->type === 'raffle' ? 'Trivia' : 'Trivia'); ?></div>
                  </td>
                  <td class="text-end fw-semibold"><?php echo e(number_format((float)$row->participations_count, 0, '.', ',')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="2" class="text-center text-muted py-4">Sin datos en este periodo</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <b>Top <?php echo e($topN ?? 5); ?> concursos con más ingresos</b>
        <span class="badge bg-success">Top <?php echo e($topN ?? 5); ?></span>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Titulo</th>
                <th class="text-end">Ingresos</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $topByIncome; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?php echo e($row->title); ?></div>
                    <div class="text-muted small"><?php echo e($row->type === 'raffle' ? 'Trivia' : 'Trivia'); ?></div>
                  </td>
                  <td class="text-end fw-semibold">$<?php echo e(number_format((float)$row->total_income, 2, '.', ',')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="2" class="text-center text-muted py-4">Sin datos de ingresos en este periodo</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-white small text-muted">
        Los ingresos se calculan como: <b>boletos × precio del boleto</b> (ticket_price).
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
  // UX: si el usuario cambia el tipo de reporte, no rompemos la UI
  const period = document.getElementById('period');
  period?.addEventListener('change', () => {
    // mantenemos input date por simplicidad, pero puedes cambiarlo a month si deseas.
  });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/admin/metrics.blade.php ENDPATH**/ ?>