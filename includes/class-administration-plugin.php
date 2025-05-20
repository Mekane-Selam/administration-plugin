<?php
/**
 * The main plugin class
 */
class Administration_Plugin {
    /**
     * The loader that's responsible for maintaining and registering all hooks
     *
     * @var Administration_Plugin_Loader
     */
    protected $loader;

    /**
     * Initialize the plugin
     */
    public function __construct() {
        error_log('Administration_Plugin constructor called');
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        error_log('Calling define_sync_hooks');
        $this->define_sync_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        error_log('Administration_Plugin::load_dependencies called');
        // Load the loader class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/class-loader.php';
        
        // Load the admin class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/admin/class-administration-plugin-admin.php';
        
        // Load the public class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/public/class-administration-plugin-public.php';
        
        // Load the API class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/api/class-administration-plugin-api.php';

        // Load the database class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/database/class-administration-database.php';
        
        // Load the sync class
        require_once ADMINISTRATION_PLUGIN_PATH . 'includes/sync/class-administration-sync-members.php';
        
        // Initialize the loader
        $this->loader = new Administration_Plugin_Loader();
    }

    /**
     * Register all admin-related hooks
     */
    private function define_admin_hooks() {
        $admin = new Administration_Plugin_Admin();
        
        // Add menu items
        $this->loader->add_action('admin_menu', $admin, 'add_menu_pages');
        
        // Register admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');
    }

    /**
     * Register all public-facing hooks
     */
    private function define_public_hooks() {
        $public = new Administration_Plugin_Public();
        
        // Register public scripts and styles
        $this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $public, 'enqueue_scripts');
    }

    /**
     * Register all API-related hooks
     */
    private function define_api_hooks() {
        $api = new Administration_Plugin_API();
        
        // Register REST API routes
        $this->loader->add_action('rest_api_init', $api, 'register_routes');
    }

    /**
     * Register all sync-related hooks
     */
    private function define_sync_hooks() {
        error_log('define_sync_hooks called');
        if (class_exists('Administration_Sync_Members')) {
            error_log('Instantiating Administration_Sync_Members');
            $sync = new Administration_Sync_Members();
            $sync->init();
            error_log('Administration_Sync_Members init called');
        } else {
            error_log('Administration_Sync_Members class does not exist');
        }
    }

    /**
     * Run the plugin
     */
    public function run() {
        $this->loader->run();
    }
} 