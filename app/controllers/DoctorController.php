<?php

require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Doctor.php';

class DoctorController
{
    /**
     * Display the list of all doctors ordered alphabetically.
     * GET /doctors
     */
    public function index(): void
    {
        AuthMiddleware::handle();

        $doctors = Doctor::all();
        $success = Session::get('success');
        if ($success) {
            // Clear flash message after reading
            unset($_SESSION['success']);
        }

        require __DIR__ . '/../views/doctors/index.php';
    }

    /**
     * Display the form to create a new doctor.
     * GET /doctors/create
     */
    public function create(): void
    {
        AuthMiddleware::handle();

        $errors = [];
        $data = [];

        require __DIR__ . '/../views/doctors/create.php';
    }

    /**
     * Process the creation of a new doctor.
     * POST /doctors
     *
     * Flow:
     * 1. Get data from $_POST
     * 2. Validate with Validator::validateDoctor()
     * 3. If errors, re-render create view with errors and data
     * 4. Check duplicates (email, license_number)
     * 5. If duplicate, add to errors and re-render
     * 6. Create record, redirect to /doctors with success message
     */
    public function store(): void
    {
        AuthMiddleware::handle();

        $data = [
            'full_name'      => $_POST['full_name'] ?? '',
            'email'          => $_POST['email'] ?? '',
            'phone'          => $_POST['phone'] ?? '',
            'specialty'      => $_POST['specialty'] ?? '',
            'license_number' => $_POST['license_number'] ?? '',
        ];

        // Step 1: Validate input data
        $errors = Validator::validateDoctor($data);

        if (!empty($errors)) {
            require __DIR__ . '/../views/doctors/create.php';
            return;
        }

        // Step 2: Check for duplicates
        if (Doctor::existsByEmail($data['email'])) {
            $errors['email'] = 'Ya existe un doctor registrado con este correo electrónico.';
        }

        if (Doctor::existsByLicense($data['license_number'])) {
            $errors['license_number'] = 'Ya existe un doctor registrado con este número de licencia.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/doctors/create.php';
            return;
        }

        // Step 3: Create the doctor record
        Doctor::create($data);

        // Step 4: Set flash message and redirect
        Session::set('success', 'Doctor registrado exitosamente.');

        header('Location: /doctors');
        exit;
    }

    /**
     * Display complete details for a specific doctor.
     * GET /doctors/{id}
     */
    public function show(int $id): void
    {
        AuthMiddleware::handle();

        $doctor = Doctor::find($id);

        if (!$doctor) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        require __DIR__ . '/../views/doctors/show.php';
    }

    /**
     * Display the form to edit an existing doctor.
     * GET /doctors/{id}/edit
     */
    public function edit(int $id): void
    {
        AuthMiddleware::handle();

        $doctor = Doctor::find($id);

        if (!$doctor) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $errors = [];
        $data = $doctor;

        require __DIR__ . '/../views/doctors/edit.php';
    }

    /**
     * Process the update of an existing doctor.
     * POST /doctors/{id}/update
     *
     * Flow:
     * 1. Validate input data
     * 2. Check duplicates excluding current doctor
     * 3. Update record, redirect to /doctors/{id} with success message
     */
    public function update(int $id): void
    {
        AuthMiddleware::handle();

        $doctor = Doctor::find($id);

        if (!$doctor) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $data = [
            'full_name'      => $_POST['full_name'] ?? '',
            'email'          => $_POST['email'] ?? '',
            'phone'          => $_POST['phone'] ?? '',
            'specialty'      => $_POST['specialty'] ?? '',
            'license_number' => $_POST['license_number'] ?? '',
        ];

        // Step 1: Validate input data
        $errors = Validator::validateDoctor($data);

        if (!empty($errors)) {
            require __DIR__ . '/../views/doctors/edit.php';
            return;
        }

        // Step 2: Check for duplicates excluding current doctor
        if (Doctor::existsByEmail($data['email'], $id)) {
            $errors['email'] = 'Ya existe otro doctor registrado con este correo electrónico.';
        }

        if (Doctor::existsByLicense($data['license_number'], $id)) {
            $errors['license_number'] = 'Ya existe otro doctor registrado con este número de licencia.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/doctors/edit.php';
            return;
        }

        // Step 3: Update the doctor record
        Doctor::update($id, $data);

        // Step 4: Set flash message and redirect
        Session::set('success', 'Doctor actualizado exitosamente.');

        header('Location: /doctors/' . $id);
        exit;
    }
}
