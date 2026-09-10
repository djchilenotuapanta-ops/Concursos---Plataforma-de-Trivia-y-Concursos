

<?php $__env->startSection('title','Admin / Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php ($stats = $stats ?? []); ?>

<div class="py-2">
  <?php if(($stats['pending_vouchers'] ?? 0) > 0): ?>
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
      <div>
        <b>Suscripciones pendientes:</b> hay <b><?php echo e($stats['pending_vouchers']); ?></b> voucher(s) por revisar.
      </div>
      <a href="/admin/companies" class="btn btn-sm btn-outline-dark">Revisar ahora</a>
    </div>
  <?php endif; ?>

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
      <h3 class="mb-0">Usuarios</h3>
      <div class="text-muted small">Vista general del sistema (concursos, empresas y usuarios).</div>
    </div>
    <div class="d-flex gap-2">
      <a href="/admin/contests/create" class="btn btn-sm btn-primary">+ Nuevo</a>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="dash-metric">
            <div>
              <div class="label">Concursos</div>
              <div class="value"><?php echo e($stats['contests'] ?? 0); ?></div>
            </div>
            <div class="icon">🎯</div>
          </div>
          <div class="mt-3 d-flex gap-2">
            <a href="/admin/contests" class="btn btn-sm btn-outline-primary">Ver</a>
            <a href="/admin/contests/create" class="btn btn-sm btn-primary">Crear</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="dash-metric">
            <div>
              <div class="label">Empresas</div>
              <div class="value"><?php echo e($stats['companies'] ?? 0); ?></div>
              <div class="text-muted small">Pendientes: <?php echo e($stats['pending_companies'] ?? 0); ?></div>
              <div class="text-muted small">Vouchers por revisar: <b><?php echo e($stats['pending_vouchers'] ?? 0); ?></b></div>
            </div>
            <div class="icon">🏢</div>
          </div>
          <div class="mt-3">
            <a href="/admin/companies" class="btn btn-sm btn-outline-primary">Revisar</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="dash-metric">
            <div>
              <div class="label">Usuarios</div>
              <div class="value"><?php echo e($stats['users'] ?? 0); ?></div>
            </div>
            <div class="icon">👤</div>
          </div>
          <div class="mt-3">
            <a href="/admin/users" class="btn btn-sm btn-outline-primary">Ver</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="dash-metric">
            <div>
              <div class="label">Métricas</div>
              <div class="value">📊</div>
              <div class="text-muted small">Resumen visual</div>
            </div>
            <div class="icon">📈</div>
          </div>
          <div class="mt-3">
            <a href="/admin/metrics" class="btn btn-sm btn-outline-primary">Abrir</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
              <div class="fw-semibold">Gráfica rápida</div>
              <div class="text-muted small">Concursos, usuarios y premios (barras)</div>
            </div>
          </div>
          <div style="height: 260px;">
            <canvas id="statsChart" data-values='<?php echo json_encode([
              (int)($stats['contests'] ?? 0), (int)($stats['users'] ?? 0), (int)($stats['prizes'] ?? 0)
            ]) ?>'></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if(($stats['pending_vouchers'] ?? 0) > 0): ?>
    <div class="alert alert-warning">
      <b>Atención:</b> tienes <b><?php echo e($stats['pending_vouchers']); ?></b> voucher(s) de suscripción esperando aprobación.
      <a href="/admin/companies" class="alert-link">Ir a revisar</a>
    </div>
  <?php endif; ?>

  <div class="alert alert-info mb-0">
    <b>Tip:</b> crea concursos, aprueba empresas y revisa usuarios desde el menú de la izquierda.
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>