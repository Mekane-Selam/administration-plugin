<?php
/**
 * Fired during plugin deactivation
 */
class Administration_Plugin_Deactivator {
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clean up any plugin-specific data if needed
        // Note: We don't delete the database tables as they might contain important data
    }
} 