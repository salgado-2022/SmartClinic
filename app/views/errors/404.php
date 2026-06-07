<?php
http_response_code(404);

$title = 'Página No Encontrada - Smart Clinic';

ob_start();
?>
<div class="text-center py-5">
    <h1 class="display-1 text-muted">404</h1>
    <h2 class="mb-3">Página No Encontrada</h2>
    <p class="text-muted mb-4">La página que buscas no existe o ha sido movida.</p>
    <a href="/" class="btn btn-primary">Volver al Inicio</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
