<?php
/**
 * Fired during plugin activation
 */
class Administration_Plugin_Activator {
    /**
     * Activate the plugin
     */
    public static function activate() {
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