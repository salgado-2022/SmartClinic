<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Property-based tests for the Validator class.
 *
 * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
 * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
 */
class ValidatorPropertyTest extends TestCase
{
    // =========================================================================
    // Helper: Random subset selection
    // =========================================================================

    /**
     * Select a random non-empty subset of the given array.
     *
     * @param array $items
     * @return array
     */
    private static function randomSubset(array $items): array
    {
        $count = random_int(1, count($items));
        $keys = array_rand($items, $count);
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $subset = [];
        foreach ($keys as $key) {
            $subset[] = $items[$key];
        }
        return $subset;
    }

    /**
     * Generate a random alphanumeric string of a given length.
     */
    private static function randomAlphanumeric(int $length): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }

    /**
     * Generate a random digit string of a given length.
     */
    private static function randomDigits(int $length): string
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= (string) random_int(0, 9);
        }
        return $str;
    }

    // =========================================================================
    // Patient: Invalid field generators
    // =========================================================================

    /**
     * Generate a valid patient base data array.
     */
    private static function validPatientBase(): array
    {
        return [
            'full_name'     => 'Juan Perez',
            'date_of_birth' => '1990-05-15',
            'email'         => 'juan' . random_int(1, 9999) . '@example.com',
            'phone'         => self::randomDigits(random_int(7, 15)),
            'id_document'   => self::randomAlphanumeric(random_int(5, 20)),
        ];
    }

    /**
     * Invalidate a specific patient field using various strategies.
     */
    private static function invalidatePatientField(string $field): string|null
    {
        $strategy = random_int(0, 2); // 0 = remove key, 1 = empty, 2 = non-empty invalid

        if ($strategy === 0) {
            return null; // signals "remove key"
        }
        if ($strategy === 1) {
            return ''; // triggers required/empty check
        }

        // Non-empty but invalid values
        switch ($field) {
            case 'full_name':
                // 1 char (too short) or >100 chars (too long)
                return random_int(0, 1) === 0 ? 'A' : str_repeat('X', 101);
            case 'date_of_birth':
                // Invalid format or future date
                return random_int(0, 1) === 0 ? 'not-a-date-format' : '2099-12-31';
            case 'email':
                // No @ sign or too long
                return random_int(0, 1) === 0 ? 'invalid-email-no-at' : str_repeat('a', 145) . '@b.com';
            case 'phone':
                // Too short, too long, or non-numeric
                $options = ['123', '1234567890123456', 'abc-def-ghij'];
                return $options[random_int(0, 2)];
            case 'id_document':
                // Too short, too long, or special characters
                $options = ['AB1', str_repeat('X', 21), 'ABC-12#45'];
                return $options[random_int(0, 2)];
            default:
                return '';
        }
    }

    /**
     * Generate patient data with specified fields invalidated.
     *
     * @return array The data array with invalid fields applied
     */
    private static function generatePatientWithInvalidFields(array $fieldsToInvalidate): array
    {
        $data = self::validPatientBase();

        foreach ($fieldsToInvalidate as $field) {
            $invalidValue = self::invalidatePatientField($field);
            if ($invalidValue === null) {
                unset($data[$field]); // Remove key entirely
            } else {
                $data[$field] = $invalidValue;
            }
        }

        return $data;
    }

    // =========================================================================
    // Doctor: Invalid field generators
    // =========================================================================

    /**
     * Generate a valid doctor base data array.
     */
    private static function validDoctorBase(): array
    {
        return [
            'full_name'      => 'Doctor Name Here',
            'email'          => 'doc' . random_int(1, 9999) . '@clinic.com',
            'phone'          => self::randomDigits(random_int(7, 15)),
            'specialty'      => 'Cardiologia',
            'license_number' => self::randomAlphanumeric(random_int(4, 20)),
        ];
    }

    /**
     * Invalidate a specific doctor field.
     */
    private static function invalidateDoctorField(string $field): string|null
    {
        $strategy = random_int(0, 2);

        if ($strategy === 0) {
            return null; // remove key
        }
        if ($strategy === 1) {
            return ''; // triggers required
        }

        switch ($field) {
            case 'full_name':
                // <3 chars or >100 chars
                return random_int(0, 1) === 0 ? 'Ab' : str_repeat('X', 101);
            case 'email':
                // Invalid format or >100 chars
                return random_int(0, 1) === 0 ? 'not-an-email' : str_repeat('a', 95) . '@b.com';
            case 'phone':
                $options = ['12345', '1234567890123456', 'phone-invalid'];
                return $options[random_int(0, 2)];
            case 'specialty':
                // <3 chars or >100 chars
                return random_int(0, 1) === 0 ? 'AB' : str_repeat('S', 101);
            case 'license_number':
                // <4 chars, >20 chars, or special chars
                $options = ['ME1', str_repeat('L', 21), 'MED-123!'];
                return $options[random_int(0, 2)];
            default:
                return '';
        }
    }

    /**
     * Generate doctor data with specified fields invalidated.
     */
    private static function generateDoctorWithInvalidFields(array $fieldsToInvalidate): array
    {
        $data = self::validDoctorBase();

        foreach ($fieldsToInvalidate as $field) {
            $invalidValue = self::invalidateDoctorField($field);
            if ($invalidValue === null) {
                unset($data[$field]);
            } else {
                $data[$field] = $invalidValue;
            }
        }

        return $data;
    }

    // =========================================================================
    // Appointment: Invalid field generators
    // =========================================================================

    /**
     * Generate a valid appointment base data array.
     */
    private static function validAppointmentBase(): array
    {
        $futureDate = date('Y-m-d', strtotime('+' . random_int(1, 30) . ' days'));
        $hour = random_int(8, 17);
        $minute = random_int(0, 59);

        return [
            'patient_id'       => (string) random_int(1, 999),
            'doctor_id'        => (string) random_int(1, 999),
            'appointment_date' => $futureDate,
            'appointment_time' => sprintf('%02d:%02d', $hour, $minute),
        ];
    }

    /**
     * Invalidate a specific appointment field.
     */
    private static function invalidateAppointmentField(string $field): string|null
    {
        $strategy = random_int(0, 2);

        if ($strategy === 0) {
            return null; // remove key
        }
        if ($strategy === 1) {
            return ''; // triggers required
        }

        switch ($field) {
            case 'patient_id':
                return 'abc'; // non-numeric
            case 'doctor_id':
                return 'xyz'; // non-numeric
            case 'appointment_date':
                // Invalid format or past date
                return random_int(0, 1) === 0 ? 'invalid-date' : '2020-01-01';
            case 'appointment_time':
                // Invalid format, before 08:00, or after 18:00
                $options = ['99:99', '07:00', '19:00'];
                return $options[random_int(0, 2)];
            default:
                return '';
        }
    }

    /**
     * Generate appointment data with specified fields invalidated.
     */
    private static function generateAppointmentWithInvalidFields(array $fieldsToInvalidate): array
    {
        $data = self::validAppointmentBase();

        foreach ($fieldsToInvalidate as $field) {
            $invalidValue = self::invalidateAppointmentField($field);
            if ($invalidValue === null) {
                unset($data[$field]);
            } else {
                $data[$field] = $invalidValue;
            }
        }

        return $data;
    }

    // =========================================================================
    // Credentials: Invalid field generators
    // =========================================================================

    /**
     * Generate a valid credentials base data array.
     */
    private static function validCredentialsBase(): array
    {
        return [
            'email'    => 'user' . random_int(1, 9999) . '@example.com',
            'password' => self::randomAlphanumeric(random_int(8, 20)),
        ];
    }

    /**
     * Invalidate a specific credentials field.
     */
    private static function invalidateCredentialsField(string $field): string|null
    {
        $strategy = random_int(0, 2);

        if ($strategy === 0) {
            return null; // remove key
        }
        if ($strategy === 1) {
            return ''; // triggers required
        }

        switch ($field) {
            case 'email':
                return 'not-valid-email'; // invalid format
            case 'password':
                return 'short'; // <8 chars
            default:
                return '';
        }
    }

    /**
     * Generate credentials data with specified fields invalidated.
     */
    private static function generateCredentialsWithInvalidFields(array $fieldsToInvalidate): array
    {
        $data = self::validCredentialsBase();

        foreach ($fieldsToInvalidate as $field) {
            $invalidValue = self::invalidateCredentialsField($field);
            if ($invalidValue === null) {
                unset($data[$field]);
            } else {
                $data[$field] = $invalidValue;
            }
        }

        return $data;
    }

    // =========================================================================
    // Data Providers (100 iterations each)
    // =========================================================================

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     */
    public static function randomInvalidPatientDataProvider(): array
    {
        $cases = [];
        $allFields = ['full_name', 'date_of_birth', 'email', 'phone', 'id_document'];

        for ($i = 0; $i < 100; $i++) {
            $fieldsToInvalidate = self::randomSubset($allFields);
            $data = self::generatePatientWithInvalidFields($fieldsToInvalidate);
            $cases["patient_iteration_$i"] = [$data, $fieldsToInvalidate];
        }

        return $cases;
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     */
    public static function randomInvalidDoctorDataProvider(): array
    {
        $cases = [];
        $allFields = ['full_name', 'email', 'phone', 'specialty', 'license_number'];

        for ($i = 0; $i < 100; $i++) {
            $fieldsToInvalidate = self::randomSubset($allFields);
            $data = self::generateDoctorWithInvalidFields($fieldsToInvalidate);
            $cases["doctor_iteration_$i"] = [$data, $fieldsToInvalidate];
        }

        return $cases;
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     */
    public static function randomInvalidAppointmentDataProvider(): array
    {
        $cases = [];
        $allFields = ['patient_id', 'doctor_id', 'appointment_date', 'appointment_time'];

        for ($i = 0; $i < 100; $i++) {
            $fieldsToInvalidate = self::randomSubset($allFields);
            $data = self::generateAppointmentWithInvalidFields($fieldsToInvalidate);
            $cases["appointment_iteration_$i"] = [$data, $fieldsToInvalidate];
        }

        return $cases;
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     */
    public static function randomInvalidCredentialsDataProvider(): array
    {
        $cases = [];
        $allFields = ['email', 'password'];

        for ($i = 0; $i < 100; $i++) {
            $fieldsToInvalidate = self::randomSubset($allFields);
            $data = self::generateCredentialsWithInvalidFields($fieldsToInvalidate);
            $cases["credentials_iteration_$i"] = [$data, $fieldsToInvalidate];
        }

        return $cases;
    }

    // =========================================================================
    // Property Tests: Invalid fields produce matching error keys
    // =========================================================================

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     *
     * For any patient data with one or more invalid fields, the returned error
     * array keys must match EXACTLY the names of the invalid fields.
     */
    #[DataProvider('randomInvalidPatientDataProvider')]
    public function testPropertyInvalidPatientFieldsProduceMatchingErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validatePatient($data);

        $actualKeys = array_keys($result);
        sort($actualKeys);
        sort($expectedErrorKeys);

        $this->assertEquals(
            $expectedErrorKeys,
            $actualKeys,
            sprintf(
                "Error keys mismatch.\nExpected invalid fields: [%s]\nActual error keys: [%s]\nData: %s",
                implode(', ', $expectedErrorKeys),
                implode(', ', $actualKeys),
                json_encode($data, JSON_UNESCAPED_UNICODE)
            )
        );
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     *
     * For any doctor data with one or more invalid fields, the returned error
     * array keys must match EXACTLY the names of the invalid fields.
     */
    #[DataProvider('randomInvalidDoctorDataProvider')]
    public function testPropertyInvalidDoctorFieldsProduceMatchingErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateDoctor($data);

        $actualKeys = array_keys($result);
        sort($actualKeys);
        sort($expectedErrorKeys);

        $this->assertEquals(
            $expectedErrorKeys,
            $actualKeys,
            sprintf(
                "Error keys mismatch.\nExpected invalid fields: [%s]\nActual error keys: [%s]\nData: %s",
                implode(', ', $expectedErrorKeys),
                implode(', ', $actualKeys),
                json_encode($data, JSON_UNESCAPED_UNICODE)
            )
        );
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     *
     * For any appointment data with one or more invalid fields, the returned error
     * array keys must match EXACTLY the names of the invalid fields.
     */
    #[DataProvider('randomInvalidAppointmentDataProvider')]
    public function testPropertyInvalidAppointmentFieldsProduceMatchingErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateAppointment($data);

        $actualKeys = array_keys($result);
        sort($actualKeys);
        sort($expectedErrorKeys);

        $this->assertEquals(
            $expectedErrorKeys,
            $actualKeys,
            sprintf(
                "Error keys mismatch.\nExpected invalid fields: [%s]\nActual error keys: [%s]\nData: %s",
                implode(', ', $expectedErrorKeys),
                implode(', ', $actualKeys),
                json_encode($data, JSON_UNESCAPED_UNICODE)
            )
        );
    }

    /**
     * Feature: unit-testing-pipeline, Property 2: Invalid fields produce matching error keys
     * **Validates: Requirements 3.2, 3.4, 3.6, 3.8**
     *
     * For any credentials data with one or more invalid fields, the returned error
     * array keys must match EXACTLY the names of the invalid fields.
     */
    #[DataProvider('randomInvalidCredentialsDataProvider')]
    public function testPropertyInvalidCredentialsFieldsProduceMatchingErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateCredentials($data);

        $actualKeys = array_keys($result);
        sort($actualKeys);
        sort($expectedErrorKeys);

        $this->assertEquals(
            $expectedErrorKeys,
            $actualKeys,
            sprintf(
                "Error keys mismatch.\nExpected invalid fields: [%s]\nActual error keys: [%s]\nData: %s",
                implode(', ', $expectedErrorKeys),
                implode(', ', $actualKeys),
                json_encode($data, JSON_UNESCAPED_UNICODE)
            )
        );
    }
}
