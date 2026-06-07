<?php

require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Patient.php';

class PatientController
{
    /**
     * Display the list of all patients.
     * GET /patients
     */
    public function index(): void
    {
        AuthMiddleware::handle();

        $patients = Patient::all();
        require __DIR__ . '/../views/patients/index.php';
    }

    /**
     * Display the patient creation form.
     * GET /patients/create
     */
    public function create(): void
    {
        AuthMiddleware::handle();

        $errors = [];
        $data = [];
        require __DIR__ . '/../views/patients/create.php';
    }

    /**
     * Process and store a new patient record.
     * POST /patients
     *
     * Flow:
     * 1. Get data from $_POST
     * 2. Validate with Validator::validatePatient()
     * 3. If errors, re-render create view with errors and preserved form data
     * 4. Check duplicates (email and id_document)
     * 5. If duplicate, add to errors and re-render
     * 6. Create patient, redirect to /patients with success message
     */
    public function store(): void
    {
        AuthMiddleware::handle();

        $data = [
            'full_name'     => $_POST['full_name'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'phone'         => $_POST['phone'] ?? '',
            'id_document'   => $_POST['id_document'] ?? '',
        ];

        // Step 1: Validate input data
        $errors = Validator::validatePatient($data);

        if (!empty($errors)) {
            require __DIR__ . '/../views/patients/create.php';
            return;
        }

        // Step 2: Check for duplicates
        if (Patient::existsByEmail($data['email'])) {
            $errors['email'] = 'Ya existe un paciente registrado con este correo electrónico.';
        }

        if (Patient::existsByDocument($data['id_document'])) {
            $errors['id_document'] = 'Ya existe un paciente registrado con este documento de identidad.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/patients/create.php';
            return;
        }

        // Step 3: Create the patient record
        Patient::create($data);

        // Store success message in session for flash notification
        Session::set('success', 'Paciente registrado exitosamente.');

        header('Location: /patients');
        exit;
    }

    /**
     * Display a single patient record.
     * GET /patients/{id}
     */
    public function show(int $id): void
    {
        AuthMiddleware::handle();

        $patient = Patient::find($id);

        if (!$patient) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        require __DIR__ . '/../views/patients/show.php';
    }

    /**
     * Display the patient edit form.
     * GET /patients/{id}/edit
     */
    public function edit(int $id): void
    {
        AuthMiddleware::handle();

        $patient = Patient::find($id);

        if (!$patient) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $errors = [];
        $data = $patient;
        require __DIR__ . '/../views/patients/edit.php';
    }

    /**
     * Process and update an existing patient record.
     * POST /patients/{id}/update
     *
     * Flow:
     * 1. Validate input data
     * 2. Check duplicates excluding current patient ID
     * 3. Update patient, redirect to /patients/{id} with success message
     */
    public function update(int $id): void
    {
        AuthMiddleware::handle();

        $patient = Patient::find($id);

        if (!$patient) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        $data = [
            'full_name'     => $_POST['full_name'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? '',
            'email'         => $_POST['email'] ?? '',
            'phone'         => $_POST['phone'] ?? '',
            'id_document'   => $_POST['id_document'] ?? '',
        ];

        // Step 1: Validate input data
        $errors = Validator::validatePatient($data);

        if (!empty($errors)) {
            require __DIR__ . '/../views/patients/edit.php';
            return;
        }

        // Step 2: Check for duplicates excluding current patient
        if (Patient::existsByEmail($data['email'], $id)) {
            $errors['email'] = 'Ya existe otro paciente registrado con este correo electrónico.';
        }

        if (Patient::existsByDocument($data['id_document'], $id)) {
            $errors['id_document'] = 'Ya existe otro paciente registrado con este documento de identidad.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/patients/edit.php';
            return;
        }

        // Step 3: Update the patient record
        Patient::update($id, $data);

        // Store success message in session for flash notification
        Session::set('success', 'Paciente actualizado exitosamente.');

        header('Location: /patients/' . $id);
        exit;
    }
}
