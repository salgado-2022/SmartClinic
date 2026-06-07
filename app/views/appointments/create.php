<?php
$title = 'Nueva Cita';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Nueva Cita</h1>
    <a href="/appointments" class="btn btn-secondary">Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/appointments" method="POST" novalidate>
            <div class="mb-3">
                <label for="patient_id" class="form-label">Paciente</label>
                <select class="form-select <?= !empty($errors['patient_id']) ? 'is-invalid' : '' ?>"
                        id="patient_id" name="patient_id" required>
                    <option value="">Seleccione un paciente</option>
                    <?php if (!empty($patients)): ?>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= (int)$patient['id'] ?>"
                                <?= (isset($data['patient_id']) && $data['patient_id'] == $patient['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($patient['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (!empty($errors['patient_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['patient_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="doctor_id" class="form-label">Doctor</label>
                <select class="form-select <?= !empty($errors['doctor_id']) ? 'is-invalid' : '' ?>"
                        id="doctor_id" name="doctor_id" required>
                    <option value="">Seleccione un doctor</option>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= (int)$doctor['id'] ?>"
                                <?= (isset($data['doctor_id']) && $data['doctor_id'] == $doctor['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doctor['full_name'] . ' - ' . $doctor['specialty']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (!empty($errors['doctor_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['doctor_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="appointment_date" class="form-label">Fecha</label>
                <input type="date" class="form-control <?= !empty($errors['appointment_date']) ? 'is-invalid' : '' ?>"
                       id="appointment_date" name="appointment_date"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($data['appointment_date'] ?? '') ?>" required>
                <?php if (!empty($errors['appointment_date'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['appointment_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="appointment_time" class="form-label">Hora</label>
                <input type="time" class="form-control <?= !empty($errors['appointment_time']) ? 'is-invalid' : '' ?>"
                       id="appointment_time" name="appointment_time"
                       min="08:00" max="18:00"
                       value="<?= htmlspecialchars($data['appointment_time'] ?? '') ?>" required>
                <?php if (!empty($errors['appointment_time'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['appointment_time']) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Agendar Cita</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
