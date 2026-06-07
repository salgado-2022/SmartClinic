<?php

class Validator
{
    /**
     * Validate patient data.
     *
     * @param array $data Patient form data
     * @return array Associative array of field => error message. Empty if valid.
     */
    public static function validatePatient(array $data): array
    {
        $errors = [];

        // full_name: required, 2-100 characters
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'El nombre completo es obligatorio.';
        } elseif (strlen(trim($data['full_name'])) < 2) {
            $errors['full_name'] = 'El nombre completo debe tener al menos 2 caracteres.';
        } elseif (strlen(trim($data['full_name'])) > 100) {
            $errors['full_name'] = 'El nombre completo no debe exceder 100 caracteres.';
        }

        // date_of_birth: required, valid date, not in the future
        if (empty($data['date_of_birth'])) {
            $errors['date_of_birth'] = 'La fecha de nacimiento es obligatoria.';
        } else {
            $dob = date_create($data['date_of_birth']);
            if (!$dob) {
                $errors['date_of_birth'] = 'La fecha de nacimiento no tiene un formato válido.';
            } elseif ($dob > new DateTime('today')) {
                $errors['date_of_birth'] = 'La fecha de nacimiento no puede ser en el futuro.';
            }
        }

        // email: required, valid format, max 150 chars
        if (empty($data['email'])) {
            $errors['email'] = 'El correo electrónico es obligatorio.';
        } elseif (strlen($data['email']) > 150) {
            $errors['email'] = 'El correo electrónico no debe exceder 150 caracteres.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no tiene un formato válido.';
        }

        // phone: required, 7-15 digits only
        if (empty($data['phone'])) {
            $errors['phone'] = 'El teléfono es obligatorio.';
        } elseif (!preg_match('/^\d{7,15}$/', $data['phone'])) {
            $errors['phone'] = 'El teléfono debe contener entre 7 y 15 dígitos numéricos.';
        }

        // id_document: required, 5-20 alphanumeric characters
        if (empty($data['id_document'])) {
            $errors['id_document'] = 'El documento de identidad es obligatorio.';
        } elseif (!preg_match('/^[a-zA-Z0-9]{5,20}$/', $data['id_document'])) {
            $errors['id_document'] = 'El documento de identidad debe tener entre 5 y 20 caracteres alfanuméricos.';
        }

        return $errors;
    }

    /**
     * Validate doctor data.
     *
     * @param array $data Doctor form data
     * @return array Associative array of field => error message. Empty if valid.
     */
    public static function validateDoctor(array $data): array
    {
        $errors = [];

        // full_name: required, 3-100 characters
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'El nombre completo es obligatorio.';
        } elseif (strlen(trim($data['full_name'])) < 3) {
            $errors['full_name'] = 'El nombre completo debe tener al menos 3 caracteres.';
        } elseif (strlen(trim($data['full_name'])) > 100) {
            $errors['full_name'] = 'El nombre completo no debe exceder 100 caracteres.';
        }

        // email: required, valid format, max 100 chars
        if (empty($data['email'])) {
            $errors['email'] = 'El correo electrónico es obligatorio.';
        } elseif (strlen($data['email']) > 100) {
            $errors['email'] = 'El correo electrónico no debe exceder 100 caracteres.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no tiene un formato válido.';
        }

        // phone: required, 7-15 digits only
        if (empty($data['phone'])) {
            $errors['phone'] = 'El teléfono es obligatorio.';
        } elseif (!preg_match('/^\d{7,15}$/', $data['phone'])) {
            $errors['phone'] = 'El teléfono debe contener entre 7 y 15 dígitos numéricos.';
        }

        // specialty: required, 3-100 characters
        if (empty($data['specialty'])) {
            $errors['specialty'] = 'La especialidad es obligatoria.';
        } elseif (strlen(trim($data['specialty'])) < 3) {
            $errors['specialty'] = 'La especialidad debe tener al menos 3 caracteres.';
        } elseif (strlen(trim($data['specialty'])) > 100) {
            $errors['specialty'] = 'La especialidad no debe exceder 100 caracteres.';
        }

        // license_number: required, 4-20 alphanumeric characters
        if (empty($data['license_number'])) {
            $errors['license_number'] = 'El número de licencia es obligatorio.';
        } elseif (!preg_match('/^[a-zA-Z0-9]{4,20}$/', $data['license_number'])) {
            $errors['license_number'] = 'El número de licencia debe tener entre 4 y 20 caracteres alfanuméricos.';
        }

        return $errors;
    }

    /**
     * Validate appointment data.
     *
     * @param array $data Appointment form data
     * @return array Associative array of field => error message. Empty if valid.
     */
    public static function validateAppointment(array $data): array
    {
        $errors = [];

        // patient_id: required, must be numeric
        if (empty($data['patient_id'])) {
            $errors['patient_id'] = 'El paciente es obligatorio.';
        } elseif (!is_numeric($data['patient_id'])) {
            $errors['patient_id'] = 'El paciente seleccionado no es válido.';
        }

        // doctor_id: required, must be numeric
        if (empty($data['doctor_id'])) {
            $errors['doctor_id'] = 'El doctor es obligatorio.';
        } elseif (!is_numeric($data['doctor_id'])) {
            $errors['doctor_id'] = 'El doctor seleccionado no es válido.';
        }

        // appointment_date: required, valid date, must be today or in the future
        if (empty($data['appointment_date'])) {
            $errors['appointment_date'] = 'La fecha de la cita es obligatoria.';
        } else {
            $appointmentDate = date_create($data['appointment_date']);
            if (!$appointmentDate) {
                $errors['appointment_date'] = 'La fecha de la cita no tiene un formato válido.';
            } elseif ($appointmentDate < new DateTime('today')) {
                $errors['appointment_date'] = 'La fecha de la cita no puede ser en el pasado.';
            }
        }

        // appointment_time: required, valid time format, between 08:00 and 18:00
        if (empty($data['appointment_time'])) {
            $errors['appointment_time'] = 'La hora de la cita es obligatoria.';
        } elseif (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $data['appointment_time'])) {
            $errors['appointment_time'] = 'La hora de la cita no tiene un formato válido (HH:MM).';
        } else {
            $time = $data['appointment_time'];
            if ($time < '08:00' || $time > '18:00') {
                $errors['appointment_time'] = 'La hora de la cita debe estar entre las 08:00 y las 18:00.';
            }
        }

        return $errors;
    }

    /**
     * Validate login credentials.
     *
     * @param array $data Credentials form data
     * @return array Associative array of field => error message. Empty if valid.
     */
    public static function validateCredentials(array $data): array
    {
        $errors = [];

        // email: required, valid format
        if (empty($data['email'])) {
            $errors['email'] = 'El correo electrónico es obligatorio.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no tiene un formato válido.';
        }

        // password: required, minimum 8 characters
        if (empty($data['password'])) {
            $errors['password'] = 'La contraseña es obligatoria.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        return $errors;
    }
}
