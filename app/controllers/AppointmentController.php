<?php

require_once __DIR__ . '/../helpers/Session.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';

class AppointmentController
{
    /**
     * Display the list of all appointments sorted by date descending.
     * GET /appointments
     */
    public function index(): void
    {
        AuthMiddleware::handle();

        $appointments = Appointment::all();
        $success = Session::get('success');
        $error = Session::get('error');

        if ($success) {
            unset($_SESSION['success']);
        }
        if ($error) {
            unset($_SESSION['error']);
        }

        require __DIR__ . '/../views/appointments/index.php';
    }

    /**
     * Display the appointment creation form.
     * GET /appointments/create
     */
    public function create(): void
    {
        AuthMiddleware::handle();

        $patients = Patient::all();
        $doctors = Doctor::all();
        $errors = [];
        $data = [];

        require __DIR__ . '/../views/appointments/create.php';
    }

    /**
     * Process and store a new appointment.
     * POST /appointments
     *
     * Flow:
     * 1. Get data from $_POST
     * 2. Validate with Validator::validateAppointment()
     * 3. If date is today, check that time is not in the past
     * 4. Check doctor conflict
     * 5. Check patient conflict
     * 6. If errors, re-render create view with errors and data
     * 7. Create appointment, redirect to /appointments with success message
     */
    public function store(): void
    {
        AuthMiddleware::handle();

        $data = [
            'patient_id'       => $_POST['patient_id'] ?? '',
            'doctor_id'        => $_POST['doctor_id'] ?? '',
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'appointment_time' => $_POST['appointment_time'] ?? '',
        ];

        // Step 1: Validate input data
        $errors = Validator::validateAppointment($data);

        // Step 2: If date is today, check that time is not in the past
        if (empty($errors['appointment_date']) && empty($errors['appointment_time'])) {
            $today = date('Y-m-d');
            if ($data['appointment_date'] === $today) {
                $currentTime = date('H:i');
                if ($data['appointment_time'] < $currentTime) {
                    $errors['appointment_time'] = 'La hora de la cita no puede ser en el pasado.';
                }
            }
        }

        // Step 3: Check for conflicts (only if no prior validation errors on fields)
        if (empty($errors)) {
            if (Appointment::hasConflictForDoctor((int) $data['doctor_id'], $data['appointment_date'], $data['appointment_time'])) {
                $errors['doctor_id'] = 'El doctor no está disponible en el horario seleccionado.';
            }

            if (Appointment::hasConflictForPatient((int) $data['patient_id'], $data['appointment_date'], $data['appointment_time'])) {
                $errors['patient_id'] = 'El paciente ya tiene una cita programada en ese horario.';
            }
        }

        // Step 4: If errors, re-render create view
        if (!empty($errors)) {
            $patients = Patient::all();
            $doctors = Doctor::all();
            require __DIR__ . '/../views/appointments/create.php';
            return;
        }

        // Step 5: Create the appointment
        Appointment::create($data);

        Session::set('success', 'Cita programada exitosamente.');

        header('Location: /appointments');
        exit;
    }

    /**
     * Cancel a scheduled appointment.
     * POST /appointments/{id}/cancel
     *
     * Flow:
     * 1. Find appointment by ID
     * 2. If not found, return 404
     * 3. If already cancelled, set error flash and redirect
     * 4. Cancel appointment, set success flash, redirect
     */
    public function cancel(int $id): void
    {
        AuthMiddleware::handle();

        $appointment = Appointment::find($id);

        if (!$appointment) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        if ($appointment['status'] === 'cancelled') {
            Session::set('error', 'La cita ya está cancelada.');
            header('Location: /appointments');
            exit;
        }

        Appointment::cancel($id);

        Session::set('success', 'Cita cancelada exitosamente.');

        header('Location: /appointments');
        exit;
    }
}
