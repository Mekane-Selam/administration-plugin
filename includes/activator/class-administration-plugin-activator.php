<?php
/**
 * Fired during plugin activation
 */
class Administration_Plugin_Activator {
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Check if Ultimate Member is active
        if (!class_exists('UM')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('This plugin requires Ultimate Member to be installed and activated.');
        }

        // Create database tables
        self::setup_database();
    }

    /**
     * Setup database tables
     */
    private static function setup_database() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/database/class-administration-database.php';
        Administration_Database::setup_tables();
    }
} 