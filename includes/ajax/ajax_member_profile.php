<?php
// AJAX handlers for member profile UI
add_action('wp_ajax_get_member_personal_info', 'ajax_get_member_personal_info');
add_action('wp_ajax_get_member_family_info', 'ajax_get_member_family_info');
add_action('wp_ajax_get_member_roles_info', 'ajax_get_member_roles_info');
add_action('wp_ajax_update_member_personal_info', 'ajax_update_member_personal_info');
add_action('wp_ajax_upload_member_avatar', 'ajax_upload_member_avatar');

function ajax_get_member_personal_info() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    if (!$person) wp_send_json_error('No person record.');
    $data = [
        'FirstName' => esc_html($person->FirstName),
        'LastName' => esc_html($person->LastName),
        'Email' => esc_html($person->Email),
        'Phone' => esc_html($person->Phone),
        'Birthday' => esc_html($person->Birthday),
        'Gender' => esc_html($person->Gender),
        'AddressLine1' => esc_html($person->AddressLine1),
        'AddressLine2' => esc_html($person->AddressLine2),
        'City' => esc_html($person->City),
        'State' => esc_html($person->State),
        'Zip' => esc_html($person->Zip),
    ];
    wp_send_json_success($data);
}

function ajax_get_member_family_info() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    if (!$person) wp_send_json_error('No person record.');
    $rel_table = $wpdb->prefix . 'core_person_relationships';
    $person_table = $wpdb->prefix . 'core_person';
    $rels = $wpdb->get_results($wpdb->prepare("SELECT * FROM $rel_table WHERE PersonID = %s", $person->PersonID));
    $family = [];
    foreach ($rels as $rel) {
        $related = $wpdb->get_row($wpdb->prepare("SELECT FirstName, LastName FROM $person_table WHERE PersonID = %s", $rel->RelatedPersonID));
        if ($related) {
            $family[] = [
                'Type' => esc_html($rel->RelationshipType),
                'Name' => esc_html($related->FirstName . ' ' . $related->LastName)
            ];
        }
    }
    wp_send_json_success($family);
}

function ajax_get_member_roles_info() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    if (!$person) wp_send_json_error('No person record.');
    $roles = [];
    // HR Staff roles
    $hr_staff_table = $wpdb->prefix . 'hr_staff';
    $hr_roles_table = $wpdb->prefix . 'hr_roles';
    $programs_table = $wpdb->prefix . 'core_programs';
    $hr_roles = $wpdb->get_results($wpdb->prepare(
        "SELECT s.StaffRolesID, r.RoleTitle, p.ProgramName FROM $hr_staff_table s
        LEFT JOIN $hr_roles_table r ON s.StaffRolesID = r.StaffRoleID
        LEFT JOIN $programs_table p ON s.ProgramID = p.ProgramID
        WHERE s.PersonID = %s",
        $person->PersonID
    ));
    foreach ($hr_roles as $role) {
        $roles[] = [
            'RoleTitle' => esc_html($role->RoleTitle),
            'ProgramName' => esc_html($role->ProgramName)
        ];
    }
    wp_send_json_success($roles);
}

function ajax_update_member_personal_info() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    if (!$person) wp_send_json_error('No person record.');
    $fields = [];
    $allowed = ['FirstName','LastName','Email','Phone','Birthday','Gender','AddressLine1','AddressLine2','City','State','Zip'];
    foreach ($allowed as $f) {
        if (isset($_POST[$f])) $fields[$f] = sanitize_text_field($_POST[$f]);
    }
    if (empty($fields)) wp_send_json_error('No fields to update.');
    $table = $wpdb->prefix . 'core_person';
    $result = $wpdb->update($table, $fields, ['PersonID' => $person->PersonID]);
    if ($result !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to update info.');
    }
}

function ajax_upload_member_avatar() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    if (!$person) wp_send_json_error('No person record.');
    if (!isset($_FILES['avatar']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])) {
        wp_send_json_error('No file uploaded.');
    }
    $file = $_FILES['avatar'];
    $allowed = ['image/jpeg','image/png','image/gif'];
    if (!in_array($file['type'], $allowed)) wp_send_json_error('Invalid file type.');
    $upload = wp_handle_upload($file, ['test_form' => false]);
    if (isset($upload['error'])) wp_send_json_error($upload['error']);
    $avatar_url = $upload['url'];
    $table = $wpdb->prefix . 'core_person';
    $wpdb->update($table, ['AvatarURL' => $avatar_url], ['PersonID' => $person->PersonID]);
    wp_send_json_success(['avatar_url' => esc_url($avatar_url)]);
} 