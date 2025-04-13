<?php
/**
 * Handle AJAX requests for the administration plugin
 */
class Administration_Ajax {
    private $db;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;

        // Register AJAX actions for both logged in and non-logged in users
        add_action('wp_ajax_save_job_posting', array($this, 'save_job_posting'));
        add_action('wp_ajax_get_job_posting', array($this, 'get_job_posting'));
        add_action('wp_ajax_get_job_postings', array($this, 'get_job_postings'));
        add_action('wp_ajax_update_job_posting', array($this, 'update_job_posting'));
        add_action('wp_ajax_update_job_status', array($this, 'update_job_status'));
        
        // Add missing HR endpoints
        add_action('wp_ajax_get_applications', array($this, 'get_applications'));
        add_action('wp_ajax_get_interviews', array($this, 'get_interviews'));
        add_action('wp_ajax_get_offers', array($this, 'get_offers'));
        
        // Add public endpoints for job applications
        add_action('wp_ajax_submit_job_application', array($this, 'submit_job_application'));
        add_action('wp_ajax_nopriv_submit_job_application', array($this, 'submit_job_application'));
    }

    /**
     * Save a new job posting
     */
    public function save_job_posting() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $title = sanitize_text_field($_POST['title']);
        $description = wp_kses_post($_POST['description']);
        $requirements = wp_kses_post($_POST['requirements']);
        $department = sanitize_text_field($_POST['departmentName']);
        $location = sanitize_text_field($_POST['location']);
        $type = sanitize_text_field($_POST['jobType']);
        $status = 'Draft';

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_jobpostings';

        $result = $wpdb->insert(
            $table_name,
            array(
                'Title' => $title,
                'Description' => $description,
                'Requirements' => $requirements,
                'DepartmentName' => $department,
                'Location' => $location,
                'JobType' => $type,
                'Status' => $status,
                'PostedDate' => current_time('mysql'),
                'CreatedBy' => get_current_user_id()
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );

        if ($result === false) {
            wp_send_json_error('Failed to save job posting');
            return;
        }

        wp_send_json_success(array(
            'message' => 'Job posting saved successfully',
            'id' => $wpdb->insert_id
        ));
    }

    /**
     * Get a specific job posting
     */
    public function get_job_posting() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('read')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $id = intval($_GET['id']);
        if (!$id) {
            wp_send_json_error('Invalid job posting ID');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_jobpostings';
        
        $job = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE JobPostingID = %d",
                $id
            )
        );

        if (!$job) {
            wp_send_json_error('Job posting not found');
            return;
        }

        wp_send_json_success($job);
    }

    /**
     * Get all job postings
     */
    public function get_job_postings() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('read')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_jobpostings';
        
        $jobs = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY PostedDate DESC"
        );

        wp_send_json_success($jobs);
    }

    /**
     * Update an existing job posting
     */
    public function update_job_posting() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $id = intval($_POST['id']);
        if (!$id) {
            wp_send_json_error('Invalid job posting ID');
            return;
        }

        $title = sanitize_text_field($_POST['title']);
        $description = wp_kses_post($_POST['description']);
        $requirements = wp_kses_post($_POST['requirements']);
        $responsibilities = wp_kses_post($_POST['responsibilities']);
        $department = sanitize_text_field($_POST['departmentName']);
        $location = sanitize_text_field($_POST['location']);
        $type = sanitize_text_field($_POST['jobType']);
        $salary_range = sanitize_text_field($_POST['salaryRange']);
        $posted_date = sanitize_text_field($_POST['postedDate']);
        $closing_date = sanitize_text_field($_POST['closingDate']);
        $is_internal = isset($_POST['isInternal']) ? 1 : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_jobpostings';

        $result = $wpdb->update(
            $table_name,
            array(
                'Title' => $title,
                'Description' => $description,
                'Requirements' => $requirements,
                'Responsibilities' => $responsibilities,
                'DepartmentName' => $department,
                'Location' => $location,
                'JobType' => $type,
                'SalaryRange' => $salary_range,
                'PostedDate' => $posted_date ? $posted_date : current_time('mysql'),
                'ClosingDate' => $closing_date,
                'IsInternal' => $is_internal,
                'LastModifiedDate' => current_time('mysql')
            ),
            array('JobPostingID' => $id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error('Failed to update job posting');
            return;
        }

        wp_send_json_success(array(
            'message' => 'Job posting updated successfully'
        ));
    }

    /**
     * Update job posting status
     */
    public function update_job_status() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        $id = intval($_POST['job_id']);
        if (!$id) {
            wp_send_json_error('Invalid job posting ID');
            return;
        }

        $status = sanitize_text_field($_POST['status']);
        $valid_statuses = array('Open', 'Closed', 'On Hold', 'Draft');
        if (!in_array($status, $valid_statuses)) {
            wp_send_json_error('Invalid status');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_jobpostings';

        $result = $wpdb->update(
            $table_name,
            array(
                'Status' => $status,
                'LastModifiedDate' => current_time('mysql')
            ),
            array('JobPostingID' => $id),
            array('%s', '%s'),
            array('%d')
        );

        if ($result === false) {
            wp_send_json_error('Failed to update job status');
            return;
        }

        wp_send_json_success(array(
            'message' => 'Job status updated successfully'
        ));
    }

    /**
     * Get all applications
     */
    public function get_applications() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('read')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_applications';
        
        $applications = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY SubmissionDate DESC"
        );

        wp_send_json_success($applications);
    }

    /**
     * Get all interviews
     */
    public function get_interviews() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('read')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_interviewschedules';
        
        $interviews = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY ScheduledDateTime DESC"
        );

        wp_send_json_success($interviews);
    }

    /**
     * Get all offers
     */
    public function get_offers() {
        check_ajax_referer('administration_nonce', 'nonce');

        if (!current_user_can('read')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'hr_offers';
        
        $offers = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY OfferDate DESC"
        );

        wp_send_json_success($offers);
    }

    /**
     * Handle job application submission
     */
    public function submit_job_application() {
        // Verify nonce
        if (!check_ajax_referer('submit_job_application', 'job_application_nonce', false)) {
            wp_send_json_error('Invalid nonce');
            return;
        }

        // Create uploads directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $job_applications_dir = $upload_dir['basedir'] . '/job-applications';
        
        if (!file_exists($job_applications_dir)) {
            wp_mkdir_p($job_applications_dir);
            // Create an index.php file to prevent directory listing
            file_put_contents($job_applications_dir . '/index.php', '<?php // Silence is golden');
            // Set directory permissions
            chmod($job_applications_dir, 0755);
        }

        // Get form data
        $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $cover_letter = sanitize_textarea_field($_POST['cover_letter']);
        $notes = sanitize_textarea_field($_POST['notes']);

        // Validate required fields
        if (!$job_id || !$first_name || !$last_name || !$email || !isset($_FILES['resume'])) {
            wp_send_json_error('Missing required fields');
            return;
        }

        // Handle resume upload
        $resume_file = $_FILES['resume'];
        $resume_name = sanitize_file_name($resume_file['name']);
        $resume_path = $job_applications_dir . '/' . time() . '_' . $resume_name;

        // Check file type
        $allowed_types = array('application/pdf');
        if (!in_array($resume_file['type'], $allowed_types)) {
            wp_send_json_error('Invalid file type. Only PDF files are allowed.');
            return;
        }

        // Move uploaded file
        if (!move_uploaded_file($resume_file['tmp_name'], $resume_path)) {
            wp_send_json_error('Error uploading resume');
            return;
        }

        // Check if person exists in core_person table
        $person = $this->db->get_row($this->db->prepare(
            "SELECT PersonID FROM {$this->db->prefix}core_person WHERE Email = %s",
            $email
        ));

        $person_id = null;
        if ($person) {
            $person_id = $person->PersonID;
        }

        // Create external applicant record if person doesn't exist
        $external_applicant_id = null;
        if (!$person_id) {
            $this->db->insert(
                $this->db->prefix . 'hr_externalapplicants',
                array(
                    'FirstName' => $first_name,
                    'LastName' => $last_name,
                    'Email' => $email,
                    'Phone' => $phone
                ),
                array('%s', '%s', '%s', '%s')
            );
            $external_applicant_id = $this->db->insert_id;
        }

        // Insert application
        $result = $this->db->insert(
            $this->db->prefix . 'hr_applications',
            array(
                'JobPostingID' => $job_id,
                'PersonID' => $person_id,
                'ExternalApplicantID' => $external_applicant_id,
                'Status' => 'Submitted',
                'SubmissionDate' => current_time('mysql'),
                'LastModifiedDate' => current_time('mysql'),
                'Notes' => $notes,
                'ResumeURL' => str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $resume_path),
                'CoverLetterURL' => $cover_letter,
                'ReferralSource' => 'Website'
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_send_json_error('Error saving application: ' . $this->db->last_error);
            return;
        }

        wp_send_json_success(array(
            'message' => 'Application submitted successfully',
            'application_id' => $this->db->insert_id
        ));
    }

    /**
     * Helper function to get a job posting by ID
     */
    private function get_job_posting_by_id($job_id) {
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$this->db->prefix}hr_jobpostings WHERE JobID = %d",
                $job_id
            ),
            ARRAY_A
        );
    }
} 