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
        add_action('wp_ajax_add_edu_enrollment', array($this, 'ajax_add_edu_enrollment'));
        add_action('wp_ajax_get_course_detail_and_enrollments', array($this, 'ajax_get_course_detail_and_enrollments'));
        add_action('wp_ajax_get_course_detail_tabs', array($this, 'ajax_get_course_detail_tabs'));
        add_action('wp_ajax_add_course_enrollment', array($this, 'ajax_add_course_enrollment'));
        add_action('wp_ajax_get_people_enrolled_in_program', array($this, 'ajax_get_people_enrolled_in_program'));
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
        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : 'main';
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
        if (!$first_name || !$last_name || !$email) {
            wp_send_json_error('All fields are required.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE Email = %s", $email));
        if ($exists) {
            wp_send_json_error('A person with this email already exists.');
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
        $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!$person_id || !$first_name || !$last_name || !$email) {
            wp_send_json_error('All fields are required.');
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
            'Email' => $email
        ];
        $result = Administration_Database::save_person($person_data);
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
     * AJAX handler to add an enrollment to an education program
     */
    public function ajax_add_edu_enrollment() {
        check_ajax_referer('administration_plugin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied.');
        }
        global $wpdb;
        $table = $wpdb->prefix . 'progtype_edu_enrollment';
        $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
        $person_id = isset($_POST['PersonID']) ? sanitize_text_field($_POST['PersonID']) : '';
        $course_id = isset($_POST['CourseID']) ? sanitize_text_field($_POST['CourseID']) : null;
        if (!$program_id || !$person_id) {
            wp_send_json_error('Missing required fields.');
        }
        // Generate unique ProgramEnrollmentID
        do {
            $unique_code = mt_rand(10000, 99999);
            $enroll_id = 'ENROLL' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ProgramEnrollmentID = %s", $enroll_id));
        } while ($exists);
        $insert_data = array(
            'ProgramEnrollmentID' => $enroll_id,
            'PersonID' => $person_id,
            'ProgramID' => $program_id,
            'ActiveFlag' => 1,
            'EnrollmentDate' => current_time('mysql', 1)
        );
        if ($course_id) {
            $insert_data['CourseID'] = $course_id;
        }
        $result = $wpdb->insert($table, $insert_data);
        if ($result) {
            wp_send_json_success(['ProgramEnrollmentID' => $enroll_id]);
        } else {
            wp_send_json_error('Failed to add enrollment.');
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
        $table = $wpdb->prefix . 'progtype_edu_courseenrollments';
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        $person_id = isset($_POST['PersonID']) ? sanitize_text_field($_POST['PersonID']) : '';
        if (!$course_id || !$person_id) {
            wp_send_json_error('Missing required fields.');
        }
        // Generate unique CourseEnrollmentID
        do {
            $unique_code = mt_rand(10000, 99999);
            $enroll_id = 'COURSEENROLL' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE CourseEnrollmentID = %s", $enroll_id));
        } while ($exists);
        $result = $wpdb->insert($table, array(
            'CourseEnrollmentID' => $enroll_id,
            'CourseID' => $course_id,
            'PersonID' => $person_id,
            'ActiveFlag' => 1,
            'EnrollmentDate' => current_time('mysql', 1)
        ));
        if ($result) {
            wp_send_json_success(['CourseEnrollmentID' => $enroll_id]);
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
} 