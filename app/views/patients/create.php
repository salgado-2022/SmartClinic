<?php
$title = 'Nuevo Paciente';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Nuevo Paciente</h1>
    <a href="/patients" class="btn btn-secondary">Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/patients" method="POST" novalidate>
            <div class="mb-3">
                <label for="full_name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control <?= !empty($errors['full_name']) ? 'is-invalid' : '' ?>"
                       id="full_name" name="full_name"
                       value="<?= htmlspecialchars($data['full_name'] ?? '') ?>" required>
                <?php if (!empty($errors['full_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control <?= !empty($errors['date_of_birth']) ? 'is-invalid' : '' ?>"
                       id="date_of_birth" name="date_of_birth"
                       value="<?= htmlspecialchars($data['date_of_birth'] ?? '') ?>" required>
                <?php if (!empty($errors['date_of_birth'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['date_of_birth']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                       id="email" name="email"
                       value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono</label>
                <input type="text" class="form-control <?= !empty($errors['phone']) ? 'is-invalid' : '' ?>"
                       id="phone" name="phone"
                       value="<?= htmlspecialchars($data['phone'] ?? '') ?>" required>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="id_document" class="form-label">Documento de Identidad</label>
                <input type="text" class="form-control <?= !empty($errors['id_document']) ? 'is-invalid' : '' ?>"
                       id="id_document" name="id_document"
                       value="<?= htmlspecialchars($data['id_document'] ?? '') ?>" required>
                <?php if (!empty($errors['id_document'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['id_document']) ?></div>
                <?php endif; ?>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Registrar Paciente</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>
