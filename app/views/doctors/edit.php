<?php
$title = 'Editar Doctor';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Editar Doctor</h1>
    <a href="/doctors" class="btn btn-secondary">Volver</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="/doctors/<?= (int)$doctor['id'] ?>/update" method="POST" novalidate>
            <div class="mb-3">
                <label for="full_name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control <?= !empty($errors['full_name']) ? 'is-invalid' : '' ?>"
                       id="full_name" name="full_name"
                       value="<?= htmlspecialchars($data['full_name'] ?? $doctor['full_name']) ?>">
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                       id="email" name="email"
                       value="<?= htmlspecialchars($data['email'] ?? $doctor['email']) ?>">
                <?php if (!empty($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" class="form-control <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                       id="phone" name="phone"
                       value="<?= htmlspecialchars($data['phone'] ?? $doctor['phone']) ?>">
                <?php if (!empty($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="specialty" class="form-label">Especialidad</label>
                <input type="text" class="form-control <?= !empty($errors['specialty']) ? 'is-invalid' : '' ?>"
                       id="specialty" name="specialty"
                       value="<?= htmlspecialchars($data['specialty'] ?? $doctor['specialty']) ?>">
                <?php if (!empty($errors['specialty'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['specialty']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="license_number" class="form-label">Número de Licencia</label>
                <input type="text" class="form-control <?= !empty($errors['license_number']) ? 'is-invalid' : '' ?>"
                       id="license_number" name="license_number"
                       value="<?= htmlspecialchars($data['license_number'] ?? $doctor['license_number']) ?>">
                <?php if (!empty($errors['license_number'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['license_number']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Doctor</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
