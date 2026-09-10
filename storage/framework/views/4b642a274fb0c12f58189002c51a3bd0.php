<?php
  /**
   * Barra superior (Topbar)
   *
   * Objetivo:
   * - Hacer la navegación más clara en TODO el sistema.
   * - Incluir un botón de "Volver" (a la pantalla anterior) y otro para regresar al "Panel".
   * - Mantener un estilo consistente con Bootstrap + dashboard.css.
   */

  // Volver seguro: si el "previous" viene de otra web, regresamos al panel.
  $prev = url()->previous();
  $prevHost = parse_url($prev, PHP_URL_HOST);
  $sameHost = empty($prevHost) || $prevHost === request()->getHost();
  $backHref = $sameHost ? $prev : route('dashboard');
?>

<header class="dash-topbar">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm btn-outline-secondary d-lg-none btn-nav" type="button" id="sidebarToggle" aria-label="Menú">
      ☰
    </button>

    <a class="btn btn-sm btn-outline-secondary btn-nav" href="<?php echo e($backHref); ?>" title="Volver">
      ← Volver
    </a>

    <a class="btn btn-sm btn-outline-secondary btn-nav" href="<?php echo e(route('dashboard')); ?>" title="Ir al Panel">
      🧭 Panel
    </a>

    <div class="dash-crumbs d-none d-md-block">
      <a href="<?php echo e(route('home')); ?>" class="dash-crumb">Home</a>
      <span class="dash-crumb-sep">/</span>
      <span class="dash-crumb-current"><?php echo $__env->yieldContent('title','Panel'); ?></span>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <a class="btn btn-sm btn-outline-primary btn-nav" href="<?php echo e(url('/notifications')); ?>" title="Notificaciones">🔔</a>
    <a class="btn btn-sm btn-outline-secondary btn-nav" href="<?php echo e(route('profile.edit')); ?>">Perfil</a>
  </div>
</header>
<?php /**PATH C:\laragon\www\concursos\resources\views/partials/dashboard/topbar.blade.php ENDPATH**/ ?>