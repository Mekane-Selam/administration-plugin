<?php
/**
 * The public-facing functionality of the plugin.
 */
class Administration_Plugin_Public {
    /**
     * Initialize the class
     */
    public function __construct() {
        // Add any initialization code here
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'administration-plugin-public',
            ADMINISTRATION_PLUGIN_URL . 'assets/css/public.css',
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
            'administration-plugin-public',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/public.js',
            array('jquery'),
            ADMINISTRATION_PLUGIN_VERSION,
            false
        );

        // Localize the script with new data
        wp_localize_script(
            'administration-plugin-public',
            'administration_plugin_public',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('administration_plugin_public_nonce')
            )
        );
    }
} 