<?php
$title = 'Citas Médicas';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Citas Médicas</h1>
    <a href="/appointments/create" class="btn btn-primary">Nueva Cita</a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Paciente</th>
                <th>Doctor</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_time']) ?></td>
                        <td>
                            <?php if ($appointment['status'] === 'scheduled'): ?>
                                <span class="badge text-bg-success">Programada</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Cancelada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($appointment['status'] === 'scheduled'): ?>
                                <form action="/appointments/<?= (int)$appointment['id'] ?>/cancel" method="POST" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">No hay citas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
