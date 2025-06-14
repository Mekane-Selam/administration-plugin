<?php
// AJAX handlers for permissions management
add_action('wp_ajax_search_people', 'ajax_search_people');
add_action('wp_ajax_get_person_permissions', 'ajax_get_person_permissions');
add_action('wp_ajax_add_person_role', 'ajax_add_person_role');
add_action('wp_ajax_remove_person_role', 'ajax_remove_person_role');

function ajax_search_people() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    global $wpdb;
    $q = isset($_POST['q']) ? trim($_POST['q']) : '';
    if (strlen($q) < 2) wp_send_json_success([]);
    $person_table = $wpdb->prefix . 'core_person';
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT PersonID, FirstName, LastName FROM $person_table WHERE FirstName LIKE %s OR LastName LIKE %s ORDER BY FirstName ASC, LastName ASC LIMIT 20",
        '%' . $wpdb->esc_like($q) . '%',
        '%' . $wpdb->esc_like($q) . '%'
    ));
    $people = [];
    foreach ($results as $row) {
        $people[] = [
            'PersonID' => $row->PersonID,
            'FirstName' => $row->FirstName ?? '',
            'LastName' => $row->LastName ?? ''
        ];
    }
    wp_send_json_success($people);
}

function ajax_get_person_permissions() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person_id = isset($_POST['person_id']) ? $_POST['person_id'] : '';
    if (!$person_id) wp_send_json_error('Missing person_id');
    $person = Administration_Database::get_person_by_person_id($person_id);
    if (!$person) wp_send_json_error('Person not found');
    $roles = [];
    $hr_staff_table = $wpdb->prefix . 'hr_staff';
    $hr_roles_table = $wpdb->prefix . 'hr_roles';
    $programs_table = $wpdb->prefix . 'core_programs';
    $staff_roles = $wpdb->get_results($wpdb->prepare(
        "SELECT s.StaffRolesID, r.RoleTitle, s.ProgramID, p.ProgramName FROM $hr_staff_table s
        LEFT JOIN $hr_roles_table r ON s.StaffRolesID = r.StaffRoleID
        LEFT JOIN $programs_table p ON s.ProgramID = p.ProgramID
        WHERE s.PersonID = %s",
        $person_id
    ));
    foreach ($staff_roles as $role) {
        $roles[] = [
            'RoleTitle' => $role->RoleTitle ?? '',
            'ProgramName' => $role->ProgramName ?? '',
            'StaffRolesID' => $role->StaffRolesID ?? '',
            'ProgramID' => $role->ProgramID ?? '',
            'Permissions' => [] // Placeholder for future permissions
        ];
    }
    $name = trim(($person->FirstName ?? '') . ' ' . ($person->LastName ?? ''));
    if (!$name) $name = '(No Name)';
    $data = [
        'name' => $name,
        'roles' => $roles
    ];
    wp_send_json_success($data);
}

function ajax_add_person_role() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person_id = isset($_POST['person_id']) ? $_POST['person_id'] : '';
    $role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
    $program_id = isset($_POST['program_id']) ? $_POST['program_id'] : null;
    if (!$person_id || !$role_id) wp_send_json_error('Missing person_id or role_id');
    $hr_staff_table = $wpdb->prefix . 'hr_staff';
    // Prevent duplicate
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $hr_staff_table WHERE PersonID = %s AND StaffRolesID = %s AND (ProgramID = %s OR (ProgramID IS NULL AND %s IS NULL))",
        $person_id, $role_id, $program_id, $program_id
    ));
    if ($exists) wp_send_json_error('Role already assigned');
    $wpdb->insert($hr_staff_table, [
        'PersonID' => $person_id,
        'StaffRolesID' => $role_id,
        'ProgramID' => $program_id
    ]);
    // Return updated roles
    ajax_get_person_permissions();
    wp_die();
}

function ajax_remove_person_role() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!is_user_logged_in()) wp_send_json_error('Not logged in.');
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    global $wpdb;
    $person_id = isset($_POST['person_id']) ? $_POST['person_id'] : '';
    $role_id = isset($_POST['role_id']) ? $_POST['role_id'] : '';
    $program_id = isset($_POST['program_id']) ? $_POST['program_id'] : null;
    if (!$person_id || !$role_id) wp_send_json_error('Missing person_id or role_id');
    $hr_staff_table = $wpdb->prefix . 'hr_staff';
    $where = [
        'PersonID' => $person_id,
        'StaffRolesID' => $role_id
    ];
    if ($program_id !== null && $program_id !== '') {
        $where['ProgramID'] = $program_id;
    } else {
        $where['ProgramID'] = null;
    }
    $wpdb->delete($hr_staff_table, $where);
    // Return updated roles
    ajax_get_person_permissions();
    wp_die();
} 