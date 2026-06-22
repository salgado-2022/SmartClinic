<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    // =========================================================================
    // Valid Patient Data Tests
    // =========================================================================

    public static function validPatientDataProvider(): array
    {
        return [
            'typical patient' => [[
                'full_name'     => 'Juan Pérez',
                'date_of_birth' => '1990-05-15',
                'email'         => 'juan@example.com',
                'phone'         => '1234567890',
                'id_document'   => 'ABC12345',
            ]],
            'minimum length fields' => [[
                'full_name'     => 'Ab',
                'date_of_birth' => '2000-01-01',
                'email'         => 'a@b.co',
                'phone'         => '1234567',
                'id_document'   => 'A1B2C',
            ]],
            'maximum length fields' => [[
                'full_name'     => str_repeat('A', 100),
                'date_of_birth' => '1950-12-31',
                'email'         => 'longname' . str_repeat('a', 50) . '@example.com',
                'phone'         => '123456789012345',
                'id_document'   => 'ABCDEFGHIJ1234567890',
            ]],
            'patient with accented name' => [[
                'full_name'     => 'María García López',
                'date_of_birth' => '1985-03-20',
                'email'         => 'maria.garcia@hospital.org',
                'phone'         => '9876543210',
                'id_document'   => 'DOC98765',
            ]],
        ];
    }

    #[DataProvider('validPatientDataProvider')]
    public function testValidatePatientWithValidDataReturnsEmptyArray(array $data): void
    {
        $result = \Validator::validatePatient($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Expected no validation errors for valid patient data');
    }

    // =========================================================================
    // Valid Doctor Data Tests
    // =========================================================================

    public static function validDoctorDataProvider(): array
    {
        return [
            'typical doctor' => [[
                'full_name'      => 'Dr. Carlos Mendoza',
                'email'          => 'carlos@clinic.com',
                'phone'          => '5551234567',
                'specialty'      => 'Cardiología',
                'license_number' => 'MED12345',
            ]],
            'minimum length fields' => [[
                'full_name'      => 'Ana',
                'email'          => 'a@b.co',
                'phone'          => '7654321',
                'specialty'      => 'UCI',
                'license_number' => 'M123',
            ]],
            'maximum length fields' => [[
                'full_name'      => str_repeat('B', 100),
                'email'          => str_repeat('x', 60) . '@' . str_repeat('y', 30) . '.com',
                'phone'          => '123456789012345',
                'specialty'      => str_repeat('C', 100),
                'license_number' => 'ABCDEFGHIJ1234567890',
            ]],
            'doctor with long specialty' => [[
                'full_name'      => 'Roberto Sánchez Villanueva',
                'email'          => 'roberto@medcenter.com',
                'phone'          => '55512345678',
                'specialty'      => 'Cirugía Cardiovascular',
                'license_number' => 'LIC2024ABC',
            ]],
        ];
    }

    #[DataProvider('validDoctorDataProvider')]
    public function testValidateDoctorWithValidDataReturnsEmptyArray(array $data): void
    {
        $result = \Validator::validateDoctor($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Expected no validation errors for valid doctor data');
    }

    // =========================================================================
    // Valid Appointment Data Tests
    // =========================================================================

    public static function validAppointmentDataProvider(): array
    {
        $futureDate = date('Y-m-d', strtotime('+7 days'));
        $today = date('Y-m-d');

        return [
            'typical appointment' => [[
                'patient_id'       => '1',
                'doctor_id'        => '5',
                'appointment_date' => $futureDate,
                'appointment_time' => '10:00',
            ]],
            'appointment today at start of day' => [[
                'patient_id'       => '100',
                'doctor_id'        => '200',
                'appointment_date' => $today,
                'appointment_time' => '08:00',
            ]],
            'appointment at end of day' => [[
                'patient_id'       => '42',
                'doctor_id'        => '7',
                'appointment_date' => $futureDate,
                'appointment_time' => '18:00',
            ]],
            'appointment at midday' => [[
                'patient_id'       => '999',
                'doctor_id'        => '888',
                'appointment_date' => $futureDate,
                'appointment_time' => '12:30',
            ]],
        ];
    }

    #[DataProvider('validAppointmentDataProvider')]
    public function testValidateAppointmentWithValidDataReturnsEmptyArray(array $data): void
    {
        $result = \Validator::validateAppointment($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Expected no validation errors for valid appointment data');
    }

    // =========================================================================
    // Valid Credentials Data Tests
    // =========================================================================

    public static function validCredentialsDataProvider(): array
    {
        return [
            'typical credentials' => [[
                'email'    => 'user@example.com',
                'password' => 'password123',
            ]],
            'long password' => [[
                'email'    => 'admin@clinic.org',
                'password' => 'MyV3ryS3cur3P@ssw0rd!',
            ]],
            'minimum password length' => [[
                'email'    => 'doctor@hospital.com',
                'password' => '12345678',
            ]],
            'complex email' => [[
                'email'    => 'first.last+tag@sub.domain.com',
                'password' => 'securePass1',
            ]],
        ];
    }

    #[DataProvider('validCredentialsDataProvider')]
    public function testValidateCredentialsWithValidDataReturnsEmptyArray(array $data): void
    {
        $result = \Validator::validateCredentials($data);

        $this->assertIsArray($result);
        $this->assertEmpty($result, 'Expected no validation errors for valid credentials');
    }

    // =========================================================================
    // Invalid Patient Data Tests
    // =========================================================================

    public static function invalidPatientDataProvider(): array
    {
        return [
            'missing full_name' => [
                ['date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['full_name'],
            ],
            'full_name too short (1 char)' => [
                ['full_name' => 'A', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['full_name'],
            ],
            'full_name too long (101 chars)' => [
                ['full_name' => str_repeat('A', 101), 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['full_name'],
            ],
            'invalid date format' => [
                ['full_name' => 'Juan', 'date_of_birth' => 'not-a-date', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['date_of_birth'],
            ],
            'future date_of_birth' => [
                ['full_name' => 'Juan', 'date_of_birth' => '2099-12-31', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['date_of_birth'],
            ],
            'invalid email format' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'invalid-email', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['email'],
            ],
            'email too long (>150 chars)' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => str_repeat('a', 145) . '@b.com', 'phone' => '1234567', 'id_document' => 'ABC12'],
                ['email'],
            ],
            'phone too short (6 digits)' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '123456', 'id_document' => 'ABC12'],
                ['phone'],
            ],
            'phone too long (16 digits)' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567890123456', 'id_document' => 'ABC12'],
                ['phone'],
            ],
            'phone with non-numeric chars' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '123-456-7890', 'id_document' => 'ABC12'],
                ['phone'],
            ],
            'id_document too short (4 chars)' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'AB12'],
                ['id_document'],
            ],
            'id_document too long (21 chars)' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABCDEFGHIJ12345678901'],
                ['id_document'],
            ],
            'id_document with special chars' => [
                ['full_name' => 'Juan', 'date_of_birth' => '1990-01-01', 'email' => 'test@test.com', 'phone' => '1234567', 'id_document' => 'ABC-12#45'],
                ['id_document'],
            ],
        ];
    }

    #[DataProvider('invalidPatientDataProvider')]
    public function testValidatePatientWithInvalidDataReturnsExpectedErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validatePatient($data);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        foreach ($expectedErrorKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Expected error key '$key' to be present");
        }
    }

    // =========================================================================
    // Invalid Doctor Data Tests
    // =========================================================================

    public static function invalidDoctorDataProvider(): array
    {
        return [
            'missing full_name' => [
                ['email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['full_name'],
            ],
            'full_name too short (2 chars)' => [
                ['full_name' => 'Ab', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['full_name'],
            ],
            'full_name too long (101 chars)' => [
                ['full_name' => str_repeat('A', 101), 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['full_name'],
            ],
            'invalid email format' => [
                ['full_name' => 'Doctor Name', 'email' => 'not-an-email', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['email'],
            ],
            'email too long (>100 chars)' => [
                ['full_name' => 'Doctor Name', 'email' => str_repeat('a', 95) . '@b.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['email'],
            ],
            'phone too short (6 digits)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '123456', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['phone'],
            ],
            'phone too long (16 digits)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567890123456', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['phone'],
            ],
            'phone with non-numeric chars' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '555-1234', 'specialty' => 'Cardio', 'license_number' => 'MED1'],
                ['phone'],
            ],
            'specialty too short (2 chars)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'AB', 'license_number' => 'MED1'],
                ['specialty'],
            ],
            'specialty too long (101 chars)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => str_repeat('S', 101), 'license_number' => 'MED1'],
                ['specialty'],
            ],
            'license_number too short (3 chars)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'ME1'],
                ['license_number'],
            ],
            'license_number too long (21 chars)' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'ABCDEFGHIJ12345678901'],
                ['license_number'],
            ],
            'license_number with special chars' => [
                ['full_name' => 'Doctor Name', 'email' => 'doc@test.com', 'phone' => '1234567', 'specialty' => 'Cardio', 'license_number' => 'MED-123!'],
                ['license_number'],
            ],
        ];
    }

    #[DataProvider('invalidDoctorDataProvider')]
    public function testValidateDoctorWithInvalidDataReturnsExpectedErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateDoctor($data);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        foreach ($expectedErrorKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Expected error key '$key' to be present");
        }
    }

    // =========================================================================
    // Invalid Appointment Data Tests
    // =========================================================================

    public static function invalidAppointmentDataProvider(): array
    {
        $futureDate = date('Y-m-d', strtotime('+7 days'));

        return [
            'missing patient_id' => [
                ['doctor_id' => '1', 'appointment_date' => $futureDate, 'appointment_time' => '10:00'],
                ['patient_id'],
            ],
            'non-numeric patient_id' => [
                ['patient_id' => 'abc', 'doctor_id' => '1', 'appointment_date' => $futureDate, 'appointment_time' => '10:00'],
                ['patient_id'],
            ],
            'non-numeric doctor_id' => [
                ['patient_id' => '1', 'doctor_id' => 'xyz', 'appointment_date' => $futureDate, 'appointment_time' => '10:00'],
                ['doctor_id'],
            ],
            'invalid date format' => [
                ['patient_id' => '1', 'doctor_id' => '1', 'appointment_date' => 'not-a-date', 'appointment_time' => '10:00'],
                ['appointment_date'],
            ],
            'past date' => [
                ['patient_id' => '1', 'doctor_id' => '1', 'appointment_date' => '2020-01-01', 'appointment_time' => '10:00'],
                ['appointment_date'],
            ],
            'invalid time format' => [
                ['patient_id' => '1', 'doctor_id' => '1', 'appointment_date' => $futureDate, 'appointment_time' => '25:99'],
                ['appointment_time'],
            ],
            'time before 08:00' => [
                ['patient_id' => '1', 'doctor_id' => '1', 'appointment_date' => $futureDate, 'appointment_time' => '07:59'],
                ['appointment_time'],
            ],
            'time after 18:00' => [
                ['patient_id' => '1', 'doctor_id' => '1', 'appointment_date' => $futureDate, 'appointment_time' => '18:01'],
                ['appointment_time'],
            ],
        ];
    }

    #[DataProvider('invalidAppointmentDataProvider')]
    public function testValidateAppointmentWithInvalidDataReturnsExpectedErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateAppointment($data);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        foreach ($expectedErrorKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Expected error key '$key' to be present");
        }
    }

    // =========================================================================
    // Invalid Credentials Data Tests
    // =========================================================================

    public static function invalidCredentialsDataProvider(): array
    {
        return [
            'missing email' => [
                ['password' => 'password123'],
                ['email'],
            ],
            'invalid email format' => [
                ['email' => 'not-an-email', 'password' => 'password123'],
                ['email'],
            ],
            'missing password' => [
                ['email' => 'user@test.com'],
                ['password'],
            ],
            'password too short (7 chars)' => [
                ['email' => 'user@test.com', 'password' => '1234567'],
                ['password'],
            ],
        ];
    }

    #[DataProvider('invalidCredentialsDataProvider')]
    public function testValidateCredentialsWithInvalidDataReturnsExpectedErrorKeys(array $data, array $expectedErrorKeys): void
    {
        $result = \Validator::validateCredentials($data);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        foreach ($expectedErrorKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Expected error key '$key' to be present");
        }
    }

    // =========================================================================
    // All-Empty Input Tests
    // =========================================================================

    public function testValidatePatientWithAllEmptyReturnsErrorsForAllFields(): void
    {
        $result = \Validator::validatePatient([]);

        $this->assertCount(5, $result, 'Expected 5 errors for empty patient data');
        $this->assertArrayHasKey('full_name', $result);
        $this->assertArrayHasKey('date_of_birth', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('id_document', $result);
    }

    public function testValidateDoctorWithAllEmptyReturnsErrorsForAllFields(): void
    {
        $result = \Validator::validateDoctor([]);

        $this->assertCount(5, $result, 'Expected 5 errors for empty doctor data');
        $this->assertArrayHasKey('full_name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('specialty', $result);
        $this->assertArrayHasKey('license_number', $result);
    }

    public function testValidateAppointmentWithAllEmptyReturnsErrorsForAllFields(): void
    {
        $result = \Validator::validateAppointment([]);

        $this->assertCount(4, $result, 'Expected 4 errors for empty appointment data');
        $this->assertArrayHasKey('patient_id', $result);
        $this->assertArrayHasKey('doctor_id', $result);
        $this->assertArrayHasKey('appointment_date', $result);
        $this->assertArrayHasKey('appointment_time', $result);
    }

    public function testValidateCredentialsWithAllEmptyReturnsErrorsForAllFields(): void
    {
        $result = \Validator::validateCredentials([]);

        $this->assertCount(2, $result, 'Expected 2 errors for empty credentials data');
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('password', $result);
    }

    // =========================================================================
    // Boundary Value Tests
    // =========================================================================

    public static function patientFullNameBoundaryProvider(): array
    {
        return [
            '1 char (too short)' => ['A', true],
            '2 chars (valid min)' => ['Ab', false],
            '100 chars (valid max)' => [str_repeat('A', 100), false],
            '101 chars (too long)' => [str_repeat('A', 101), true],
        ];
    }

    #[DataProvider('patientFullNameBoundaryProvider')]
    public function testValidatePatientFullNameBoundary(string $fullName, bool $expectError): void
    {
        $data = [
            'full_name'     => $fullName,
            'date_of_birth' => '1990-01-01',
            'email'         => 'test@test.com',
            'phone'         => '1234567',
            'id_document'   => 'ABC12',
        ];

        $result = \Validator::validatePatient($data);

        if ($expectError) {
            $this->assertArrayHasKey('full_name', $result);
        } else {
            $this->assertArrayNotHasKey('full_name', $result);
        }
    }

    public static function phoneBoundaryProvider(): array
    {
        return [
            '6 digits (too short)' => ['123456', true],
            '7 digits (valid min)' => ['1234567', false],
            '15 digits (valid max)' => ['123456789012345', false],
            '16 digits (too long)' => ['1234567890123456', true],
        ];
    }

    #[DataProvider('phoneBoundaryProvider')]
    public function testValidatePatientPhoneBoundary(string $phone, bool $expectError): void
    {
        $data = [
            'full_name'     => 'Juan Pérez',
            'date_of_birth' => '1990-01-01',
            'email'         => 'test@test.com',
            'phone'         => $phone,
            'id_document'   => 'ABC12',
        ];

        $result = \Validator::validatePatient($data);

        if ($expectError) {
            $this->assertArrayHasKey('phone', $result);
        } else {
            $this->assertArrayNotHasKey('phone', $result);
        }
    }

    public static function idDocumentBoundaryProvider(): array
    {
        return [
            '4 chars (too short)' => ['AB12', true],
            '5 chars (valid min)' => ['ABC12', false],
            '20 chars (valid max)' => ['ABCDEFGHIJ1234567890', false],
            '21 chars (too long)' => ['ABCDEFGHIJ12345678901', true],
        ];
    }

    #[DataProvider('idDocumentBoundaryProvider')]
    public function testValidatePatientIdDocumentBoundary(string $idDocument, bool $expectError): void
    {
        $data = [
            'full_name'     => 'Juan Pérez',
            'date_of_birth' => '1990-01-01',
            'email'         => 'test@test.com',
            'phone'         => '1234567',
            'id_document'   => $idDocument,
        ];

        $result = \Validator::validatePatient($data);

        if ($expectError) {
            $this->assertArrayHasKey('id_document', $result);
        } else {
            $this->assertArrayNotHasKey('id_document', $result);
        }
    }

    public static function licenseNumberBoundaryProvider(): array
    {
        return [
            '3 chars (too short)' => ['ME1', true],
            '4 chars (valid min)' => ['MED1', false],
            '20 chars (valid max)' => ['ABCDEFGHIJ1234567890', false],
            '21 chars (too long)' => ['ABCDEFGHIJ12345678901', true],
        ];
    }

    #[DataProvider('licenseNumberBoundaryProvider')]
    public function testValidateDoctorLicenseNumberBoundary(string $licenseNumber, bool $expectError): void
    {
        $data = [
            'full_name'      => 'Doctor Name',
            'email'          => 'doc@test.com',
            'phone'          => '1234567',
            'specialty'      => 'Cardiology',
            'license_number' => $licenseNumber,
        ];

        $result = \Validator::validateDoctor($data);

        if ($expectError) {
            $this->assertArrayHasKey('license_number', $result);
        } else {
            $this->assertArrayNotHasKey('license_number', $result);
        }
    }
}
