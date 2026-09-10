<?php
  /**
   * Sidebar inteligente por rol.
   * - Admin: gestión total.
   * - Empresa: gestión de concursos.
   * - Usuario: participación e historial.
   *
   * Contadores (badges): se calculan en un solo lugar.
   * Si faltan migraciones o una tabla no existe todavía, atrapamos el error
   * para NO romper la vista.
   */
  $u = auth()->user();
  $role = $u?->role ?? 'guest';

  // Links por rol (sin romper rutas existentes)
  $links = [];

  if ($role === 'admin') {
    $links = [
      ['label' => 'Inicio', 'href' => url('/dashboard'), 'icon' => '🏠'],
      ['label' => 'Usuarios', 'href' => url('/admin/users'), 'icon' => '👤'],
      ['label' => 'Empresas', 'href' => url('/admin/companies'), 'icon' => '🏢'],
      ['label' => 'Concursos', 'href' => url('/admin/contests'), 'icon' => '🎯'],
      // Acceso directo para que el admin pueda CREAR concursos sin depender de una empresa
      ['label' => 'Crear concurso', 'href' => url('/admin/contests/create'), 'icon' => '➕'],
      ['label' => 'Métricas', 'href' => url('/admin/metrics'), 'icon' => '📊'],
      ['label' => 'Mensajes', 'href' => url('/admin/contact-messages'), 'icon' => '✉️'],
      ['label' => 'Reportes', 'href' => url('/admin/reports'), 'icon' => '🚩'],
      ['label' => 'Configuración', 'href' => url('/admin/settings'), 'icon' => '⚙️'],
    ];
  } elseif (in_array($role, ['company'], true)) {
    $links = [
      ['label' => 'Inicio', 'href' => url('/dashboard'), 'icon' => '🏠'],
      ['label' => 'Perfil', 'href' => url('/company/profile'), 'icon' => '🏢'],
      ['label' => 'Suscripción / Voucher', 'href' => url('/company/subscription'), 'icon' => '💳'],
      ['label' => 'Mis concursos', 'href' => url('/company/contests'), 'icon' => '🎯'],
    ];

    // Solo empresas activas y con voucher aprobado pueden crear concursos
    if ((int)($u?->active ?? 1) === 1 && (int)($u?->voucher_approved ?? 0) === 1) {
      $links[] = ['label' => 'Crear concurso', 'href' => url('/company/contests/create'), 'icon' => '➕'];
    }

    $links[] = ['label' => 'Ganadores/Premios', 'href' => url('/company/prizes'), 'icon' => '🏆'];
    $links[] = ['label' => 'Notificaciones', 'href' => url('/notifications'), 'icon' => '🔔'];
  } else { // user
    $links = [
      ['label' => 'Inicio', 'href' => url('/dashboard'), 'icon' => '🏠'],
      ['label' => 'Concursos', 'href' => url('/user/contests'), 'icon' => '🎯'],
      ['label' => 'Historial', 'href' => url('/user/history'), 'icon' => '🧾'],
      ['label' => 'Mis resultados', 'href' => url('/user/results'), 'icon' => '🏁'],
      ['label' => 'Notificaciones', 'href' => url('/notifications'), 'icon' => '🔔'],
      ['label' => 'Perfil', 'href' => route('profile.edit'), 'icon' => '🧑'],
    ];
  }

  $unread = $u?->unreadNotifications?->count() ?? 0;
?>

<aside class="dash-sidebar" id="dashSidebar">
  <div class="dash-brand">
    <a href="<?php echo e(route('home')); ?>" class="dash-brand-link">
      <span class="dash-brand-dot"></span>
      <span class="dash-brand-text">GANA FÁCIL</span>
    </a>
  </div>

  <div class="dash-user">
    <img src="<?php echo e(auth()->user()->avatar_url); ?>" class="dash-avatar" alt="Avatar">
    <div class="dash-user-meta">
      <div class="dash-user-name"><?php echo e(auth()->user()->name); ?></div>
      <div class="dash-user-role"><?php echo e(auth()->user()->role); ?></div>
    </div>
  </div>

  <nav class="dash-nav">
    <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a class="dash-link <?php echo e(request()->fullUrlIs($item['href'].'*') ? 'active' : ''); ?>" href="<?php echo e($item['href']); ?>">
        <span class="dash-ico"><?php echo e($item['icon']); ?></span>
        <span><?php echo e($item['label']); ?></span>
        <?php if($item['label'] === 'Notificaciones' && $unread > 0): ?>
          <span class="dash-badge"><?php echo e($unread); ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </nav>

  <div class="dash-sidebar-footer">
    <form method="POST" action="<?php echo e(route('logout')); ?>">
      <?php echo csrf_field(); ?>
      <button class="btn btn-sm btn-danger w-100" type="submit">Salir</button>
    </form>
  </div>
</aside>
<?php /**PATH C:\laragon\www\concursos\resources\views/partials/dashboard/sidebar.blade.php ENDPATH**/ ?>