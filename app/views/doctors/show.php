<?php
$title = 'Detalle del Doctor';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detalle del Doctor</h1>
    <div>
        <a href="/doctors/<?= (int)$doctor['id'] ?>/edit" class="btn btn-warning">Editar</a>
        <a href="/doctors" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nombre Completo</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($doctor['full_name']) ?></dd>

            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($doctor['email']) ?></dd>

            <dt class="col-sm-3">Teléfono</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($doctor['phone']) ?></dd>

            <dt class="col-sm-3">Especialidad</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($doctor['specialty']) ?></dd>

            <dt class="col-sm-3">Número de Licencia</dt>
            <dd class="col-sm-9"><?= htmlspecialchars($doctor['license_number']) ?></dd>
        </dl>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
