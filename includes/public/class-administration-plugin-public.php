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

        // Register AJAX handlers for dashboard content
        add_action('wp_ajax_load_dashboard_page', array($this, 'ajax_load_dashboard_page'));
        add_action('wp_ajax_get_programs_overview', array($this, 'ajax_get_programs_overview'));
        add_action('wp_ajax_get_people_overview', array($this, 'ajax_get_people_overview'));
        add_action('wp_ajax_get_volunteer_ops_overview', array($this, 'ajax_get_volunteer_ops_overview'));
        add_action('wp_ajax_get_hr_overview', array($this, 'ajax_get_hr_overview'));
        add_action('wp_ajax_add_program', array($this, 'ajax_add_program'));
        add_action('wp_ajax_get_program_details', array($this, 'ajax_get_program_details'));
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

    /**
     * AJAX handler for loading dashboard page content modularly
     */
    public function ajax_load_dashboard_page() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : 'main';
        ob_start();
        switch ($page) {
            case 'main':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/dashboard-content.php';
                break;
            case 'programs':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/programs-content.php';
                break;
            case 'people':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/people-content.php';
                break;
            case 'volunteer-ops':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/volunteer-ops-content.php';
                break;
            case 'hr':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/hr-content.php';
                break;
            default:
                echo '<div class="error-message">Invalid page.</div>';
        }
        $content = ob_get_clean();
        wp_send_json_success($content);
    }

    /**
     * AJAX handler for Programs Overview widget
     */
    public function ajax_get_programs_overview() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        ob_start();
        // Modular: include a partial for programs list (create this file as needed)
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/widgets/programs-list.php';
        $content = ob_get_clean();
        wp_send_json_success($content);
    }

    /**
     * AJAX handler for People Overview widget
     */
    public function ajax_get_people_overview() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/widgets/people-list.php';
        $content = ob_get_clean();
        wp_send_json_success($content);
    }

    /**
     * AJAX handler for Volunteer Operations Overview widget
     */
    public function ajax_get_volunteer_ops_overview() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/widgets/volunteer-ops-list.php';
        $content = ob_get_clean();
        wp_send_json_success($content);
    }

    /**
     * AJAX handler for HR Overview widget
     */
    public function ajax_get_hr_overview() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/widgets/hr-list.php';
        $content = ob_get_clean();
        wp_send_json_success($content);
    }

    /**
     * AJAX handler to add a new program
     */
    public function ajax_add_program() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        $name = isset($_POST['program_name']) ? sanitize_text_field($_POST['program_name']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active';
        if (!$name) {
            wp_send_json_error('Program name is required.');
        }
        $active_flag = ($status === 'active') ? 1 : 0;
        $result = $wpdb->insert($table, array(
            'ProgramName' => $name,
            'ProgramDescription' => $description,
            'ActiveFlag' => $active_flag,
            'StartDate' => $start_date,
            'EndDate' => $end_date
        ));
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to add program.');
        }
    }

    /**
     * AJAX handler to fetch program details for the modal overlay
     */
    public function ajax_get_program_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;
        if (!$program_id) {
            wp_send_json_error('Invalid program ID.');
        }
        $program = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ProgramID = %d", $program_id));
        if (!$program) {
            wp_send_json_error('Program not found.');
        }
        ob_start();
        ?>
        <h3><?php echo esc_html($program->ProgramName); ?></h3>
        <p><strong>Type:</strong> <?php echo esc_html($program->ProgramType); ?></p>
        <p><strong>Description:</strong> <?php echo esc_html($program->ProgramDescription); ?></p>
        <p><strong>Start Date:</strong> <?php echo esc_html($program->StartDate); ?></p>
        <p><strong>End Date:</strong> <?php echo esc_html($program->EndDate); ?></p>
        <p><strong>Status:</strong> <?php echo $program->ActiveFlag ? 'Active' : 'Inactive'; ?></p>
        <button class="edit-button" data-program-id="<?php echo esc_attr($program->ProgramID); ?>">Edit Program</button>
        <?php
        $html = ob_get_clean();
        wp_send_json_success($html);
    }
} 