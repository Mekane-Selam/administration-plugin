<?php
/**
 * Fired during plugin activation.
 */
class Administration_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        // Check if Ultimate Member is active
        if (!class_exists('UM')) {
            deactivate_plugins(plugin_basename(dirname(__FILE__) . '/administration.php'));
            wp_die('This plugin requires Ultimate Member to be installed and activated.');
        }

        try {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-database.php';
            
            // Create the database tables
            Administration_Database::setup_tables();
            
            // Add default roles
            self::add_default_roles();
            
            // Add capabilities
            self::add_capabilities();
            
            // Flush rewrite rules
            flush_rewrite_rules();
        } catch (Exception $e) {
            error_log('Administration Plugin Activation Error: ' . $e->getMessage());
            wp_die('Error activating the plugin. Please check the error log for details.');
        }
    }
    
    /**
     * Add default roles for the plugin.
     */
    private static function add_default_roles() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_roles';
        
        // Check if roles already exist
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        if ($count == 0) {
            $default_roles = [
                ['RoleName' => 'Teacher'],
                ['RoleName' => 'Student'],
                ['RoleName' => 'Parent'],
                ['RoleName' => 'Administrator'],
                ['RoleName' => 'Assistant'],
            ];
            
            foreach ($default_roles as $role) {
                $wpdb->insert($table_name, $role);
            }
        }
    }
    
    /**
     * Add capabilities for WordPress roles.
     */
    private static function add_capabilities() {
        // Add admin capability
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_administration');
        }
        
        // Create custom capability for access
        update_option('administration_access_roles', ['administrator']);
    }
}
