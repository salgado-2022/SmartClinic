<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base TestCase for all SmartClinic unit tests.
 *
 * Provides autoloading for app classes (which do not use namespaces)
 * and shared helpers available to all test classes.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Map of class names to their file paths relative to the app/ directory.
     * Used by the custom autoloader to locate non-namespaced app classes.
     */
    private static array $classMap = [
        // Helpers
        'Validator'      => 'app/helpers/Validator.php',
        'Router'         => 'app/helpers/Router.php',
        'Session'        => 'app/helpers/Session.php',
        // Middleware
        'AuthMiddleware' => 'app/middleware/AuthMiddleware.php',
        // Models
        'Patient'        => 'app/models/Patient.php',
        'Doctor'         => 'app/models/Doctor.php',
        'Appointment'    => 'app/models/Appointment.php',
        'User'           => 'app/models/User.php',
        // Controllers
        'AppointmentController' => 'app/controllers/AppointmentController.php',
        'AuthController'        => 'app/controllers/AuthController.php',
        'DashboardController'   => 'app/controllers/DashboardController.php',
        'DoctorController'      => 'app/controllers/DoctorController.php',
        'HealthController'      => 'app/controllers/HealthController.php',
        'PatientController'     => 'app/controllers/PatientController.php',
    ];

    /**
     * Whether the autoloader has already been registered.
     */
    private static bool $autoloaderRegistered = false;

    /**
     * Register the custom autoloader once before any test runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::registerAutoloader();
    }

    /**
     * Register a custom autoloader for non-namespaced app classes.
     * This allows tests to reference app classes without manual require_once calls.
     */
    protected static function registerAutoloader(): void
    {
        if (self::$autoloaderRegistered) {
            return;
        }

        $projectRoot = dirname(__DIR__);

        spl_autoload_register(function (string $className) use ($projectRoot): void {
            if (isset(self::$classMap[$className])) {
                $filePath = $projectRoot . DIRECTORY_SEPARATOR . self::$classMap[$className];
                if (file_exists($filePath)) {
                    require_once $filePath;
                }
            }
        });

        self::$autoloaderRegistered = true;
    }

    /**
     * Get the absolute path to the project root directory.
     */
    protected function getProjectRoot(): string
    {
        return dirname(__DIR__);
    }
}
