<?php
http_response_code(404);

$title = 'Page Not Found - Smart Clinic';

ob_start();
?>
<div class="text-center py-5">
    <h1 class="display-1 text-muted">404</h1>
    <h2 class="mb-3">Page Not Found</h2>
    <p class="text-muted mb-4">The page you are looking for does not exist or has been moved.</p>
    <a href="/" class="btn btn-primary">Back to Home</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
