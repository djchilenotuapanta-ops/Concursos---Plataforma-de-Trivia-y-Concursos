<?php $__env->startSection('title', 'Iniciar sesión'); ?>

<?php $__env->startSection('content'); ?>
  <div class="auth-head">
    <h1 class="auth-brand">GANA FÁCIL</h1>
    <p class="auth-sub">Inicia sesión para participar en trivias y concursos.</p>
  </div>

  <div class="auth-body">
    <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <?php if(session('status')): ?>
      <div class="auth-alert"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login')); ?>" class="auth-grid" novalidate>
      <?php echo csrf_field(); ?>

      <div>
        <div class="auth-label">
          <span>Correo electrónico</span>
          <span class="auth-hint">Obligatorio</span>
        </div>
        <input
          type="email"
          name="email"
          class="auth-input"
          value="<?php echo e(old('email')); ?>"
          autocomplete="off"
          required
        >
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="auth-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div>
        <div class="auth-label">
          <span>Contraseña</span>
          <span class="auth-hint">Obligatorio</span>
        </div>

        <div class="d-flex gap-2">
          <input
            id="password"
            type="password"
            name="password"
            class="auth-input"
            autocomplete="new-password"
            required
          >
          <button class="pw-btn" type="button" data-password-toggle="#password" title="Mostrar/Ocultar">👁️</button>
        </div>

        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="auth-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="auth-row">
        <label class="auth-check">
          <input type="checkbox" name="remember" value="1">
          <span>Recordarme</span>
        </label>

        <?php if(Route::has('password.request')): ?>
          <a class="auth-link" href="<?php echo e(route('password.request')); ?>">¿Olvidaste tu contraseña?</a>
        <?php endif; ?>
      </div>

      <button type="submit" class="auth-btn">Iniciar sesión</button>
    </form>
  </div>

  <div class="auth-foot">
    ¿No tienes cuenta? <a class="auth-link" href="<?php echo e(route('register')); ?>">Regístrate</a>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/auth/login.blade.php ENDPATH**/ ?>