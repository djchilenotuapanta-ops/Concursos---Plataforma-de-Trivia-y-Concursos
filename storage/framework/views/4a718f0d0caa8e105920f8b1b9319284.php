<?php $__env->startSection('content'); ?>
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Seleccionar preguntas a importar</h3>
            <div class="text-muted small">Trivia: <b><?php echo e($contest->title); ?></b> - Marca las preguntas que deseas importar.</div>
        </div>

        <div class="d-flex gap-2">
            <a href="<?php echo e(route($routeBase . 'trivia.questions.import.form', $contest)); ?>" class="btn btn-outline-secondary btn-sm btn-nav">← Volver</a>
            <a href="<?php echo e(route($routeBase . 'trivia.questions.index', $contest)); ?>" class="btn btn-outline-primary btn-sm">Ver preguntas</a>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route($routeBase . 'trivia.questions.import.commit', $contest)); ?>" class="card shadow-sm">
        <?php echo csrf_field(); ?>

        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="text-muted small">Total cargadas: <b><?php echo e(count($payloads ?? [])); ?></b></div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAll(true)">Marcar todo</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(false)">Desmarcar</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width:60px;">Importar</th>
                            <th>Pregunta</th>
                            <th>Opciones</th>
                            <th style="width:90px;">Correcta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = ($payloads ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input class="form-check-input jsSelect" type="checkbox" name="selected[]" value="<?php echo e($idx); ?>" checked>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo e($p['question'] ?? '—'); ?></div>
                                </td>
                                <td class="small">
                                    <div><b>A)</b> <?php echo e($p['option_a'] ?? '—'); ?></div>
                                    <div><b>B)</b> <?php echo e($p['option_b'] ?? '—'); ?></div>
                                    <?php if(!empty($p['option_c'])): ?><div><b>C)</b> <?php echo e($p['option_c']); ?></div><?php endif; ?>
                                    <?php if(!empty($p['option_d'])): ?><div><b>D)</b> <?php echo e($p['option_d']); ?></div><?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo e($p['correct_option'] ?? '—'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex gap-2">
            <button type="submit" class="btn btn-primary">📥 Importar seleccionadas</button>
            <a href="<?php echo e(route($routeBase . 'trivia.questions.index', $contest)); ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
function toggleAll(state) {
    document.querySelectorAll('.jsSelect').forEach(el => el.checked = !!state);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/shared/trivia/questions/import_select.blade.php ENDPATH**/ ?>