<?php
/**
 * Route Definitions
 * 
 * Registers all application routes with the Router instance.
 * Each route maps an HTTP method and URI pattern to a controller action.
 * 
 * @var Router $router The Router instance (provided by index.php)
 */

// Dashboard (root route)
$router->addRoute('GET', '/', ['DashboardController', 'index']);

// Health check
$router->addRoute('GET', '/health', ['HealthController', 'check']);

// Authentication routes
$router->addRoute('GET', '/login', ['AuthController', 'loginForm']);
$router->addRoute('POST', '/login', ['AuthController', 'login']);
$router->addRoute('POST', '/logout', ['AuthController', 'logout']);

// Patient routes
$router->addRoute('GET', '/patients', ['PatientController', 'index']);
$router->addRoute('GET', '/patients/create', ['PatientController', 'create']);
$router->addRoute('POST', '/patients', ['PatientController', 'store']);
$router->addRoute('GET', '/patients/{id}', ['PatientController', 'show']);
$router->addRoute('GET', '/patients/{id}/edit', ['PatientController', 'edit']);
$router->addRoute('POST', '/patients/{id}/update', ['PatientController', 'update']);

// Doctor routes
$router->addRoute('GET', '/doctors', ['DoctorController', 'index']);
$router->addRoute('GET', '/doctors/create', ['DoctorController', 'create']);
$router->addRoute('POST', '/doctors', ['DoctorController', 'store']);
$router->addRoute('GET', '/doctors/{id}', ['DoctorController', 'show']);
$router->addRoute('GET', '/doctors/{id}/edit', ['DoctorController', 'edit']);
$router->addRoute('POST', '/doctors/{id}/update', ['DoctorController', 'update']);

// Appointment routes
$router->addRoute('GET', '/appointments', ['AppointmentController', 'index']);
$router->addRoute('GET', '/appointments/create', ['AppointmentController', 'create']);
$router->addRoute('POST', '/appointments', ['AppointmentController', 'store']);
$router->addRoute('POST', '/appointments/{id}/cancel', ['AppointmentController', 'cancel']);
