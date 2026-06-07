<?php

require_once __DIR__ . '/../config/database.php';

class Doctor
{
    /**
     * Get all doctors ordered alphabetically by full name.
     *
     * @return array List of doctor records
     */
    public static function all(): array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->query('SELECT * FROM doctors ORDER BY full_name ASC');

        return $stmt->fetchAll();
    }

    /**
     * Find a doctor by ID.
     *
     * @param int $id
     * @return array|null Doctor record or null if not found
     */
    public static function find(int $id): ?array
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $doctor = $stmt->fetch();

        return $doctor ?: null;
    }

    /**
     * Create a new doctor record.
     *
     * @param array $data Associative array with keys: full_name, email, phone, specialty, license_number
     * @return int The ID of the newly created doctor
     */
    public static function create(array $data): int
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO doctors (full_name, email, phone, specialty, license_number)
             VALUES (:full_name, :email, :phone, :specialty, :license_number)'
        );
        $stmt->execute([
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'specialty'      => $data['specialty'],
            'license_number' => $data['license_number'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    /**
     * Update an existing doctor record.
     *
     * @param int   $id   The doctor ID to update
     * @param array $data Associative array with keys: full_name, email, phone, specialty, license_number
     * @return bool True if the update affected a row, false otherwise
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            'UPDATE doctors
             SET full_name = :full_name, email = :email, phone = :phone,
                 specialty = :specialty, license_number = :license_number
             WHERE id = :id'
        );
        $stmt->execute([
            'full_name'      => $data['full_name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'specialty'      => $data['specialty'],
            'license_number' => $data['license_number'],
            'id'             => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if a doctor with the given email already exists.
     *
     * @param string   $email     The email to check
     * @param int|null $excludeId Optional doctor ID to exclude from the check (for updates)
     * @return bool True if a doctor with that email exists
     */
    public static function existsByEmail(string $email, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        if ($excludeId !== null) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE email = :email AND id != :id');
            $stmt->execute(['email' => $email, 'id' => $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE email = :email');
            $stmt->execute(['email' => $email]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Check if a doctor with the given license number already exists.
     *
     * @param string   $license   The license number to check
     * @param int|null $excludeId Optional doctor ID to exclude from the check (for updates)
     * @return bool True if a doctor with that license number exists
     */
    public static function existsByLicense(string $license, ?int $excludeId = null): bool
    {
        $pdo = getDBConnection();

        if ($excludeId !== null) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE license_number = :license AND id != :id');
            $stmt->execute(['license' => $license, 'id' => $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM doctors WHERE license_number = :license');
            $stmt->execute(['license' => $license]);
        }

        return (int) $stmt->fetchColumn() > 0;
    }
}
