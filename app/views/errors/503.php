<?php
http_response_code(503);

$title = 'Service Unavailable - Smart Clinic';

ob_start();
?>
<div class="text-center py-5">
    <h1 class="display-1 text-muted">503</h1>
    <h2 class="mb-3">Service Unavailable</h2>
    <p class="text-muted mb-4">The service is temporarily unavailable. Please try again later.</p>
    <a href="/" class="btn btn-primary">Back to Home</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
