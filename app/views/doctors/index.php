<?php
$title = 'Doctores';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Doctores</h1>
    <a href="/doctors/create" class="btn btn-primary">Nuevo Doctor</a>
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
                <th>Especialidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['full_name']) ?></td>
                        <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                        <td>
                            <a href="/doctors/<?= (int)$doctor['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="/doctors/<?= (int)$doctor['id'] ?>/edit" class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">No hay doctores registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
