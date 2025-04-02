<?php
/**
 * Plugin Name: Administration
 * Plugin URI: https://example.com/administration-plugin
 * Description: A plugin that syncs with Ultimate Member and manages custom program data.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: administration
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('ADMINISTRATION_VERSION', '1.0.0');
define('ADMINISTRATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ADMINISTRATION_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Activation function
 */
function activate_administration() {
    require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-activator.php';
    Administration_Activator::activate();
}

/**
 * Deactivation function
 */
function deactivate_administration() {
    require_once ADMINISTRATION_PLUGIN_DIR . 'includes/class-deactivator.php';
    Administration_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_administration');
register_deactivation_hook(__FILE__, 'deactivate_administration');

/**
 * The core plugin class
 */
$required_files = [
    'includes/class-admin.php',
    'includes/class-database.php',
    'includes/class-sync.php',
    'includes/class-api.php',
    'public/class-public.php'
];

foreach ($required_files as $file) {
    $file_path = ADMINISTRATION_PLUGIN_DIR . $file;
    if (!file_exists($file_path)) {
        error_log('Administration Plugin Error: Required file not found: ' . $file_path);
        wp_die('Error: Required plugin file not found. Please check the error log for details.');
    }
    require_once $file_path;
}

/**
 * Initialize the plugin
 */
function run_administration() {
    // Initialize admin
    $admin = new Administration_Admin();
    $admin->init();

    // Initialize public
    $public = new Administration_Public();
    $public->init();

    // Initialize API
    $api = new Administration_API();
    $api->init();

    // Initialize Sync
    $sync = new Administration_Sync();
    $sync->init();
}

run_administration();
