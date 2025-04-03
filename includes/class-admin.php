<?php
/**
 * The admin-specific functionality of the plugin.
 */
class Administration_Admin {

    /**
     * Initialize the class.
     */
    public function init() {
        // Add menu items
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Register settings
        add_action('admin_init', [$this, 'register_settings']);
        
        // Add admin scripts and styles
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Register the administration menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            'Administration',
            'Administration',
            'manage_administration',
            'administration',
            [$this, 'display_admin_page'],
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'administration',
            'Settings',
            'Settings',
            'manage_administration',
            'administration-settings',
            [$this, 'display_settings_page']
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting('administration_options', 'administration_access_roles');
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'administration') !== false) {
            wp_enqueue_style(
                'administration-admin',
                ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                ADMINISTRATION_VERSION,
                'all'
            );

            wp_enqueue_script(
                'administration-admin',
                ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                ADMINISTRATION_VERSION,
                true
            );

            wp_localize_script(
                'administration-admin-js',
                'administration_data',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('administration_nonce'),
                ]
            );
        }
    }

    /**
     * Display the main admin page.
     */
    public function display_admin_page() {
        // Get data for the interface
        $programs = Administration_Database::get_all_programs();
        $persons = Administration_Database::get_all_persons();
        $roles = Administration_Database::get_all_roles();
        
        // Include the template
        include ADMINISTRATION_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }

    /**
     * Display the settings page.
     */
    public function display_settings_page() {
        // Get current settings
        $access_roles = get_option('administration_access_roles', ['administrator']);
        
        // Include the template
        include ADMINISTRATION_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'administration-admin',
            ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ADMINISTRATION_VERSION,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'administration-admin',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ADMINISTRATION_VERSION,
            true
        );
    }
}
