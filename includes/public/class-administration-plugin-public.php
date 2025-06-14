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
        add_shortcode('careers_job_list', array($this, 'render_careers_job_list'));
        add_shortcode('administration_member_profile', array($this, 'render_member_profile'));

        // Register AJAX handlers for dashboard content
        add_action('wp_ajax_load_dashboard_page', array($this, 'ajax_load_dashboard_page'));
        add_action('wp_ajax_get_programs_overview', array($this, 'ajax_get_programs_overview'));
        add_action('wp_ajax_get_people_overview', array($this, 'ajax_get_people_overview'));
        add_action('wp_ajax_get_volunteer_ops_overview', array($this, 'ajax_get_volunteer_ops_overview'));
        add_action('wp_ajax_get_hr_overview', array($this, 'ajax_get_hr_overview'));
        add_action('wp_ajax_add_program', array($this, 'ajax_add_program'));
        add_action('wp_ajax_get_program_details', array($this, 'ajax_get_program_details'));
        add_action('wp_ajax_edit_program', array($this, 'ajax_edit_program'));
        add_action('wp_ajax_get_people_for_owner_select', array($this, 'ajax_get_people_for_owner_select'));
        // Register AJAX handler for force syncing users
        add_action('wp_ajax_administration_force_sync_users', array($this, 'ajax_force_sync_users'));
        add_action('wp_ajax_nopriv_administration_force_sync_users', array($this, 'ajax_force_sync_users'));
        add_action('wp_ajax_get_people_list', array($this, 'ajax_get_people_list'));
        add_action('wp_ajax_nopriv_get_people_list', array($this, 'ajax_get_people_list'));
        add_action('wp_ajax_add_person', array($this, 'ajax_add_person'));
        add_action('wp_ajax_nopriv_add_person', array($this, 'ajax_add_person'));
        add_action('wp_ajax_get_person', array($this, 'ajax_get_person'));
        add_action('wp_ajax_nopriv_get_person', array($this, 'ajax_get_person'));
        add_action('wp_ajax_edit_person', array($this, 'ajax_edit_person'));
        add_action('wp_ajax_nopriv_edit_person', array($this, 'ajax_edit_person'));
        // Register new AJAX handler
        add_action('wp_ajax_get_program_full_view', array($this, 'ajax_get_program_full_view'));
        add_action('wp_ajax_add_edu_course', array($this, 'ajax_add_edu_course'));
        add_action('wp_ajax_get_course_detail_and_enrollments', array($this, 'ajax_get_course_detail_and_enrollments'));
        add_action('wp_ajax_get_course_detail_tabs', array($this, 'ajax_get_course_detail_tabs'));
        add_action('wp_ajax_add_course_enrollment', array($this, 'ajax_add_course_enrollment'));
        add_action('wp_ajax_get_people_enrolled_in_program', array($this, 'ajax_get_people_enrolled_in_program'));
        // Register new AJAX handler
        add_action('wp_ajax_get_person_details', array($this, 'ajax_get_person_details'));
        add_action('wp_ajax_add_staff_role', array($this, 'ajax_add_staff_role'));
        add_action('wp_ajax_add_staff_member', array($this, 'ajax_add_staff_member'));
        add_action('wp_ajax_get_program_staff_list', array($this, 'ajax_get_program_staff_list'));
        add_action('wp_ajax_update_course_instructors', array($this, 'ajax_update_course_instructors'));
        add_action('wp_ajax_get_full_person_details', array(
            $this, 'ajax_get_full_person_details'));
        // Register new AJAX handler
        add_action('wp_ajax_search_people', array($this, 'ajax_search_people'));
        // Register new AJAX handler
        add_action('wp_ajax_save_person_relationships', array($this, 'ajax_save_person_relationships'));
        // Register AJAX handlers for job postings
        add_action('wp_ajax_get_job_postings_list', array($this, 'ajax_get_job_postings_list'));
        add_action('wp_ajax_add_job_posting', array($this, 'ajax_add_job_posting'));
        add_action('wp_ajax_get_job_posting_details', array($this, 'ajax_get_job_posting_details'));
        add_action('wp_ajax_get_job_posting_full_view', array($this, 'ajax_get_job_posting_full_view'));
        add_action('wp_ajax_get_programs_for_select', array($this, 'ajax_get_programs_for_select'));
        add_action('wp_ajax_edit_job_posting', array($this, 'ajax_edit_job_posting'));
        add_action('wp_ajax_delete_job_posting', array($this, 'ajax_delete_job_posting'));
        // Register AJAX handlers for job applicants (admin and public)
        add_action('wp_ajax_get_job_applicants_list', array($this, 'ajax_get_job_applicants_list'));
        add_action('wp_ajax_nopriv_get_job_applicants_list', array($this, 'ajax_get_job_applicants_list'));
        add_action('wp_ajax_get_job_applicant_details', array($this, 'ajax_get_job_applicant_details'));
        add_action('wp_ajax_nopriv_get_job_applicant_details', array($this, 'ajax_get_job_applicant_details'));
        add_action('wp_ajax_update_job_applicant_status', array($this, 'ajax_update_job_applicant_status'));
        add_action('wp_ajax_nopriv_update_job_applicant_status', array($this, 'ajax_update_job_applicant_status'));
        add_action('wp_ajax_update_job_applicant_notes', array($this, 'ajax_update_job_applicant_notes'));
        add_action('wp_ajax_nopriv_update_job_applicant_notes', array($this, 'ajax_update_job_applicant_notes'));
        add_action('wp_ajax_get_course_assignments', array($this, 'ajax_get_course_assignments'));
        add_action('wp_ajax_nopriv_get_course_assignments', array($this, 'ajax_get_course_assignments'));
        add_action('wp_ajax_get_assignment_details', array($this, 'ajax_get_assignment_details'));
        add_action('wp_ajax_nopriv_get_assignment_details', array($this, 'ajax_get_assignment_details'));
        add_action('wp_ajax_add_assignment', array($this, 'ajax_add_assignment'));
        add_action('wp_ajax_nopriv_add_assignment', array($this, 'ajax_add_assignment'));
        add_action('wp_ajax_edit_assignment', array($this, 'ajax_edit_assignment'));
        add_action('wp_ajax_nopriv_edit_assignment', array($this, 'ajax_edit_assignment'));
        add_action('wp_ajax_delete_assignment', array($this, 'ajax_delete_assignment'));
        add_action('wp_ajax_nopriv_delete_assignment', array($this, 'ajax_delete_assignment'));
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
        wp_enqueue_style(
            'administration-plugin-program-view',
            ADMINISTRATION_PLUGIN_URL . 'assets/css/public/program-view.css',
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
            'administration-plugin-program-view',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/public/program-view.js',
            array('jquery'),
            ADMINISTRATION_PLUGIN_VERSION,
            true
        );
        wp_enqueue_script(
            'administration-plugin-dashboard',
            ADMINISTRATION_PLUGIN_URL . 'assets/js/public/dashboard.js',
            array('jquery', 'administration-plugin-program-view'),
            ADMINISTRATION_PLUGIN_VERSION,
            true
        );

        // Define program types for localization
        $program_types = array('Education', 'Health', 'Social');

        // Localize the script with new data for AJAX and nonce
        wp_localize_script(
            'administration-plugin-dashboard',
            'administration_plugin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('administration_plugin_nonce'),
                'program_types' => $program_types
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
        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : 'parish';
        ob_start();
        switch ($page) {
            case 'main':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/dashboard-content.php';
                break;
            case 'programs':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/programs-content.php';
                break;
            case 'parish':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/people-content.php';
                break;
            case 'calendar':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/calendar-content.php';
                break;
            case 'hr':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/hr-content.php';
                break;
            case 'finances':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/finances-content.php';
                break;
            case 'resources':
                include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/resources-content.php';
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
        $type = isset($_POST['program_type']) ? sanitize_text_field($_POST['program_type']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active';
        $owner = isset($_POST['program_owner']) ? sanitize_text_field($_POST['program_owner']) : '';
        if (!$name || !$type || !$owner) {
            wp_send_json_error('Program name, type, and owner are required.');
        }
        $active_flag = ($status === 'active') ? 1 : 0;
        // Generate unique ProgramID (PROGxxxxx)
        do {
            $unique_code = mt_rand(10000, 99999);
            $program_id = 'PROG' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ProgramID = %s", $program_id));
        } while ($exists);
        $result = $wpdb->insert($table, array(
            'ProgramID' => $program_id,
            'ProgramName' => $name,
            'ProgramType' => $type,
            'ProgramDescription' => $description,
            'ActiveFlag' => $active_flag,
            'StartDate' => $start_date,
            'EndDate' => $end_date,
            'ProgramOwner' => $owner
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
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$program_id) {
            wp_send_json_error('Invalid program ID.');
        }
        $program = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ProgramID = %s", $program_id));
        if (!$program) {
            wp_send_json_error('Program not found.');
        }
        ob_start();
        ?>
        <div class="program-details-modal-content">
            <h3 class="program-details-title"><?php echo esc_html($program->ProgramName); ?></h3>
            <div class="program-details-fields">
                <div class="program-details-row"><span class="program-details-label">Type:</span> <span class="program-details-value"><?php echo esc_html($program->ProgramType); ?></span></div>
                <div class="program-details-row"><span class="program-details-label">Description:</span> <span class="program-details-value"><?php echo esc_html($program->ProgramDescription); ?></span></div>
                <div class="program-details-row"><span class="program-details-label">Start Date:</span> <span class="program-details-value"><?php echo esc_html($program->StartDate); ?></span></div>
                <div class="program-details-row"><span class="program-details-label">End Date:</span> <span class="program-details-value"><?php echo esc_html($program->EndDate); ?></span></div>
                <div class="program-details-row"><span class="program-details-label">Status:</span> <span class="program-details-value"><?php echo $program->ActiveFlag ? 'Active' : 'Inactive'; ?></span></div>
            </div>
            <div class="program-details-actions program-details-actions-centered">
                <a href="#" class="button program-goto-btn modern-goto-btn" data-program-id="<?php echo esc_attr($program->ProgramID); ?>">Go to Program</a>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        wp_send_json_success($html);
    }

    /**
     * AJAX handler to edit a program
     */
    public function ajax_edit_program() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        $name = isset($_POST['program_name']) ? sanitize_text_field($_POST['program_name']) : '';
        $type = isset($_POST['program_type']) ? sanitize_text_field($_POST['program_type']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'active';
        $owner = isset($_POST['program_owner']) ? sanitize_text_field($_POST['program_owner']) : '';
        if (!$program_id || !$name || !$owner) {
            wp_send_json_error('Missing required fields.');
        }
        $active_flag = ($status === 'active') ? 1 : 0;
        $result = $wpdb->update(
            $table,
            array(
                'ProgramName' => $name,
                'ProgramType' => $type,
                'ProgramDescription' => $description,
                'StartDate' => $start_date,
                'EndDate' => $end_date,
                'ActiveFlag' => $active_flag,
                'ProgramOwner' => $owner
            ),
            array('ProgramID' => $program_id)
        );
        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update program.');
        }
    }

    public function ajax_get_people_for_owner_select() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        $people = $wpdb->get_results("SELECT PersonID, FirstName, LastName FROM $table ORDER BY LastName, FirstName");
        wp_send_json_success($people);
    }

    /**
     * AJAX handler to force sync all users to core_person
     */
    public function ajax_force_sync_users() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        // Ensure the sync class is loaded
        if (!class_exists('Administration_Sync_Members')) {
            require_once ADMINISTRATION_PLUGIN_PATH . 'includes/sync/class-administration-sync-members.php';
        }
        $sync = new Administration_Sync_Members();
        $sync->force_sync_all_users();
        wp_send_json_success(['message' => 'User synchronization completed.']);
    }

    /**
     * AJAX handler to get all people for the people list
     */
    public function ajax_get_people_list() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : '';
        $allowed_sorts = ['first_name', 'last_name', 'email'];
        $order_by = 'LastName, FirstName';
        if (in_array($sort, $allowed_sorts)) {
            if ($sort === 'first_name') $order_by = 'FirstName, LastName';
            if ($sort === 'last_name') $order_by = 'LastName, FirstName';
            if ($sort === 'email') $order_by = 'Email, LastName';
        }
        $where = '';
        $params = array();
        if ($search) {
            $where = "WHERE FirstName LIKE %s OR LastName LIKE %s OR Email LIKE %s";
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params = array($like, $like, $like);
        }
        $sql = "SELECT PersonID, FirstName, LastName, Email FROM $table $where ORDER BY $order_by";
        $people = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);
        ob_start();
        if ($people && count($people) > 0) {
            foreach ($people as $person) {
                echo '<div class="person-row" data-person-id="' . esc_attr($person->PersonID) . '">';
                echo '<span class="person-name">' . esc_html($person->FirstName . ' ' . $person->LastName) . '</span>';
                echo '<span class="person-email">' . esc_html($person->Email) . '</span>';
                echo '</div>';
            }
        } else {
            echo '<div class="no-people">No people found.</div>';
        }
        $html = ob_get_clean();
        wp_send_json_success($html);
    }

    /**
     * AJAX handler to add a new person
     */
    public function ajax_add_person() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!$first_name || !$last_name) {
            wp_send_json_error('First name and last name are required.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        if ($email) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE Email = %s", $email));
            if ($exists) {
                wp_send_json_error('A person with this email already exists.');
            }
        }
        $person_data = [
            'FirstName' => $first_name,
            'LastName' => $last_name,
            'Email' => $email
        ];
        $result = Administration_Database::save_person($person_data);
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to add person.');
        }
    }

    /**
     * AJAX handler to get a person's data
     */
    public function ajax_get_person() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
        if (!$person_id) {
            wp_send_json_error('Missing person ID.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        $person = $wpdb->get_row($wpdb->prepare("SELECT PersonID, FirstName, LastName, Email FROM $table WHERE PersonID = %s", $person_id));
        if (!$person) {
            wp_send_json_error('Person not found.');
        }
        wp_send_json_success($person);
    }

    /**
     * AJAX handler to edit a person's data
     */
    public function ajax_edit_person() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        error_log('ajax_edit_person POST: ' . print_r($_POST, true));
        $person_id = isset($_POST['person_id']) ? trim(sanitize_text_field($_POST['person_id'])) : '';
        error_log('Trimmed person_id: ' . $person_id);
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!$person_id || !$first_name || !$email) {
            wp_send_json_error('First name and email are required.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        // Check for duplicate email (exclude current person)
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE Email = %s AND PersonID != %s", $email, $person_id));
        if ($exists) {
            wp_send_json_error('A person with this email already exists.');
        }
        $person_data = [
            'PersonID' => $person_id,
            'FirstName' => $first_name,
            'LastName' => $last_name,
            'Email' => $email,
            'Title' => isset($_POST['Title']) ? sanitize_text_field($_POST['Title']) : '',
            'Gender' => isset($_POST['Gender']) ? sanitize_text_field($_POST['Gender']) : '',
            'Phone' => isset($_POST['Phone']) ? sanitize_text_field($_POST['Phone']) : '',
            'AddressLine1' => isset($_POST['AddressLine1']) ? sanitize_text_field($_POST['AddressLine1']) : '',
            'AddressLine2' => isset($_POST['AddressLine2']) ? sanitize_text_field($_POST['AddressLine2']) : '',
            'City' => isset($_POST['City']) ? sanitize_text_field($_POST['City']) : '',
            'State' => isset($_POST['State']) ? sanitize_text_field($_POST['State']) : '',
            'Zip' => isset($_POST['Zip']) ? sanitize_text_field($_POST['Zip']) : '',
            'Birthday' => isset($_POST['Birthday']) ? sanitize_text_field($_POST['Birthday']) : null,
        ];
        $result = Administration_Database::save_person($person_data);
        error_log('save_person result: ' . print_r($result, true));
        // Check if update matched any rows
        $row_check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE PersonID = %s", $person_id));
        error_log('Row check for person_id ' . $person_id . ': ' . $row_check);
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update person.');
        }
    }

    /**
     * AJAX handler to get the full program-specific view
     */
    public function ajax_get_program_full_view() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$program_id) {
            wp_send_json_error('Invalid program ID.');
        }
        $program = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ProgramID = %s", $program_id));
        if (!$program) {
            wp_send_json_error('Program not found.');
        }
        $type = strtolower($program->ProgramType);
        $template_file = ADMINISTRATION_PLUGIN_PATH . 'templates/public/program-types/' . $type . '.php';
        if (!file_exists($template_file)) {
            wp_send_json_error('No template for this program type.');
        }
        ob_start();
        include $template_file;
        $html = ob_get_clean();
        wp_send_json_success([
            'html' => $html,
            'program' => $program
        ]);
    }

    /**
     * AJAX handler to add a course to an education program
     */
    public function ajax_add_edu_course() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'progtype_edu_courses';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        $course_name = isset($_POST['CourseName']) ? sanitize_text_field($_POST['CourseName']) : '';
        $level = isset($_POST['Level']) ? sanitize_text_field($_POST['Level']) : '';
        if (!$program_id || !$course_name) {
            wp_send_json_error('Missing required fields.');
        }
        // Generate unique CourseID
        do {
            $unique_code = mt_rand(10000, 99999);
            $course_id = 'COURSE' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE CourseID = %s", $course_id));
        } while ($exists);
        $result = $wpdb->insert($table, array(
            'CourseID' => $course_id,
            'ProgramID' => $program_id,
            'CourseName' => $course_name,
            'Level' => $level
        ));
        if ($result) {
            wp_send_json_success(['CourseID' => $course_id]);
        } else {
            wp_send_json_error('Failed to add course.');
        }
    }

    /**
     * AJAX handler to get course details and enrollments
     */
    public function ajax_get_course_detail_and_enrollments() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$course_id || !$program_id) {
            wp_send_json_error('Missing required fields.');
        }
        $course = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}progtype_edu_courses WHERE CourseID = %s AND ProgramID = %s", $course_id, $program_id));
        $enrollments = $wpdb->get_results($wpdb->prepare("SELECT ce.*, p.FirstName, p.LastName FROM {$wpdb->prefix}progtype_edu_courseenrollments ce LEFT JOIN {$wpdb->prefix}core_person p ON ce.PersonID = p.PersonID WHERE ce.CourseID = %s ORDER BY ce.EnrollmentDate DESC", $course_id));
        ob_start();
        include dirname(__FILE__,3) . '/templates/public/partials/course-detail-overview.php';
        $overview_html = ob_get_clean();
        ob_start();
        include dirname(__FILE__,3) . '/templates/public/partials/course-detail-enrollments.php';
        $enrollments_html = ob_get_clean();
        wp_send_json_success(['overview_html' => $overview_html, 'enrollments_html' => $enrollments_html]);
    }

    /**
     * AJAX handler to get course details and enrollments
     */
    public function ajax_get_course_detail_tabs() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$course_id || !$program_id) {
            wp_send_json_error('Missing required fields.');
        }
        // Fetch course
        $course_table = $wpdb->prefix . 'progtype_edu_courses';
        $course = $wpdb->get_row($wpdb->prepare("SELECT * FROM $course_table WHERE CourseID = %s", $course_id));
        // Fetch enrollments for this program (not by course)
        $enroll_table = $wpdb->prefix . 'progtype_edu_courseenrollments';
        $person_table = $wpdb->prefix . 'core_person';
        $enrollments = $wpdb->get_results($wpdb->prepare(
            "SELECT e.*, p.FirstName, p.LastName FROM {$enroll_table} AS e LEFT JOIN {$person_table} AS p ON e.PersonID = p.PersonID WHERE e.CourseID = %s ORDER BY e.EnrollmentDate DESC",
            $course_id
        ));
        ob_start();
        include plugin_dir_path(__FILE__) . '../../templates/public/partials/course-detail.php';
        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }

    /**
     * AJAX handler to add an enrollment to a course (progtype_edu_courseenrollments)
     */
    public function ajax_add_course_enrollment() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $course_enroll_table = $wpdb->prefix . 'progtype_edu_courseenrollments';
        $program_enroll_table = $wpdb->prefix . 'progtype_edu_enrollment';
        $course_id = isset($_POST['CourseID']) ? sanitize_text_field($_POST['CourseID']) : '';
        $person_id = isset($_POST['PersonID']) ? sanitize_text_field($_POST['PersonID']) : '';
        $active_flag = isset($_POST['ActiveFlag']) ? intval($_POST['ActiveFlag']) : 1;
        $enrollment_date = isset($_POST['EnrollmentDate']) ? sanitize_text_field($_POST['EnrollmentDate']) : current_time('mysql', 1);
        $course_enrollment_id = isset($_POST['CourseEnrollmentID']) ? sanitize_text_field($_POST['CourseEnrollmentID']) : '';

        if (!$course_id || !$person_id) {
            wp_send_json_error('Missing required fields.');
        }

        // Get the program ID for this course
        $course = $wpdb->get_row($wpdb->prepare(
            "SELECT ProgramID FROM {$wpdb->prefix}progtype_edu_courses WHERE CourseID = %s",
            $course_id
        ));

        if (!$course) {
            wp_send_json_error('Course not found.');
            return;
        }

        // Check if person is actively enrolled in the program
        $program_enrollment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $program_enroll_table WHERE ProgramID = %s AND PersonID = %s AND ActiveFlag = 1",
            $course->ProgramID,
            $person_id
        ));

        if (!$program_enrollment) {
            wp_send_json_error('This person must be actively enrolled in the program before enrolling in a course.');
            return;
        }

        // Check if person is already actively enrolled in the course
        $existing_course_enrollment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $course_enroll_table WHERE CourseID = %s AND PersonID = %s AND ActiveFlag = 1",
            $course_id,
            $person_id
        ));

        if ($existing_course_enrollment) {
            wp_send_json_error('This person is already actively enrolled in this course.');
            return;
        }

        if (!$course_enrollment_id) {
            do {
                $unique_code = mt_rand(10000, 99999);
                $course_enrollment_id = 'CORENROL' . $unique_code;
                $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $course_enroll_table WHERE CourseEnrollmentID = %s", $course_enrollment_id));
            } while ($exists);
        }

        $result = $wpdb->insert($course_enroll_table, array(
            'CourseEnrollmentID' => $course_enrollment_id,
            'CourseID' => $course_id,
            'PersonID' => $person_id,
            'ActiveFlag' => $active_flag,
            'EnrollmentDate' => $enrollment_date
        ));

        if ($result) {
            wp_send_json_success(['CourseEnrollmentID' => $course_enrollment_id]);
        } else {
            wp_send_json_error('Failed to add enrollment.');
        }
    }

    /**
     * AJAX handler to get people enrolled in a program (for course enrollment modal)
     */
    public function ajax_get_people_enrolled_in_program() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$program_id) {
            wp_send_json_error('Missing program ID.');
        }
        $enroll_table = $wpdb->prefix . 'progtype_edu_enrollment';
        $person_table = $wpdb->prefix . 'core_person';
        $people = $wpdb->get_results($wpdb->prepare(
            "SELECT p.PersonID, p.FirstName, p.LastName FROM {$enroll_table} AS e LEFT JOIN {$person_table} AS p ON e.PersonID = p.PersonID WHERE e.ProgramID = %s ORDER BY p.LastName, p.FirstName",
            $program_id
        ));
        wp_send_json_success($people);
    }

    /**
     * AJAX handler to get person details
     */
    public function ajax_get_person_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
        if (!$person_id) {
            wp_send_json_error('Missing person ID.');
        }
        $person_table = $wpdb->prefix . 'core_person';
        $person = $wpdb->get_row($wpdb->prepare("SELECT * FROM $person_table WHERE PersonID = %s", $person_id));
        if ($person) {
            wp_send_json_success($person);
        } else {
            wp_send_json_error('Person not found.');
        }
    }

    /**
     * AJAX handler to add a new staff role
     */
    public function ajax_add_staff_role() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_roles';
        $staff_role_id = isset($_POST['StaffRoleID']) ? sanitize_text_field($_POST['StaffRoleID']) : '';
        $role_title = isset($_POST['RoleTitle']) ? sanitize_text_field($_POST['RoleTitle']) : '';
        $role_desc = isset($_POST['StaffRoleDescription']) ? sanitize_textarea_field($_POST['StaffRoleDescription']) : '';
        if (!$staff_role_id || !$role_title) {
            wp_send_json_error('Missing required fields.');
        }
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE StaffRoleID = %s", $staff_role_id));
        if ($exists) {
            wp_send_json_error('A role with this ID already exists.');
        }
        $result = $wpdb->insert($table, array(
            'StaffRoleID' => $staff_role_id,
            'RoleTitle' => $role_title,
            'StaffRoleDescription' => $role_desc
        ));
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to add staff role.');
        }
    }

    /**
     * AJAX handler to add a new staff member
     */
    public function ajax_add_staff_member() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_staff';
        $person_id = isset($_POST['PersonID']) ? sanitize_text_field($_POST['PersonID']) : '';
        $staff_roles_id = isset($_POST['StaffRolesID']) ? sanitize_text_field($_POST['StaffRolesID']) : '';
        $program_id = isset($_POST['ProgramID']) ? sanitize_text_field($_POST['ProgramID']) : '';
        if (!$person_id || !$staff_roles_id || !$program_id) {
            wp_send_json_error('Missing required fields.');
        }
        // Prevent duplicate staff for the same program
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE PersonID = %s AND ProgramID = %s", $person_id, $program_id));
        if ($exists) {
            wp_send_json_error('This person is already a staff member for this program.');
        }
        $result = $wpdb->insert($table, array(
            'PersonID' => $person_id,
            'StaffRolesID' => $staff_roles_id,
            'ProgramID' => $program_id
        ));
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to add staff member.');
        }
    }

    /**
     * AJAX handler to get program staff list
     */
    public function ajax_get_program_staff_list() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        if (!$program_id) {
            wp_send_json_error('Missing program ID.');
        }
        $staff_table = $wpdb->prefix . 'hr_staff';
        $person_table = $wpdb->prefix . 'core_person';
        $staff_roles_table = $wpdb->prefix . 'hr_roles';
        $staff = $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, p.FirstName, p.LastName, sr.RoleTitle 
            FROM $staff_table s 
            LEFT JOIN $person_table p ON s.PersonID = p.PersonID 
            LEFT JOIN $staff_roles_table sr ON s.StaffRolesID = sr.StaffRoleID 
            WHERE s.ProgramID = %s 
            ORDER BY sr.RoleTitle, p.LastName, p.FirstName",
            $program_id
        ));
        wp_send_json_success($staff);
    }

    /**
     * AJAX handler to update course instructors
     */
    public function ajax_update_course_instructors() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'progtype_edu_courses';
        $course_id = isset($_POST['CourseID']) ? sanitize_text_field($_POST['CourseID']) : '';
        $primary = isset($_POST['PrimaryInstructorID']) ? sanitize_text_field($_POST['PrimaryInstructorID']) : '';
        $backup1 = isset($_POST['BackUpTeacher1ID']) ? sanitize_text_field($_POST['BackUpTeacher1ID']) : '';
        $backup2 = isset($_POST['BackUpTeacher2ID']) ? sanitize_text_field($_POST['BackUpTeacher2ID']) : '';
        if (!$course_id || !$primary) {
            wp_send_json_error('Missing required fields.');
        }
        $result = $wpdb->update(
            $table,
            array(
                'PrimaryInstructorID' => $primary,
                'BackUpTeacher1ID' => $backup1,
                'BackUpTeacher2ID' => $backup2
            ),
            array('CourseID' => $course_id)
        );
        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update instructors.');
        }
    }

    /**
     * Helper: Get reverse relationship type
     */
    private function get_reverse_relationship_type($type) {
        $map = [
            'Mother' => 'Child',
            'Father' => 'Child',
            'Child' => 'Parent',
            'Sibling' => 'Sibling',
            'Other' => 'Other',
            // Add more as needed
        ];
        return isset($map[$type]) ? $map[$type] : $type;
    }

    /**
     * AJAX handler to get full person details (general, family, roles)
     */
    public function ajax_get_full_person_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
        if (!$person_id) {
            wp_send_json_error('Missing person ID.');
        }
        $person_table = $wpdb->prefix . 'core_person';
        $rel_table = $wpdb->prefix . 'core_person_relationships';
        $roles_table = $wpdb->prefix . 'core_roles';
        $person_roles_table = $wpdb->prefix . 'core_person_roles';
        $programs_table = $wpdb->prefix . 'core_programs';

        // General info
        $general = $wpdb->get_row($wpdb->prepare("SELECT * FROM $person_table WHERE PersonID = %s", $person_id), ARRAY_A);
        if (!$general) {
            wp_send_json_error('Person not found.');
        }

        // Relationships (show both sides)
        $relationships = [];
        // Direct relationships
        $rels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $rel_table WHERE PersonID = %s", $person_id));
        foreach ($rels as $rel) {
            $related = $wpdb->get_row($wpdb->prepare("SELECT PersonID, FirstName, LastName FROM $person_table WHERE PersonID = %s", $rel->RelatedPersonID));
            if ($related) {
                $relationships[] = [
                    'RelatedPersonID' => $related->PersonID,
                    'RelatedPersonName' => $related->FirstName . ' ' . $related->LastName,
                    'RelationshipType' => $rel->RelationshipType
                ];
            }
        }
        // Reverse relationships
        $reverse_rels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $rel_table WHERE RelatedPersonID = %s", $person_id));
        foreach ($reverse_rels as $rel) {
            $related = $wpdb->get_row($wpdb->prepare("SELECT PersonID, FirstName, LastName FROM $person_table WHERE PersonID = %s", $rel->PersonID));
            if ($related) {
                $relationships[] = [
                    'RelatedPersonID' => $related->PersonID,
                    'RelatedPersonName' => $related->FirstName . ' ' . $related->LastName,
                    'RelationshipType' => $this->get_reverse_relationship_type($rel->RelationshipType)
                ];
            }
        }

        // Roles (with program) - core_person_roles
        $roles = $wpdb->get_results($wpdb->prepare(
            "SELECT pr.PersonRoleID, r.RoleName, p.ProgramName FROM $person_roles_table pr
            LEFT JOIN $roles_table r ON pr.RoleID = r.RoleID
            LEFT JOIN $programs_table p ON pr.ProgramID = p.ProgramID
            WHERE pr.PersonID = %s AND pr.ActiveFlag = 1",
            $person_id
        ));
        $roles_arr = [];
        foreach ($roles as $role) {
            $roles_arr[] = [
                'RoleName' => $role->RoleName,
                'RoleTitle' => $role->RoleName, // for compatibility with frontend
                'ProgramName' => $role->ProgramName
            ];
        }
        // HR Staff roles
        $hr_staff_table = $wpdb->prefix . 'hr_staff';
        $hr_roles_table = $wpdb->prefix . 'hr_roles';
        $hr_programs_table = $wpdb->prefix . 'core_programs';
        $hr_roles = $wpdb->get_results($wpdb->prepare(
            "SELECT s.StaffRolesID, r.RoleTitle, p.ProgramName FROM $hr_staff_table s
            LEFT JOIN $hr_roles_table r ON s.StaffRolesID = r.StaffRoleID
            LEFT JOIN $hr_programs_table p ON s.ProgramID = p.ProgramID
            WHERE s.PersonID = %s",
            $person_id
        ));
        foreach ($hr_roles as $role) {
            $roles_arr[] = [
                'RoleName' => $role->RoleTitle,
                'RoleTitle' => $role->RoleTitle,
                'ProgramName' => $role->ProgramName
            ];
        }
        wp_send_json_success([
            'general' => $general,
            'relationships' => $relationships,
            'roles' => $roles_arr
        ]);
    }

    /**
     * AJAX handler to search people for relationship typeahead
     */
    public function ajax_search_people() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $q = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';
        $exclude_id = isset($_POST['exclude_id']) ? sanitize_text_field($_POST['exclude_id']) : '';
        $table = $wpdb->prefix . 'core_person';
        $where = '';
        $params = [];
        if ($q) {
            $where = "WHERE (FirstName LIKE %s OR LastName LIKE %s OR Email LIKE %s)";
            $like = '%' . $wpdb->esc_like($q) . '%';
            $params = [$like, $like, $like];
        }
        if ($exclude_id) {
            $where .= ($where ? ' AND ' : 'WHERE ') . 'PersonID != %s';
            $params[] = $exclude_id;
        }
        $sql = "SELECT PersonID, FirstName, LastName, Email FROM $table $where ORDER BY LastName, FirstName LIMIT 20";
        $people = $params ? $wpdb->get_results($wpdb->prepare($sql, ...$params)) : $wpdb->get_results($sql);
        $results = [];
        foreach ($people as $p) {
            $results[] = [
                'PersonID' => $p->PersonID,
                'Name' => $p->FirstName . ' ' . $p->LastName,
                'Email' => $p->Email
            ];
        }
        wp_send_json_success($results);
    }

    /**
     * AJAX handler to save relationships for a person
     */
    public function ajax_save_person_relationships() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
        $relationships = isset($_POST['relationships']) ? $_POST['relationships'] : [];
        $rel_table = $wpdb->prefix . 'core_person_relationships';
        if (!$person_id) {
            wp_send_json_error('Missing person ID.');
        }
        // Fetch all existing relationships for this person
        $existing = $wpdb->get_results($wpdb->prepare("SELECT * FROM $rel_table WHERE PersonID = %s", $person_id));
        $existing_map = [];
        foreach ($existing as $rel) {
            $existing_map[$rel->RelationshipID] = $rel;
        }
        $submitted_ids = [];
        // Process submitted relationships
        foreach ($relationships as $rel) {
            $relationship_id = isset($rel['RelationshipID']) ? sanitize_text_field($rel['RelationshipID']) : '';
            $related_person_id = isset($rel['RelatedPersonID']) ? sanitize_text_field($rel['RelatedPersonID']) : '';
            $relationship_type = isset($rel['RelationshipType']) ? sanitize_text_field($rel['RelationshipType']) : '';
            if (!$related_person_id || !$relationship_type) continue;
            $reverse_type = $this->get_reverse_relationship_type($relationship_type);
            if ($relationship_id && isset($existing_map[$relationship_id])) {
                // Update existing (do not update RelationshipID)
                $wpdb->update(
                    $rel_table,
                    [
                        'RelatedPersonID' => $related_person_id,
                        'RelationshipType' => $relationship_type
                    ],
                    ['RelationshipID' => $relationship_id]
                );
                $submitted_ids[] = $relationship_id;
                // Update or insert reverse
                $reverse = $wpdb->get_row($wpdb->prepare("SELECT * FROM $rel_table WHERE PersonID = %s AND RelatedPersonID = %s", $related_person_id, $person_id));
                if ($reverse) {
                    $wpdb->update($rel_table, [ 'RelationshipType' => $reverse_type ], [ 'RelationshipID' => $reverse->RelationshipID ]);
                } else {
                    do {
                        $unique_code = mt_rand(10000, 99999);
                        $new_id = 'REL' . $unique_code;
                        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $rel_table WHERE RelationshipID = %s", $new_id));
                    } while ($exists);
                    $wpdb->insert($rel_table, [
                        'RelationshipID' => $new_id,
                        'PersonID' => $related_person_id,
                        'RelatedPersonID' => $person_id,
                        'RelationshipType' => $reverse_type
                    ]);
                }
            } else {
                // Insert new
                do {
                    $unique_code = mt_rand(10000, 99999);
                    $new_id = 'REL' . $unique_code;
                    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $rel_table WHERE RelationshipID = %s", $new_id));
                } while ($exists);
                $wpdb->insert($rel_table, [
                    'RelationshipID' => $new_id,
                    'PersonID' => $person_id,
                    'RelatedPersonID' => $related_person_id,
                    'RelationshipType' => $relationship_type
                ]);
                $submitted_ids[] = $new_id;
                // Insert reverse
                do {
                    $unique_code = mt_rand(10000, 99999);
                    $reverse_id = 'REL' . $unique_code;
                    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $rel_table WHERE RelationshipID = %s", $reverse_id));
                } while ($exists);
                $wpdb->insert($rel_table, [
                    'RelationshipID' => $reverse_id,
                    'PersonID' => $related_person_id,
                    'RelatedPersonID' => $person_id,
                    'RelationshipType' => $reverse_type
                ]);
            }
        }
        // Delete relationships that were removed (and their reverse)
        foreach ($existing as $rel) {
            if (!in_array($rel->RelationshipID, $submitted_ids)) {
                // Delete reverse
                $wpdb->delete($rel_table, [ 'PersonID' => $rel->RelatedPersonID, 'RelatedPersonID' => $person_id ]);
                // Delete original
                $wpdb->delete($rel_table, [ 'RelationshipID' => $rel->RelationshipID ]);
            }
        }
        wp_send_json_success();
    }

    /**
     * AJAX handler to get the list of active job postings
     */
    public function ajax_get_job_postings_list() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_jobpostings';
        $show_all = isset($_POST['show_all']) && $_POST['show_all'] == '1';
        if ($show_all) {
            $results = $wpdb->get_results("SELECT JobPostingID, Title, DepartmentName, JobType, Location, SalaryRange, PostedDate, ClosingDate, Status FROM $table ORDER BY PostedDate DESC");
        } else {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT JobPostingID, Title, DepartmentName, JobType, Location, SalaryRange, PostedDate, ClosingDate, Status FROM $table WHERE Status = %s ORDER BY PostedDate DESC",
                'Active'
            ));
        }
        wp_send_json_success($results);
    }

    /**
     * AJAX handler to add a new job posting
     */
    public function ajax_add_job_posting() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_jobpostings';
        $fields = [
            'ProgramID' => isset($_POST['program_id']) && $_POST['program_id'] !== '' ? sanitize_text_field($_POST['program_id']) : null,
            'Title' => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'Description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'Requirements' => isset($_POST['requirements']) ? sanitize_textarea_field($_POST['requirements']) : '',
            'Responsibilities' => isset($_POST['responsibilities']) ? sanitize_textarea_field($_POST['responsibilities']) : '',
            'JobType' => isset($_POST['job_type']) ? sanitize_text_field($_POST['job_type']) : '',
            'Status' => isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'Draft',
            'Location' => isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '',
            'SalaryRange' => isset($_POST['salary_range']) ? sanitize_text_field($_POST['salary_range']) : '',
            'PostedDate' => current_time('mysql', 1),
            'ClosingDate' => isset($_POST['closing_date']) ? sanitize_text_field($_POST['closing_date']) : null,
            'DepartmentName' => isset($_POST['department_name']) ? sanitize_text_field($_POST['department_name']) : '',
            'ReportsTo' => isset($_POST['reports_to']) && $_POST['reports_to'] !== '' ? sanitize_text_field($_POST['reports_to']) : null,
            'CreatedBy' => get_current_user_id(),
            'LastModifiedDate' => current_time('mysql', 1),
            'IsInternal' => isset($_POST['is_internal']) ? intval($_POST['is_internal']) : 0,
        ];
        // Validate required fields
        if (empty($fields['Title']) || empty($fields['JobType']) || empty($fields['Status'])) {
            wp_send_json_error('Title, Job Type, and Status are required.');
        }
        // Get PersonID for current user
        $person = Administration_Database::get_person_by_user_id(get_current_user_id());
        $fields['CreatedBy'] = $person ? $person->PersonID : null;
        // Generate unique JobPostingID
        do {
            $unique_code = mt_rand(10000, 99999);
            $job_posting_id = 'JOBPOST' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE JobPostingID = %s", $job_posting_id));
        } while ($exists);
        $fields['JobPostingID'] = $job_posting_id;
        $result = $wpdb->insert($table, $fields);
        if ($result) {
            // Create Google Drive folder for this job posting
            if (!function_exists('create_job_posting_drive_folder')) {
                require_once dirname(__FILE__, 2) . '/ajax/ajax_careers.php';
            }
            $drive_folder_id = create_job_posting_drive_folder($fields['Title'], $job_posting_id);
            if ($drive_folder_id) {
                $wpdb->update($table, ['DriveFolderID' => $drive_folder_id], ['JobPostingID' => $job_posting_id]);
            }
            wp_send_json_success(['JobPostingID' => $job_posting_id]);
        } else {
            wp_send_json_error('Failed to add job posting.');
        }
    }

    /**
     * AJAX handler to fetch job posting details for the modal overlay
     */
    public function ajax_get_job_posting_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_jobpostings';
        $job_posting_id = isset($_POST['job_posting_id']) ? sanitize_text_field($_POST['job_posting_id']) : '';
        if (!$job_posting_id) {
            wp_send_json_error('Missing job posting ID.');
        }
        $job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE JobPostingID = %s", $job_posting_id));
        if (!$job) {
            wp_send_json_error('Job posting not found.');
        }
        // Render details in a grid similar to staff modal
        ob_start();
        echo '<div class="person-details-grid">';
        echo '<div class="person-detail-row"><span class="person-detail-label">Job Title</span><span class="person-detail-value">' . esc_html($job->Title) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Status</span><span class="person-detail-value">' . esc_html($job->Status) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Department</span><span class="person-detail-value">' . esc_html($job->DepartmentName) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Job Type</span><span class="person-detail-value">' . esc_html($job->JobType) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Location</span><span class="person-detail-value">' . esc_html($job->Location) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Salary Range</span><span class="person-detail-value">' . esc_html($job->SalaryRange) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Posted Date</span><span class="person-detail-value">' . esc_html($job->PostedDate) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Closing Date</span><span class="person-detail-value">' . esc_html($job->ClosingDate) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Program</span><span class="person-detail-value">' . esc_html($job->ProgramID) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Reports To</span><span class="person-detail-value">' . esc_html($job->ReportsTo) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Internal?</span><span class="person-detail-value">' . ($job->IsInternal ? 'Yes' : 'No') . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Description</span><span class="person-detail-value">' . esc_html($job->Description) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Requirements</span><span class="person-detail-value">' . esc_html($job->Requirements) . '</span></div>';
        echo '<div class="person-detail-row"><span class="person-detail-label">Responsibilities</span><span class="person-detail-value">' . esc_html($job->Responsibilities) . '</span></div>';
        echo '</div>';
        $html = ob_get_clean();
        wp_send_json_success($html);
    }

    /**
     * AJAX handler to fetch the full job posting view (page-like UI)
     */
    public function ajax_get_job_posting_full_view() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $job_posting_id = isset($_POST['job_posting_id']) ? sanitize_text_field($_POST['job_posting_id']) : '';
        if (!$job_posting_id) {
            wp_send_json_error('Missing job posting ID.');
        }
        $table = $wpdb->prefix . 'hr_jobpostings';
        $job = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE JobPostingID = %s", $job_posting_id));
        if (!$job) {
            wp_send_json_error('Job posting not found.');
        }
        // Fetch related program info
        $program = null;
        if ($job->ProgramID) {
            $program = $wpdb->get_row($wpdb->prepare("SELECT ProgramID, ProgramName, ProgramType FROM {$wpdb->prefix}core_programs WHERE ProgramID = %s", $job->ProgramID));
        }
        // Fetch Reports To person name
        $reports_to = null;
        if ($job->ReportsTo) {
            $person = $wpdb->get_row($wpdb->prepare("SELECT FirstName, LastName FROM {$wpdb->prefix}core_person WHERE PersonID = %s", $job->ReportsTo));
            if ($person) {
                $reports_to = $person->FirstName . ' ' . $person->LastName;
            }
        }
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/job-posting-full-view.php';
        $html = ob_get_clean();
        wp_send_json_success($html);
    }

    /**
     * AJAX handler to get all programs for select dropdowns
     */
    public function ajax_get_programs_for_select() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        $programs = $wpdb->get_results("SELECT ProgramID, ProgramName FROM $table ORDER BY ProgramName");
        wp_send_json_success($programs);
    }

    /**
     * AJAX handler to edit a job posting
     */
    public function ajax_edit_job_posting() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_jobpostings';
        $job_posting_id = isset($_POST['job_posting_id']) ? sanitize_text_field($_POST['job_posting_id']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $requirements = isset($_POST['requirements']) ? sanitize_textarea_field($_POST['requirements']) : '';
        $responsibilities = isset($_POST['responsibilities']) ? sanitize_textarea_field($_POST['responsibilities']) : '';
        $job_type = isset($_POST['job_type']) ? sanitize_text_field($_POST['job_type']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'Draft';
        $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
        $salary_range = isset($_POST['salary_range']) ? sanitize_text_field($_POST['salary_range']) : '';
        $closing_date = isset($_POST['closing_date']) ? sanitize_text_field($_POST['closing_date']) : null;
        $department_name = isset($_POST['department_name']) ? sanitize_text_field($_POST['department_name']) : '';
        $reports_to = isset($_POST['reports_to']) && $_POST['reports_to'] !== '' ? sanitize_text_field($_POST['reports_to']) : null;
        $is_internal = isset($_POST['is_internal']) ? intval($_POST['is_internal']) : 0;

        if (!$job_posting_id || !$title || !$job_type || !$status) {
            wp_send_json_error('Missing required fields.');
        }

        $result = $wpdb->update(
            $table,
            [
                'Title' => $title,
                'Description' => $description,
                'Requirements' => $requirements,
                'Responsibilities' => $responsibilities,
                'JobType' => $job_type,
                'Status' => $status,
                'Location' => $location,
                'SalaryRange' => $salary_range,
                'ClosingDate' => $closing_date,
                'DepartmentName' => $department_name,
                'ReportsTo' => $reports_to,
                'IsInternal' => $is_internal,
                'LastModifiedDate' => current_time('mysql', 1)
            ],
            ['JobPostingID' => $job_posting_id]
        );

        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update job posting.');
        }
    }

    public function ajax_delete_job_posting() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'hr_jobpostings';
        $job_posting_id = isset($_POST['job_posting_id']) ? sanitize_text_field($_POST['job_posting_id']) : '';
        if (!$job_posting_id) {
            wp_send_json_error('Missing job posting ID.');
        }
        $result = $wpdb->delete($table, array('JobPostingID' => $job_posting_id));
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete job posting.');
        }
    }

    public function ajax_get_job_applicants_list() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $job_posting_id = isset($_POST['job_posting_id']) ? sanitize_text_field($_POST['job_posting_id']) : '';
        if (!$job_posting_id) {
            wp_send_json_error('Missing job posting ID.');
        }
        $applications_table = $wpdb->prefix . 'hr_applications';
        $person_table = $wpdb->prefix . 'core_person';
        $external_table = $wpdb->prefix . 'hr_externalapplicants';
        $apps = $wpdb->get_results($wpdb->prepare("SELECT * FROM $applications_table WHERE JobPostingID = %s ORDER BY SubmissionDate DESC", $job_posting_id));
        $result = [];
        foreach ($apps as $app) {
            $is_external = $app->ExternalApplicantID && !$app->PersonID;
            if ($is_external) {
                $applicant = $wpdb->get_row($wpdb->prepare("SELECT * FROM $external_table WHERE ExternalApplicantID = %s", $app->ExternalApplicantID));
                $name = $applicant ? trim($applicant->FirstName . ' ' . $applicant->LastName) : 'External Applicant';
                $email = $applicant ? $applicant->Email : '';
                $phone = $applicant ? $applicant->Phone : '';
            } else {
                $applicant = $wpdb->get_row($wpdb->prepare("SELECT * FROM $person_table WHERE PersonID = %s", $app->PersonID));
                $name = $applicant ? trim($applicant->FirstName . ' ' . $applicant->LastName) : 'Applicant';
                $email = $applicant ? $applicant->Email : '';
                $phone = $applicant ? $applicant->Phone : '';
            }
            $result[] = [
                'ApplicationID' => $app->ApplicationID,
                'Name' => $name,
                'Email' => $email,
                'Phone' => $phone,
                'Status' => $app->Status,
                'Type' => $is_external ? 'external' : 'internal',
            ];
        }
        wp_send_json_success($result);
    }

    public function ajax_get_job_applicant_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $application_id = isset($_POST['application_id']) ? sanitize_text_field($_POST['application_id']) : '';
        if (!$application_id) {
            wp_send_json_error('Missing application ID.');
        }
        $applications_table = $wpdb->prefix . 'hr_applications';
        $person_table = $wpdb->prefix . 'core_person';
        $external_table = $wpdb->prefix . 'hr_externalapplicants';
        $app = $wpdb->get_row($wpdb->prepare("SELECT * FROM $applications_table WHERE ApplicationID = %s", $application_id));
        if (!$app) {
            wp_send_json_error('Application not found.');
        }
        $is_external = $app->ExternalApplicantID && !$app->PersonID;
        if ($is_external) {
            $applicant = $wpdb->get_row($wpdb->prepare("SELECT * FROM $external_table WHERE ExternalApplicantID = %s", $app->ExternalApplicantID));
        } else {
            $applicant = $wpdb->get_row($wpdb->prepare("SELECT * FROM $person_table WHERE PersonID = %s", $app->PersonID));
        }
        $details = [
            'ApplicationID' => $app->ApplicationID,
            'Status' => $app->Status,
            'SubmissionDate' => $app->SubmissionDate,
            'Notes' => $app->Notes,
            'ResumeURL' => $app->ResumeURL,
            'CoverLetterURL' => $app->CoverLetterURL,
            'Applicant' => $applicant,
            'Type' => $is_external ? 'external' : 'internal',
        ];
        wp_send_json_success($details);
    }

    public function ajax_update_job_applicant_status() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $application_id = isset($_POST['application_id']) ? sanitize_text_field($_POST['application_id']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $allowed = ['New', 'Interview(s) Scheduled', 'Pending Decision', 'Decision Made'];
        if (!$application_id || !in_array($status, $allowed)) {
            wp_send_json_error('Invalid application ID or status.');
        }
        $applications_table = $wpdb->prefix . 'hr_applications';
        $result = $wpdb->update($applications_table, ['Status' => $status], ['ApplicationID' => $application_id]);
        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update status.');
        }
    }

    public function ajax_update_job_applicant_notes() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $application_id = isset($_POST['application_id']) ? sanitize_text_field($_POST['application_id']) : '';
        $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
        if (!$application_id) {
            wp_send_json_error('Invalid application ID.');
        }
        $applications_table = $wpdb->prefix . 'hr_applications';
        $result = $wpdb->update($applications_table, ['Notes' => $notes], ['ApplicationID' => $application_id]);
        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update notes.');
        }
    }

    public function ajax_get_course_assignments() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        if (!$course_id) wp_send_json_error('Missing course ID.');
        $table = $wpdb->prefix . 'progtype_edu_assignments';
        $assignments = $wpdb->get_results($wpdb->prepare("SELECT AssignmentID, Title, DueDate, MaxScore FROM $table WHERE CourseID = %s ORDER BY DueDate ASC, Title ASC", $course_id));
        wp_send_json_success($assignments);
    }

    public function ajax_get_assignment_details() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $assignment_id = isset($_POST['assignment_id']) ? sanitize_text_field($_POST['assignment_id']) : '';
        if (!$assignment_id) wp_send_json_error('Missing assignment ID.');
        $table = $wpdb->prefix . 'progtype_edu_assignments';
        $assignment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE AssignmentID = %s", $assignment_id));
        if (!$assignment) wp_send_json_error('Assignment not found.');
        wp_send_json_success($assignment);
    }

    public function ajax_add_assignment() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $due_date = isset($_POST['due_date']) ? sanitize_text_field($_POST['due_date']) : null;
        $max_score = isset($_POST['max_score']) ? floatval($_POST['max_score']) : null;
        if (!$course_id || !$title) wp_send_json_error('Course and title required.');
        $table = $wpdb->prefix . 'progtype_edu_assignments';
        // Generate unique AssignmentID
        do {
            $unique_code = mt_rand(10000, 99999);
            $assignment_id = 'ASSIGN' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE AssignmentID = %s", $assignment_id));
        } while ($exists);
        $result = $wpdb->insert($table, [
            'AssignmentID' => $assignment_id,
            'CourseID' => $course_id,
            'Title' => $title,
            'Description' => $description,
            'DueDate' => $due_date,
            'MaxScore' => $max_score
        ]);
        if ($result) {
            wp_send_json_success(['AssignmentID' => $assignment_id]);
        } else {
            wp_send_json_error('Failed to add assignment.');
        }
    }

    public function ajax_edit_assignment() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $assignment_id = isset($_POST['assignment_id']) ? sanitize_text_field($_POST['assignment_id']) : '';
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $due_date = isset($_POST['due_date']) ? sanitize_text_field($_POST['due_date']) : null;
        $max_score = isset($_POST['max_score']) ? floatval($_POST['max_score']) : null;
        if (!$assignment_id || !$title) wp_send_json_error('Assignment and title required.');
        $table = $wpdb->prefix . 'progtype_edu_assignments';
        $result = $wpdb->update($table, [
            'Title' => $title,
            'Description' => $description,
            'DueDate' => $due_date,
            'MaxScore' => $max_score
        ], ['AssignmentID' => $assignment_id]);
        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update assignment.');
        }
    }

    public function ajax_delete_assignment() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        global $wpdb;
        $assignment_id = isset($_POST['assignment_id']) ? sanitize_text_field($_POST['assignment_id']) : '';
        if (!$assignment_id) wp_send_json_error('Missing assignment ID.');
        $table = $wpdb->prefix . 'progtype_edu_assignments';
        $result = $wpdb->delete($table, ['AssignmentID' => $assignment_id]);
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete assignment.');
        }
    }

    public function render_careers_job_list($atts = array()) {
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/careers-list.php';
        return ob_get_clean();
    }

    public function render_member_profile($atts = array()) {
        if (!is_user_logged_in()) {
            return '<p>' . __('You must be logged in to view your member profile.', 'administration-plugin') . '</p>';
        }
        // Enqueue member profile assets
        wp_enqueue_style('administration-plugin-member-profile', ADMINISTRATION_PLUGIN_URL . 'assets/css/public/member-profile.css', array(), ADMINISTRATION_PLUGIN_VERSION, 'all');
        wp_enqueue_script('administration-plugin-member-profile', ADMINISTRATION_PLUGIN_URL . 'assets/js/public/member-profile.js', array('jquery'), ADMINISTRATION_PLUGIN_VERSION, true);
        ob_start();
        include ADMINISTRATION_PLUGIN_PATH . 'templates/public/member-profile.php';
        return ob_get_clean();
    }
} 