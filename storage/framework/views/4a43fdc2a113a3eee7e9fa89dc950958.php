<?php $__env->startSection('title', '500 - Error del servidor'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-page">
    <div class="error-container">
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Error del servidor</h2>
        <p class="error-message">
            Ha ocurrido un error inesperado. Nuestro equipo ha sido notificado.
        </p>
        <div class="error-actions">
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                Volver al inicio
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                Regresar
            </a>
        </div>
    </div>
</div>

<style>
.error-page {
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.error-container {
    text-align: center;
    max-width: 600px;
}

.error-code {
    font-size: 8rem;
    font-weight: bold;
    color: #c0392b;
    margin: 0;
    line-height: 1;
}

.error-title {
    font-size: 2rem;
    margin: 1rem 0;
    color: #333;
}

.error-message {
    font-size: 1.1rem;
    color: #666;
    margin: 1.5rem 0;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/errors/500.blade.php ENDPATH**/ ?>