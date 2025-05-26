<?php
// AJAX handlers for course attendance
add_action('wp_ajax_get_course_attendance', 'ajax_get_course_attendance');
add_action('wp_ajax_nopriv_get_course_attendance', 'ajax_get_course_attendance');
add_action('wp_ajax_save_course_attendance', 'ajax_save_course_attendance');
add_action('wp_ajax_nopriv_save_course_attendance', 'ajax_save_course_attendance');

function ajax_get_course_attendance() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    $session_date = isset($_POST['session_date']) ? sanitize_text_field($_POST['session_date']) : '';
    if (!$course_id || !$session_date) {
        wp_send_json_error('Missing course_id or session_date');
    }
    $enrollments_table = $wpdb->prefix . 'progtype_edu_courseenrollments';
    $person_table = $wpdb->prefix . 'core_person';
    $attendance_table = $wpdb->prefix . 'progtype_edu_courseattendance';
    // Get enrolled students
    $students = $wpdb->get_results($wpdb->prepare(
        "SELECT p.PersonID, p.FirstName, p.LastName FROM $enrollments_table e
         JOIN $person_table p ON e.PersonID = p.PersonID
         WHERE e.CourseID = %s AND e.ActiveFlag = 1",
        $course_id
    ));
    // Get attendance for the date
    $attendance = $wpdb->get_col($wpdb->prepare(
        "SELECT PersonID FROM $attendance_table WHERE CourseID = %s AND SessionDate = %s",
        $course_id, $session_date
    ));
    wp_send_json_success([ 'students' => $students, 'attendance' => $attendance ]);
}

function ajax_save_course_attendance() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    $session_date = isset($_POST['session_date']) ? sanitize_text_field($_POST['session_date']) : '';
    $present_ids = isset($_POST['present_person_ids']) && is_array($_POST['present_person_ids']) ? array_map('sanitize_text_field', $_POST['present_person_ids']) : [];
    if (!$course_id || !$session_date) {
        wp_send_json_error('Missing course_id or session_date');
    }
    $attendance_table = $wpdb->prefix . 'progtype_edu_courseattendance';
    // Get all enrolled students for this course
    $enrollments_table = $wpdb->prefix . 'progtype_edu_courseenrollments';
    $all_students = $wpdb->get_col($wpdb->prepare(
        "SELECT PersonID FROM $enrollments_table WHERE CourseID = %s AND ActiveFlag = 1",
        $course_id
    ));
    // Remove all attendance records for this course/date for enrolled students
    if (!empty($all_students)) {
        $in = implode(',', array_fill(0, count($all_students), '%s'));
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $attendance_table WHERE CourseID = %s AND SessionDate = %s AND PersonID IN ($in)",
            array_merge([$course_id, $session_date], $all_students)
        ));
    }
    // Insert new attendance records for checked students
    $now = current_time('mysql');
    foreach ($present_ids as $person_id) {
        $wpdb->insert($attendance_table, [
            'PersonID' => $person_id,
            'CourseID' => $course_id,
            'SessionDate' => $session_date,
            'DateTimeStamp' => $now
        ]);
    }
    wp_send_json_success('Attendance saved.');
} 