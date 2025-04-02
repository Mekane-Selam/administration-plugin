<?php
/**
 * The REST API functionality of the plugin.
 */
class Administration_API {

    /**
     * Initialize the class.
     */
    public function init() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register the REST API routes.
     */
    public function register_routes() {
        register_rest_route('administration/v1', '/programs', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_programs'],
                'permission_callback' => [$this, 'check_permission'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'save_program'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        register_rest_route('administration/v1', '/persons', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_persons'],
                'permission_callback' => [$this, 'check_permission'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'save_person'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);

        register_rest_route('administration/v1', '/roles', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_roles'],
                'permission_callback' => [$this, 'check_permission'],
            ],
        ]);
    }

    /**
     * Check if user has permission to access the API.
     */
    public function check_permission() {
        // Get allowed roles
        $allowed_roles = get_option('administration_access_roles', ['administrator']);
        
        // Check if user is logged in and has permission
        return is_user_logged_in() && array_intersect($allowed_roles, wp_get_current_user()->roles);
    }

    /**
     * Get all programs.
     */
    public function get_programs() {
        return rest_ensure_response(Administration_Database::get_all_programs());
    }

    /**
     * Save program.
     */
    public function save_program($request) {
        $data = $request->get_params();
        $program_id = Administration_Database::save_program($data);
        return rest_ensure_response(['id' => $program_id]);
    }

    /**
     * Get all persons.
     */
    public function get_persons() {
        return rest_ensure_response(Administration_Database::get_all_persons());
    }

    /**
     * Save person.
     */
    public function save_person($request) {
        $data = $request->get_params();
        $person_id = Administration_Database::save_person($data);
        return rest_ensure_response(['id' => $person_id]);
    }

    /**
     * Get all roles.
     */
    public function get_roles() {
        return rest_ensure_response(Administration_Database::get_all_roles());
    }
}
