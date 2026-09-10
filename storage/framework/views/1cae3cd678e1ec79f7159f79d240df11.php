<?php $__env->startSection('content'); ?>
<div class="container py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Importar preguntas</h3>
            <div class="text-muted small">Trivia: <b><?php echo e($contest->title); ?></b></div>
        </div>

        <a href="<?php echo e(route($routeBase . 'trivia.questions.index', $contest)); ?>" class="btn btn-outline-secondary btn-sm btn-nav">
            ← Volver a preguntas
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <form method="POST" action="<?php echo e(route($routeBase . 'trivia.questions.import', $contest)); ?>" enctype="multipart/form-data" class="card shadow-sm">
                <?php echo csrf_field(); ?>

                <div class="card-body">
                    <div class="alert alert-info">
                        Puedes importar preguntas desde <b>CSV</b> o <b>Excel</b> (<code>.xlsx</code>/<code>.xls</code>).
                        <div class="small mt-1">
                            ✅ Excel debe tener encabezados en la fila 1: <code>pregunta, opcion_a, opcion_b, opcion_c, opcion_d, opcion_correcta</code>.
                        </div>
                    </div>

                    <?php if(!empty($companies)): ?>
                        <div class="mb-3">
                            <label class="form-label">Asignar a empresa</label>
                            <select name="company_id" class="form-select" required>
                                <option value="">-- Selecciona una empresa --</option>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>">
                                        <?php echo e($c->company_name ?: $c->name); ?>

                                        <?php if(!empty($c->razon_social)): ?> - <?php echo e($c->razon_social); ?> <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text">Solo aplica si el concurso no tiene empresa asignada.</div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Archivo</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls,text/csv" required>
                        <div class="form-text">CSV puede venir con o sin encabezados. Excel debe tener encabezados.</div>
                    </div>

                    <button class="btn btn-success">📥 Cargar y previsualizar</button>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-2">Plantilla</h6>
                    <p class="text-muted small mb-2">Descarga una plantilla lista para llenar.</p>
                    <a class="btn btn-outline-primary" href="<?php echo e(route($routeBase . 'trivia.questions.template', $contest)); ?>">
                        Descargar plantilla CSV
                    </a>

                    <hr>

                    <h6 class="mb-2">Ejemplo de columnas</h6>
                    <div class="small">
                        <div><b>pregunta</b>: ¿Cuánto es 2 + 2?</div>
                        <div><b>opcion_a</b>: 3</div>
                        <div><b>opcion_b</b>: 4</div>
                        <div><b>opcion_c</b>: 5</div>
                        <div><b>opcion_d</b>: 6</div>
                        <div><b>opcion_correcta</b>: B</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\concursos\resources\views/shared/trivia/questions/import.blade.php ENDPATH**/ ?>