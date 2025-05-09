<?php
/**
 * The public-facing functionality of the plugin.
 */
class Administration_Plugin_Public {
    /**
     * Initialize the class
     */
    public function __construct() {
        // Register shortcodes
        add_shortcode('administration_dashboard', array($this, 'render_dashboard'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'administration-plugin-dashboard',
            ADMINISTRATION_PLUGIN_URL . 'assets/css/public/dashboard.css',
            array(),
            ADMINISTRATION_PLUGIN_VERSION,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'administration-plugin-dashboard',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/public/dashboard.js',
            array('jquery'),
            ADMINISTRATION_PLUGIN_VERSION,
            true
        );

        // Localize the script with new data for AJAX and nonce
        wp_localize_script(
            'administration-plugin-dashboard',
            'administration_plugin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('administration_plugin_nonce')
            )
        );
    }

    /**
     * Render the administration dashboard
     *
     * @param array $atts Shortcode attributes
     * @return string The rendered dashboard HTML
     */
    public function render_dashboard($atts) {
        // Check if user is logged in and has appropriate permissions
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return '<p>' . __('You do not have permission to view this content.', 'administration-plugin') . '</p>';
        }

        // Start output buffering
        ob_start();

        // Include the dashboard template
        require_once ADMINISTRATION_PLUGIN_PATH . 'templates/public/dashboard.php';

        // Return the buffered content
        return ob_get_clean();
    }
} 