<?php

require_once __DIR__ . '/../config/database.php';

class Appointment
{
    /**
     * Retrieve all appointments with patient and doctor names, ordered by date and time descending.
     *
     * @return array List of appointment records with joined patient/doctor names
     */
    public static function all(): array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->query(
            'SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name
             FROM appointments a
             JOIN patients p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             ORDER BY a.appointment_date DESC, a.appointment_time DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Find an appointment by its ID, including patient and doctor names.
     *
     * @param int $id Appointment ID
     * @return array|null Appointment record or null if not found
     */
    public static function find(int $id): ?array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name
             FROM appointments a
             JOIN patients p ON a.patient_id = p.id
             JOIN doctors d ON a.doctor_id = d.id
             WHERE a.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $appointment = $stmt->fetch();

        return $appointment ?: null;
    }

    /**
     * Create a new appointment with status "scheduled".
     *
     * @param array $data Associative array with keys: patient_id, doctor_id, appointment_date, appointment_time
     * @return int The ID of the newly created appointment
     */
    public static function create(array $data): int
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status)
             VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :status)'
        );
        $stmt->execute([
            'patient_id'       => $data['patient_id'],
            'doctor_id'        => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'status'           => 'scheduled',
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Cancel an appointment by setting its status to "cancelled".
     *
     * @param int $id Appointment ID
     * @return bool True if the update affected at least one row
     */
    public static function cancel(int $id): bool
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'UPDATE appointments SET status = :status WHERE id = :id'
        );
        $stmt->execute([
            'status' => 'cancelled',
            'id'     => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a 30-minute time slot conflicts with existing non-cancelled appointments for a doctor.
     *
     * Two appointments conflict if the absolute difference between their times is less than 30 minutes
     * (1800 seconds), meaning their 30-minute slots overlap.
     *
     * @param int      $doctorId  Doctor ID to check
     * @param string   $date      Appointment date (Y-m-d)
     * @param string   $time      Appointment time (H:i or H:i:s)
     * @param int|null $excludeId Optional appointment ID to exclude from the check
     * @return bool True if a conflict exists
     */
    public static function hasConflictForDoctor(int $doctorId, string $date, string $time, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        $sql = 'SELECT COUNT(*) FROM appointments
                WHERE doctor_id = :doctor_id
                  AND appointment_date = :appointment_date
                  AND status != :status
                  AND ABS(TIME_TO_SEC(TIMEDIFF(appointment_time, :appointment_time))) < 1800';

        $params = [
            'doctor_id'        => $doctorId,
            'appointment_date' => $date,
            'status'           => 'cancelled',
            'appointment_time' => $time,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Check if a 30-minute time slot conflicts with existing non-cancelled appointments for a patient.
     *
     * Two appointments conflict if the absolute difference between their times is less than 30 minutes
     * (1800 seconds), meaning their 30-minute slots overlap.
     *
     * @param int      $patientId Patient ID to check
     * @param string   $date      Appointment date (Y-m-d)
     * @param string   $time      Appointment time (H:i or H:i:s)
     * @param int|null $excludeId Optional appointment ID to exclude from the check
     * @return bool True if a conflict exists
     */
    public static function hasConflictForPatient(int $patientId, string $date, string $time, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        $sql = 'SELECT COUNT(*) FROM appointments
                WHERE patient_id = :patient_id
                  AND appointment_date = :appointment_date
                  AND status != :status
                  AND ABS(TIME_TO_SEC(TIMEDIFF(appointment_time, :appointment_time))) < 1800';

        $params = [
            'patient_id'       => $patientId,
            'appointment_date' => $date,
            'status'           => 'cancelled',
            'appointment_time' => $time,
        ];

        if ($excludeId !== null) {
            $sql .= ' AND id != :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }
}
