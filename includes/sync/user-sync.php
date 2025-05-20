<?php
// User synchronization logic for Administration Plugin
// Hooks into Ultimate Member registration to sync with core_person table

error_log('user-sync.php loaded');

add_action('um_registration_complete', 'administration_plugin_add_person_on_registration', 10, 2);

function administration_plugin_add_person_on_registration($user_id, $args) {
    error_log('um_registration_complete fired for user_id: ' . $user_id);
    global $wpdb;

    // Log all user meta for debugging
    error_log('All user meta: ' . print_r(get_user_meta($user_id), true));

    // Get user data
    $user = get_userdata($user_id);
    if (!$user) {
        error_log('No user found for user_id: ' . $user_id);
        return;
    }

    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name  = get_user_meta($user_id, 'last_name', true);
    $email      = $user->user_email;

    // Log user data
    error_log('Registering new person: ' . print_r([
        'user_id' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email
    ], true));

    // Generate unique PersonID
    $table = $wpdb->prefix . 'core_person';
    error_log('Table used: ' . $table);
    do {
        $unique_code = mt_rand(10000, 99999);
        $person_id = 'PERS' . $unique_code;
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE PersonID = %s", $person_id));
    } while ($exists);

    // Insert into core_person table
    $result = $wpdb->insert(
        $table,
        [
            'PersonID'   => $person_id,
            'FirstName'  => $first_name,
            'LastName'   => $last_name,
            'Email'      => $email,
        ],
        [
            '%s', '%s', '%s', '%s'
        ]
    );
    if ($result === false) {
        error_log('Failed to insert person: ' . $wpdb->last_error);
    } else {
        error_log('Successfully inserted person: ' . $person_id);
    }
}

// Initialize the modular sync class for user/person sync
require_once __DIR__ . '/class-administration-sync-members.php';
if (class_exists('Administration_Sync_Members')) {
    (new Administration_Sync_Members())->init();
} 