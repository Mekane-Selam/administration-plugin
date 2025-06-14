<?php
/**
 * Autoloader class for the Administration Plugin
 */
class Administration_Plugin_Autoloader {
    /**
     * Register the autoloader
     */
    public static function register() {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Autoload classes
     *
     * @param string $class_name The name of the class to load
     */
    public static function autoload($class_name) {
        // Handle classes with our prefix or Permissions_Util
        if (strpos($class_name, 'Administration_') !== 0 && $class_name !== 'Permissions_Util') {
            return;
        }

        // Convert class name to file path
        if ($class_name === 'Permissions_Util') {
            $file_name = 'class-permissions-util.php';
        } else {
        $file_name = strtolower(str_replace('_', '-', $class_name));
        $file_name = str_replace('administration-', '', $file_name);
        $file_name = 'class-' . $file_name . '.php';
        }

        // Define the base directories to search
        $directories = array(
            'includes/',
            'includes/activator/',
            'includes/admin/',
            'includes/api/',
            'includes/database/',
            'includes/public/',
            'includes/sync/',
            'includes/ajax/'
        );

        // Search for the file in each directory
        foreach ($directories as $directory) {
            $file_path = ADMINISTRATION_PLUGIN_PATH . $directory . $file_name;
            if (file_exists($file_path)) {
                require_once $file_path;
                return;
            }
        }

        // If we get here, the class file wasn't found
        error_log('Administration Plugin: Could not load class file for ' . $class_name);
    }
}

// Register the autoloader
Administration_Plugin_Autoloader::register(); 