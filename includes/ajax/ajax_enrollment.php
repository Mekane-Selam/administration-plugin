<?php
// AJAX handlers for education program enrollments
add_action('wp_ajax_add_edu_enrollment', 'administration_plugin_ajax_add_edu_enrollment');
add_action('wp_ajax_remove_edu_enrollments', 'administration_plugin_ajax_remove_edu_enrollments');

function administration_plugin_ajax_add_edu_enrollment() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
    }
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_enrollment';
    $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
    $person_ids = array();
    if (isset($_POST['PersonIDs'])) {
        $person_ids = is_array($_POST['PersonIDs']) ? $_POST['PersonIDs'] : array($_POST['PersonIDs']);
    } elseif (isset($_POST['PersonID'])) {
        $person_ids = array($_POST['PersonID']);
    }
    $course_id = isset($_POST['CourseID']) ? sanitize_text_field($_POST['CourseID']) : null;
    if (!$program_id || empty($person_ids)) {
        wp_send_json_error('Missing required fields.');
    }
    $success = 0;
    $already_enrolled = 0;
    $errors = 0;
    foreach ($person_ids as $person_id) {
        $person_id = sanitize_text_field($person_id);
        // Check if person is already actively enrolled in the program
        $existing_enrollment = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE ProgramID = %s AND PersonID = %s AND ActiveFlag = 1",
            $program_id,
            $person_id
        ));
        if ($existing_enrollment) {
            $already_enrolled++;
            continue;
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
            $success++;
        } else {
            $errors++;
        }
    }
    $summary = "$success enrolled, $already_enrolled already enrolled, $errors errors.";
    if ($success > 0) {
        wp_send_json_success(['summary' => $summary]);
    } else {
        wp_send_json_error($summary);
    }
}

function administration_plugin_ajax_remove_edu_enrollments() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
    }
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_enrollment';
    $program_id = isset($_POST['program_id']) ? sanitize_text_field($_POST['program_id']) : '';
    $person_ids = array();
    if (isset($_POST['PersonIDs'])) {
        $person_ids = is_array($_POST['PersonIDs']) ? $_POST['PersonIDs'] : array($_POST['PersonIDs']);
    }
    if (!$program_id || empty($person_ids)) {
        wp_send_json_error('Missing required fields.');
    }
    $removed = 0;
    $not_found = 0;
    foreach ($person_ids as $person_id) {
        $person_id = sanitize_text_field($person_id);
        $result = $wpdb->update(
            $table,
            array('ActiveFlag' => 0),
            array('ProgramID' => $program_id, 'PersonID' => $person_id, 'ActiveFlag' => 1)
        );
        if ($result) {
            $removed++;
        } else {
            $not_found++;
        }
    }
    $summary = "$removed removed, $not_found not found or already inactive.";
    if ($removed > 0) {
        wp_send_json_success(['summary' => $summary]);
    } else {
        wp_send_json_error($summary);
    }
} 