<?php
/**
 * Plugin Name: Administration Plugin
 * Plugin URI: https://mekaneselam.org
 * Description: WordPress plugin for managing program administration and user synchronization with Ultimate Member.
 * Version: 1.0.0
 * Author: Mekane Selam
 * Author URI: https://mekaneselam.org
 * Text Domain: administration-plugin
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('ADMINISTRATION_PLUGIN_VERSION', '1.0.0');
define('ADMINISTRATION_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ADMINISTRATION_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader
require_once ADMINISTRATION_PLUGIN_PATH . 'includes/class-autoloader.php';

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array('Administration_Plugin_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('Administration_Plugin_Deactivator', 'deactivate'));

// Initialize the plugin
function run_administration_plugin() {
    $plugin = new Administration_Plugin();
    $plugin->run();
}
run_administration_plugin(); 