<?php
/**
 * The public-facing functionality of the plugin.
 */
class Administration_Public {

    /**
     * Initialize the class.
     */
    public function init() {
        // Add shortcode
        add_shortcode('administration', [$this, 'render_administration']);
        
        // Enqueue public scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    /**
     * Enqueue public scripts and styles.
     */
    public function enqueue_scripts() {
        // Only enqueue if shortcode is present
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'administration')) {
            wp_enqueue_style(
                'administration-css',
                ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css',
                [],
                ADMINISTRATION_VERSION
            );
            
            // Enqueue WordPress default dashicons
            wp_enqueue_style('dashicons');
            
            wp_enqueue_script(
                'administration-admin',
                ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                ADMINISTRATION_VERSION,
                true
            );
            
            wp_localize_script(
                'administration-admin',
                'administrationData',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'rest_url' => esc_url_raw(rest_url('administration/v1/')),
                    'nonce' => wp_create_nonce('administration_nonce'),
                    'debug' => WP_DEBUG
                ]
            );
        }
    }
    
    /**
     * Render the administration interface.
     */
    public function render_administration($atts) {
        // Check if user has access
        if (!$this->user_has_access()) {
            return '<p>' . __('You do not have permission to access this content.', 'administration') . '</p>';
        }
        
        // Get data for the interface
        $programs = Administration_Database::get_all_programs();
        $persons = Administration_Database::get_all_persons();
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include ADMINISTRATION_PLUGIN_DIR . 'templates/main-interface.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Check if current user has access.
     */
    private function user_has_access() {
        // Get allowed roles
        $allowed_roles = get_option('administration_access_roles', ['administrator']);
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }
        
        // Get current user
        $user = wp_get_current_user();
        
        // Check if user has any allowed role
        foreach ($allowed_roles as $role) {
            if (in_array($role, $user->roles)) {
                return true;
            }
        }
        
        return false;
    }
}
