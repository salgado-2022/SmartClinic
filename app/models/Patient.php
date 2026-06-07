<?php

require_once __DIR__ . '/../config/database.php';

class Patient
{
    /**
     * Retrieve all patients from the database.
     *
     * @return array List of all patient records
     */
    public static function all(): array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->query('SELECT * FROM patients ORDER BY full_name ASC');

        return $stmt->fetchAll();
    }

    /**
     * Find a patient by their ID.
     *
     * @param int $id Patient ID
     * @return array|null Patient record or null if not found
     */
    public static function find(int $id): ?array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $patient = $stmt->fetch();

        return $patient ?: null;
    }

    /**
     * Create a new patient record.
     *
     * @param array $data Patient data (full_name, date_of_birth, email, phone, id_document)
     * @return int The ID of the newly created patient
     */
    public static function create(array $data): int
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO patients (full_name, date_of_birth, email, phone, id_document)
             VALUES (:full_name, :date_of_birth, :email, :phone, :id_document)'
        );
        $stmt->execute([
            'full_name'     => $data['full_name'],
            'date_of_birth' => $data['date_of_birth'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'id_document'   => $data['id_document'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Update an existing patient record.
     *
     * @param int   $id   Patient ID
     * @param array $data Patient data to update
     * @return bool True if the update affected at least one row
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'UPDATE patients
             SET full_name = :full_name,
                 date_of_birth = :date_of_birth,
                 email = :email,
                 phone = :phone,
                 id_document = :id_document
             WHERE id = :id'
        );
        $stmt->execute([
            'full_name'     => $data['full_name'],
            'date_of_birth' => $data['date_of_birth'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'id_document'   => $data['id_document'],
            'id'            => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a patient with the given email already exists.
     *
     * @param string   $email     Email to check
     * @param int|null $excludeId Optional patient ID to exclude (for update scenarios)
     * @return bool True if a patient with this email exists
     */
    public static function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        if ($excludeId !== null) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE email = :email AND id != :excludeId');
            $stmt->execute(['email' => $email, 'excludeId' => $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Check if a patient with the given ID document already exists.
     *
     * @param string   $document  ID document to check
     * @param int|null $excludeId Optional patient ID to exclude (for update scenarios)
     * @return bool True if a patient with this document exists
     */
    public static function existsByDocument(string $document, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        if ($excludeId !== null) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE id_document = :document AND id != :excludeId');
            $stmt->execute(['document' => $document, 'excludeId' => $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE id_document = :document');
            $stmt->execute(['document' => $document]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }
}
