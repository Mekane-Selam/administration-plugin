<?php
/**
 * The admin-specific functionality of the plugin.
 */
class Administration_Plugin_Admin {
    /**
     * Initialize the class
     */
    public function __construct() {
        // Add any initialization code here
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'administration-plugin-admin',
            ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            ADMINISTRATION_PLUGIN_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'administration-plugin-admin',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            ADMINISTRATION_PLUGIN_VERSION,
            false
        );
    }

    /**
     * Add menu pages to the admin area
     */
    public function add_menu_pages() {
        // Main menu
        add_menu_page(
            'Administration',
            'Administration',
            'manage_options',
            'administration',
            array($this, 'display_main_page'),
            'dashicons-admin-generic',
            30
        );

        // Submenu pages
        add_submenu_page(
            'administration',
            'Programs',
            'Programs',
            'manage_options',
            'administration-programs',
            array($this, 'display_programs_page')
        );

        add_submenu_page(
            'administration',
            'People',
            'People',
            'manage_options',
            'administration-people',
            array($this, 'display_people_page')
        );

        add_submenu_page(
            'administration',
            'Volunteer Operations',
            'Volunteer Ops',
            'manage_options',
            'administration-volunteer-ops',
            array($this, 'display_volunteer_ops_page')
        );

        add_submenu_page(
            'administration',
            'HR',
            'HR',
            'manage_options',
            'administration-hr',
            array($this, 'display_hr_page')
        );
    }

    /**
     * Display the main admin page
     */
    public function display_main_page() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/admin/main-page.php';
    }

    /**
     * Display the programs page
     */
    public function display_programs_page() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/admin/programs-page.php';
    }

    /**
     * Display the people page
     */
    public function display_people_page() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/admin/people-page.php';
    }

    /**
     * Display the volunteer operations page
     */
    public function display_volunteer_ops_page() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/admin/volunteer-ops-page.php';
    }

    /**
     * Display the HR page
     */
    public function display_hr_page() {
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/admin/hr-page.php';
    }
} 