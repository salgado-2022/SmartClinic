<?php
http_response_code(503);

$title = 'Servicio No Disponible - Smart Clinic';

ob_start();
?>
<div class="text-center py-5">
    <h1 class="display-1 text-muted">503</h1>
    <h2 class="mb-3">Servicio No Disponible</h2>
    <p class="text-muted mb-4">El servicio no está disponible temporalmente. Por favor, intente de nuevo más tarde.</p>
    <a href="/" class="btn btn-primary">Volver al Inicio</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
