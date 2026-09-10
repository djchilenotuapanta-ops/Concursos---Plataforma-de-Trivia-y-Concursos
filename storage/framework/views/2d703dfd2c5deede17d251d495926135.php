<?php $__env->startSection('content'); ?>
<div class="container py-4">
  <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
    <div>
      <h3 class="mb-1">Empresas registradas</h3>
      <div class="text-muted small">
        Aquí puedes revisar datos, ver comprobantes, aprobar vouchers y activar/inactivar empresas.
      </div>
    </div>

    <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
      <form method="GET" action="<?php echo e(route('admin.companies.index')); ?>" class="d-flex">
        <div class="input-group input-group-sm">
          <span class="input-group-text">🔍</span>
          <input
            type="text"
            name="q"
            value="<?php echo e($q ?? ''); ?>"
            class="form-control"
            placeholder="Buscar por empresa, email o RUC…">
          <?php if(!empty($q)): ?>
            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.companies.index')); ?>" title="Limpiar">✖</a>
          <?php endif; ?>
          <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
      </form>

      <span class="badge rounded-pill bg-light text-dark border align-self-sm-center">
        <?php if(!empty($q)): ?>
          Mostrando: <?php echo e($companies?->count() ?? 0); ?> de <?php echo e($stats['total'] ?? ($companies?->count() ?? 0)); ?>

        <?php else: ?>
          Total: <?php echo e($companies?->count() ?? 0); ?>

        <?php endif; ?>
      </span>

      <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
        ⬅ Volver al panel
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">

      <?php if(isset($companies) && $companies->count()): ?>
        <div class="table-responsive">
          <table class="table align-middle mb-0">

            <thead class="bg-light">
              <tr class="text-muted small">
                <th>#</th>
                <th>Logo</th>
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Comprobante</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>

            <tbody>
              <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $logoUrl = $c->logo ? asset('storage/'.$c->logo) : null;
                  $voucherUrl = $c->voucher_image ? asset('storage/'.$c->voucher_image) : null;

                  $companyApproved = (int)($c->approved ?? 0) === 1;
                  $companyText = $companyApproved ? 'Empresa aprobada' : 'Empresa pendiente';
                  $companyClass = $companyApproved ? 'bg-success' : 'bg-warning text-dark';

                  $voucherStatus = (int)($c->voucher_approved ?? 0);
                  $voucherText = match($voucherStatus) {
                    1 => 'Voucher aprobado',
                    2 => 'Voucher rechazado',
                    default => ($c->voucher_image ? 'Voucher en revisión' : 'Voucher no enviado'),
                  };
                  $voucherClass = match($voucherStatus) {
                    1 => 'bg-success',
                    2 => 'bg-danger',
                    default => ($c->voucher_image ? 'bg-warning text-dark' : 'bg-secondary'),
                  };
                ?>

                <tr>
                  <td><?php echo e($i + 1); ?></td>

                  <td>
                    <?php if($logoUrl): ?>
                      <a href="<?php echo e($logoUrl); ?>" target="_blank">
                        <img src="<?php echo e($logoUrl); ?>" style="width:52px;height:52px;object-fit:contain;">
                      </a>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>

                  <td>
                    <strong><?php echo e($c->company_name ?? $c->name ?? '—'); ?></strong><br>
                    <small class="text-muted">Razón social: <?php echo e($c->razon_social ?? '—'); ?></small><br>
                    <small class="text-muted">RUC: <?php echo e($c->ruc ?? '—'); ?></small>
                  </td>

                  <td>
                    <small>Email: <?php echo e($c->email ?? '—'); ?></small><br>
                    <small>Tel: <?php echo e($c->phone ?? '—'); ?></small><br>
                    <small class="text-muted">Rep: <?php echo e($c->representative_name ?? '—'); ?></small>
                  </td>

                  <td>
                    <?php if($voucherUrl): ?>
                      <a href="#"
                         class="btn btn-sm btn-outline-secondary btn-view-voucher"
                         data-img="<?php echo e($voucherUrl); ?>">
                        Ver comprobante
                      </a>
                    <?php else: ?>
                      <span class="badge bg-light text-dark">No subido</span>
                    <?php endif; ?>
                  </td>

                  <td>
                    <div class="d-flex flex-column gap-1">
                      <span class="badge <?php echo e($companyClass); ?>"><?php echo e($companyText); ?></span>
                      <span class="badge <?php echo e($voucherClass); ?>"><?php echo e($voucherText); ?></span>

                      <?php
                        $isActive = (int)($c->active ?? 1) === 1;
                      ?>
                      <span class="badge <?php echo e($isActive ? 'bg-success' : 'bg-danger'); ?>"><?php echo e($isActive ? 'Activa' : 'Inactiva'); ?></span>

                      <?php if((int)($c->voucher_approved ?? 0) === 2 && $c->rejection_reason): ?>
                        <div class="small text-muted">
                          Motivo: <?php echo e(\Illuminate\Support\Str::limit($c->rejection_reason, 60)); ?>

                        </div>
                      <?php endif; ?>
                    </div>
                  </td>

                  <td class="text-end">
                    <div class="d-inline-flex gap-1">                      <form method="POST" action="<?php echo e(route('admin.companies.subscription.approve', $c->id)); ?>" class="form-voucher-approve">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button class="btn btn-primary btn-sm" <?php echo e(!$voucherUrl || $voucherStatus === 1 ? 'disabled' : ''); ?>>
                          Aprobar voucher
                        </button>
                      </form>

                      <form method="POST" action="<?php echo e(route('admin.companies.approve', $c->id)); ?>" class="form-company-approve">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button class="btn btn-outline-success btn-sm" <?php echo e($companyApproved ? 'disabled' : ''); ?>>
                          Aprobar empresa
                        </button>
                      </form>

                      <form method="POST" action="<?php echo e(route('admin.companies.subscription.reject', $c->id)); ?>" class="form-voucher-reject">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="rejection_reason" value="">
                        <button class="btn btn-outline-danger btn-sm" <?php echo e(!$voucherUrl || $voucherStatus === 1 ? 'disabled' : ''); ?>>
                          Rechazar
                        </button>
                      </form>

                      <form method="POST" action="<?php echo e(route('admin.companies.active.toggle', $c->id)); ?>" class="form-toggle-active">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="active" value="<?php echo e((int)($c->active ?? 1) === 1 ? 0 : 1); ?>">
                        <input type="hidden" name="reason" value="">
                        <button class="btn btn-outline-<?php echo e((int)($c->active ?? 1) === 1 ? 'danger' : 'success'); ?> btn-sm">
                          <?php echo e((int)($c->active ?? 1) === 1 ? 'Inactivar' : 'Activar'); ?>

                        </button>
                      </form>

                      <a href="<?php echo e(route('admin.companies.show', $c->id)); ?>" class="btn btn-outline-primary btn-sm">
                        Ver
                      </a>

                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>

          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-5">
          <h5>No hay empresas registradas</h5>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<div class="modal fade" id="voucherModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h6 class="modal-title">Comprobante</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <img id="voucherImage" src="" class="img-fluid" style="max-height:80vh;object-fit:contain;">
      </div>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // ABRIR COMPROBANTE EN MODAL
  document.querySelectorAll('.btn-view-voucher').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('voucherImage').src = this.dataset.img;
      new bootstrap.Modal(document.getElementById('voucherModal')).show();
    });
  });

  // CONFIRMACIÓN: aprobar voucher
  document.querySelectorAll('.form-voucher-approve').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: '¿Aprobar voucher?',
        text: 'Esto activará la suscripción por 1 mes.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
      }).then(r => r.isConfirmed && form.submit());
    });
  });

  // CONFIRMACIÓN: aprobar empresa
  document.querySelectorAll('.form-company-approve').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: '¿Aprobar empresa?',
        text: 'Esto marcará la empresa como aprobada (el voucher sigue siendo un paso separado).',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
      }).then(r => r.isConfirmed && form.submit());
    });
  });

  // CONFIRMACIÓN: rechazar voucher
  document.querySelectorAll('.form-voucher-reject').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: 'Rechazar voucher',
        text: 'Escribe un motivo (recomendado).',
        icon: 'warning',
        input: 'text',
        inputPlaceholder: 'Ej: imagen borrosa / pago incorrecto / datos no coinciden',
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        cancelButtonText: 'Cancelar',
      }).then(result => {
        if (!result.isConfirmed) return;
        form.querySelector('input[name="rejection_reason"]').value = (result.value || '').trim();
        form.submit();
      });
    });
  });

  // CONFIRMACIÓN: activar/inactivar empresa
  document.querySelectorAll('.form-toggle-active').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const makeActive = form.querySelector('input[name="active"]').value === '1';

      if (makeActive) {
        Swal.fire({
          title: '¿Activar empresa?',
          text: 'La empresa podrá operar normalmente.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, activar',
          cancelButtonText: 'Cancelar'
        }).then(r => r.isConfirmed && form.submit());
        return;
      }

      // Inactivar: pedir motivo
      Swal.fire({
        title: 'Inactivar empresa',
        text: 'Escribe un motivo (por reclamo o contenido inapropiado).',
        icon: 'warning',
        input: 'text',
        inputPlaceholder: 'Ej: Reclamo de usuario / revisión pendiente',
        showCancelButton: true,
        confirmButtonText: 'Inactivar',
        cancelButtonText: 'Cancelar',
        preConfirm: (value) => {
          if (!value || !value.trim()) {
            Swal.showValidationMessage('El motivo es obligatorio.');
          }
          return value;
        }
      }).then(result => {
        if (!result.isConfirmed) return;
        form.querySelector('input[name="reason"]').value = result.value;
        form.submit();
      });
    });
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/admin/companies/index.blade.php ENDPATH**/ ?>