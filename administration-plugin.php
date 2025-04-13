<?php
/**
 * Plugin Name: Administration Plugin
 * Description: Administration plugin for Mekane Selam
 * Version: 1.0.0
 * Author: Your Name
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-public.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-ajax.php';

// Initialize classes
function initialize_administration_plugin() {
    // Initialize database
    $database = new Database();
    
    // Initialize admin
    if (is_admin()) {
        $admin = new AdminClass();
        $ajax = new AjaxClass();
    }
    
    // Initialize public functionality
    $public = new PublicClass();
}

// Hook into WordPress
add_action('init', 'initialize_administration_plugin'); 