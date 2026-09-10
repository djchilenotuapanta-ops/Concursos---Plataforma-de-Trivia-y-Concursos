<?php
  /**
   * ✅ Vista ÚNICA para crear concursos
   * La usan: ADMIN, MODERATOR y EMPRESA.
   *
   * - Admin/Moderator: muestran selector de empresa (si viene $companies)
   * - Empresa: no muestra selector de empresa
   *
   * Puedes sobreescribir estas variables desde el controller si lo necesitas:
   * - $backRoute, $storeRoute, $pageTitle, $subtitle
   */

  $user = auth()->user();

  $role = strtolower(trim((string) optional($user)->role));
  $role = preg_replace('/\s+/', '', $role);
  $role = match ($role) {
    'administrator','administrador','admin' => 'admin',
    'moderador','moderator' => 'moderator',
    'empresa','company' => 'company',
    default => $role,
  };

  $isAdminArea = in_array($role, ['admin','moderator'], true);

  $backRoute  = $backRoute  ?? ($isAdminArea ? 'admin.contests_adv.index' : 'company.contests.index');
  $storeRoute = $storeRoute ?? ($isAdminArea ? 'admin.contests_adv.store' : 'company.contests.store');

  $pageTitle  = $pageTitle  ?? 'Crear concurso';
  $subtitle   = $subtitle   ?? 'Crea una TRIVIA usando el flujo de 2 pasos: primero datos básicos, luego preguntas, reglas y premio.';
?>



<?php $__env->startSection('content'); ?>
  <?php echo $__env->make('contests._create_form', [
    'backRoute' => $backRoute,
    'storeRoute' => $storeRoute,
    'pageTitle' => $pageTitle,
    'subtitle' => $subtitle,
    'companies' => $companies ?? null,
  ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/contests/create.blade.php ENDPATH**/ ?>