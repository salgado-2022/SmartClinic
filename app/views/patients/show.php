<?php
$title = 'Detalle del Paciente';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detalle del Paciente</h1>
    <div>
        <a href="/patients/<?= (int)$patient['id'] ?>/edit" class="btn btn-warning">Editar</a>
        <a href="/patients" class="btn btn-secondary">Volver</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-4">Nombre Completo</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($patient['full_name']) ?></dd>

            <dt class="col-sm-4">Fecha de Nacimiento</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($patient['date_of_birth']) ?></dd>

            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($patient['email']) ?></dd>

            <dt class="col-sm-4">Teléfono</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($patient['phone']) ?></dd>

            <dt class="col-sm-4">Documento de Identidad</dt>
            <dd class="col-sm-8"><?= htmlspecialchars($patient['id_document']) ?></dd>
        </dl>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
