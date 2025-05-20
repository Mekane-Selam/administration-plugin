<?php
/**
 * Handles syncing users (members) with the core_person table.
 */
class Administration_Sync_Members {
    public function __construct() {
        error_log('Administration_Sync_Members __construct called');
    }
    public function init() {
        // Hook into Ultimate Member user registration
        add_action('um_registration_complete', [$this, 'sync_new_user'], 10, 2);
        error_log('Hook um_registration_complete added');
        // Hook into Ultimate Member profile update
        add_action('um_user_profile_updated', [$this, 'sync_user_update'], 10, 1);
        error_log('Hook um_user_profile_updated added');
        // Sync existing users
        add_action('admin_init', [$this, 'maybe_sync_existing_users']);
        error_log('Hook admin_init added');
        error_log('Administration Sync Members initialized');
    }

    public function sync_new_user($user_id, $args) {
        error_log("New user registration sync triggered for user ID: $user_id");
        $this->sync_user($user_id);
    }

    public function sync_user_update($user_id) {
        error_log("User profile update sync triggered for user ID: $user_id");
        $this->sync_user($user_id);
    }

    public function sync_user($user_id) {
        $user = get_userdata($user_id);
        if (!$user) {
            error_log("No user found for ID: $user_id");
            return;
        }
        error_log("Syncing user: {$user->user_email}");
        $first_name = get_user_meta($user_id, 'first_name', true);
        $last_name = get_user_meta($user_id, 'last_name', true);
        error_log("Initial name data - First: $first_name, Last: $last_name");
        if (function_exists('um_user') && (!$first_name || !$last_name)) {
            um_fetch_user($user_id);
            $first_name = um_user('first_name') ?: $first_name;
            $last_name = um_user('last_name') ?: $last_name;
            error_log("UM name data - First: $first_name, Last: $last_name");
        }
        if (empty($first_name) && empty($last_name) && !empty($user->display_name)) {
            $name_parts = explode(' ', $user->display_name);
            $first_name = $name_parts[0];
            $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
            error_log("Using display name: {$user->display_name}");
        }
        if (empty($first_name)) {
            $first_name = $user->user_login;
            error_log("Using username as first name: {$user->user_login}");
        }
        $person_data = [
            'UserID' => $user_id,
            'FirstName' => $first_name,
            'LastName' => $last_name ?: '',
            'Email' => $user->user_email,
        ];
        error_log("Saving person data: " . print_r($person_data, true));
        // Use new database helper if available
        if (class_exists('Administration_Database_Members')) {
            $person = Administration_Database_Members::get_person_by_user_id($user_id);
        } else {
            $person = Administration_Database::get_person_by_user_id($user_id);
        }
        if ($person) {
            $person_data['PersonID'] = $person->PersonID;
            error_log("Updating existing person record ID: {$person->PersonID}");
        } else {
            error_log("Creating new person record");
        }
        if (class_exists('Administration_Database_Members')) {
            $result = Administration_Database_Members::save_person($person_data);
        } else {
            $result = Administration_Database::save_person($person_data);
        }
        error_log("Save result: " . ($result ? "Success (ID: $result)" : "Failed"));
    }

    public function maybe_sync_existing_users() {
        error_log('Starting sync check...');
        $users = get_users([
            'fields' => ['ID', 'user_email', 'display_name']
        ]);
        error_log('Found ' . count($users) . ' total users');
        if (count($users) === 0) {
            error_log('No users found to sync');
            return;
        }
        if (get_option('administration_synced_existing_users') && !get_option('administration_force_sync_needed', false)) {
            error_log('Users already synced, skipping sync');
            return;
        }
        foreach ($users as $user) {
            error_log("Processing user ID: {$user->ID}, Email: {$user->user_email}");
            $this->sync_user($user->ID);
        }
        update_option('administration_synced_existing_users', true);
        delete_option('administration_force_sync_needed');
        error_log('Completed sync of existing users');
    }

    public function force_sync_all_users() {
        error_log('Force sync initiated');
        delete_option('administration_synced_existing_users');
        update_option('administration_force_sync_needed', true);
        $this->maybe_sync_existing_users();
    }

    public function get_sync_status() {
        global $wpdb;
        $total_users = count(get_users(['fields' => ['ID']]));
        $total_synced = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}core_person WHERE UserID IS NOT NULL");
        return [
            'total_users' => $total_users,
            'total_synced' => $total_synced,
            'is_synced' => get_option('administration_synced_existing_users', false),
            'force_sync_needed' => get_option('administration_force_sync_needed', false)
        ];
    }
} 