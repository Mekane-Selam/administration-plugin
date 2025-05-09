<?php
/**
 * The REST API functionality of the plugin.
 */
class Administration_Plugin_API {
    /**
     * Initialize the class
     */
    public function __construct() {
        // Add any initialization code here
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Programs endpoints
        register_rest_route('administration/v1', '/programs', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_programs'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('administration/v1', '/programs/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_program'),
            'permission_callback' => array($this, 'check_permission')
        ));

        // People endpoints
        register_rest_route('administration/v1', '/people', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_people'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('administration/v1', '/people/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_person'),
            'permission_callback' => array($this, 'check_permission')
        ));

        // Volunteer Operations endpoints
        register_rest_route('administration/v1', '/volunteers', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_volunteers'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('administration/v1', '/volunteers/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_volunteer'),
            'permission_callback' => array($this, 'check_permission')
        ));

        // HR endpoints
        register_rest_route('administration/v1', '/jobs', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_jobs'),
            'permission_callback' => array($this, 'check_permission')
        ));

        register_rest_route('administration/v1', '/jobs/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_job'),
            'permission_callback' => array($this, 'check_permission')
        ));
    }

    /**
     * Check if the user has permission to access the API
     */
    public function check_permission() {
        return current_user_can('manage_options');
    }

    /**
     * Get all programs
     */
    public function get_programs($request) {
        global $wpdb;
        $programs_table = $wpdb->prefix . 'core_programs';
        
        $programs = $wpdb->get_results("SELECT * FROM $programs_table");
        return rest_ensure_response($programs);
    }

    /**
     * Get a specific program
     */
    public function get_program($request) {
        global $wpdb;
        $programs_table = $wpdb->prefix . 'core_programs';
        
        $program = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $programs_table WHERE ProgramID = %d",
            $request['id']
        ));
        
        if (!$program) {
            return new WP_Error('not_found', 'Program not found', array('status' => 404));
        }
        
        return rest_ensure_response($program);
    }

    /**
     * Get all people
     */
    public function get_people($request) {
        global $wpdb;
        $person_table = $wpdb->prefix . 'core_person';
        
        $people = $wpdb->get_results("SELECT * FROM $person_table");
        return rest_ensure_response($people);
    }

    /**
     * Get a specific person
     */
    public function get_person($request) {
        global $wpdb;
        $person_table = $wpdb->prefix . 'core_person';
        
        $person = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $person_table WHERE PersonID = %d",
            $request['id']
        ));
        
        if (!$person) {
            return new WP_Error('not_found', 'Person not found', array('status' => 404));
        }
        
        return rest_ensure_response($person);
    }

    /**
     * Get all volunteers
     */
    public function get_volunteers($request) {
        global $wpdb;
        $volunteers_table = $wpdb->prefix . 'volunteerops_volunteers';
        
        $volunteers = $wpdb->get_results("SELECT * FROM $volunteers_table");
        return rest_ensure_response($volunteers);
    }

    /**
     * Get a specific volunteer
     */
    public function get_volunteer($request) {
        global $wpdb;
        $volunteers_table = $wpdb->prefix . 'volunteerops_volunteers';
        
        $volunteer = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $volunteers_table WHERE VolunteerID = %d",
            $request['id']
        ));
        
        if (!$volunteer) {
            return new WP_Error('not_found', 'Volunteer not found', array('status' => 404));
        }
        
        return rest_ensure_response($volunteer);
    }

    /**
     * Get all jobs
     */
    public function get_jobs($request) {
        global $wpdb;
        $job_postings_table = $wpdb->prefix . 'hr_jobpostings';
        
        $jobs = $wpdb->get_results("SELECT * FROM $job_postings_table");
        return rest_ensure_response($jobs);
    }

    /**
     * Get a specific job
     */
    public function get_job($request) {
        global $wpdb;
        $job_postings_table = $wpdb->prefix . 'hr_jobpostings';
        
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $job_postings_table WHERE JobPostingID = %d",
            $request['id']
        ));
        
        if (!$job) {
            return new WP_Error('not_found', 'Job not found', array('status' => 404));
        }
        
        return rest_ensure_response($job);
    }
} 