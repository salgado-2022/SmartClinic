<?php
$title = 'Pacientes';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Pacientes</h1>
    <a href="/patients/create" class="btn btn-primary">Nuevo Paciente</a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nombre Completo</th>
                <th>Documento</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['full_name']) ?></td>
                        <td><?= htmlspecialchars($patient['id_document']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['phone']) ?></td>
                        <td>
                            <a href="/patients/<?= (int)$patient['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="/patients/<?= (int)$patient['id'] ?>/edit" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No hay pacientes registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
