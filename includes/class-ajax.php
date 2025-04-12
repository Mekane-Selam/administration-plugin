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

        $id = intval($_POST['id']);
        if (!$id) {
            wp_send_json_error('Invalid job posting ID');
            return;
        }

        $status = sanitize_text_field($_POST['status']);
        $valid_statuses = array('draft', 'published', 'closed', 'archived');
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
                'ModifiedDate' => current_time('mysql'),
                'ModifiedBy' => get_current_user_id()
            ),
            array('ID' => $id),
            array('%s', '%s', '%d'),
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