<?php
// AJAX handlers for education grades functionality
add_action('wp_ajax_get_course_assignments', 'ajax_get_course_assignments');
add_action('wp_ajax_get_assignment_grades', 'ajax_get_assignment_grades');
add_action('wp_ajax_add_grade', 'ajax_add_grade');
add_action('wp_ajax_edit_grade', 'ajax_edit_grade');
add_action('wp_ajax_delete_grade', 'ajax_delete_grade');
add_action('wp_ajax_get_course_students', 'ajax_get_course_students');

function ajax_get_course_assignments() {
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    if (!$course_id) wp_send_json_error('Missing course_id');
    $table = $wpdb->prefix . 'progtype_edu_assignments';
    $results = $wpdb->get_results($wpdb->prepare("SELECT AssignmentID, Title, DueDate, MaxScore FROM $table WHERE CourseID = %s ORDER BY DueDate ASC", $course_id));
    wp_send_json_success($results);
}

function ajax_get_assignment_grades() {
    global $wpdb;
    $assignment_id = isset($_POST['assignment_id']) ? sanitize_text_field($_POST['assignment_id']) : '';
    if (!$assignment_id) wp_send_json_error('Missing assignment_id');
    $grades_table = $wpdb->prefix . 'progtype_edu_assignmentgrades';
    $person_table = $wpdb->prefix . 'core_person';
    $results = $wpdb->get_results($wpdb->prepare("SELECT g.GradeID, g.PersonID, g.Score, g.Feedback, g.GradedDate, p.FirstName, p.LastName FROM $grades_table g LEFT JOIN $person_table p ON g.PersonID = p.PersonID WHERE g.AssignmentID = %s ORDER BY p.LastName, p.FirstName", $assignment_id));
    wp_send_json_success($results);
}

function ajax_add_grade() {
    global $wpdb;
    $assignment_id = isset($_POST['assignment_id']) ? sanitize_text_field($_POST['assignment_id']) : '';
    $person_id = isset($_POST['person_id']) ? sanitize_text_field($_POST['person_id']) : '';
    $score = isset($_POST['score']) ? floatval($_POST['score']) : null;
    $feedback = isset($_POST['feedback']) ? sanitize_textarea_field($_POST['feedback']) : '';
    if (!$assignment_id || !$person_id || $score === null) wp_send_json_error('Missing required fields');
    // Generate unique GradeID
    do {
        $unique_code = mt_rand(10000, 99999);
        $grade_id = 'GRADE' . $unique_code;
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}progtype_edu_assignmentgrades WHERE GradeID = %s", $grade_id));
    } while ($exists);
    $result = $wpdb->insert(
        $wpdb->prefix . 'progtype_edu_assignmentgrades',
        array(
            'GradeID' => $grade_id,
            'AssignmentID' => $assignment_id,
            'PersonID' => $person_id,
            'Score' => $score,
            'Feedback' => $feedback,
            'GradedDate' => current_time('mysql'),
        ),
        array('%s','%s','%s','%f','%s','%s')
    );
    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to add grade.');
    }
}

function ajax_edit_grade() {
    global $wpdb;
    $grade_id = isset($_POST['grade_id']) ? sanitize_text_field($_POST['grade_id']) : '';
    $score = isset($_POST['score']) ? floatval($_POST['score']) : null;
    $feedback = isset($_POST['feedback']) ? sanitize_textarea_field($_POST['feedback']) : '';
    if (!$grade_id || $score === null) wp_send_json_error('Missing required fields');
    $result = $wpdb->update(
        $wpdb->prefix . 'progtype_edu_assignmentgrades',
        array('Score' => $score, 'Feedback' => $feedback, 'GradedDate' => current_time('mysql')),
        array('GradeID' => $grade_id),
        array('%f','%s','%s'),
        array('%s')
    );
    if ($result !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to update grade.');
    }
}

function ajax_delete_grade() {
    global $wpdb;
    $grade_id = isset($_POST['grade_id']) ? sanitize_text_field($_POST['grade_id']) : '';
    if (!$grade_id) wp_send_json_error('Missing grade_id');
    $result = $wpdb->delete($wpdb->prefix . 'progtype_edu_assignmentgrades', array('GradeID' => $grade_id), array('%s'));
    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to delete grade.');
    }
}

function ajax_get_course_students() {
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    if (!$course_id) wp_send_json_error('Missing course_id');
    $enrollments_table = $wpdb->prefix . 'progtype_edu_courseenrollments';
    $person_table = $wpdb->prefix . 'core_person';
    $results = $wpdb->get_results($wpdb->prepare("SELECT p.PersonID, p.FirstName, p.LastName FROM $enrollments_table e LEFT JOIN $person_table p ON e.PersonID = p.PersonID WHERE e.CourseID = %s AND e.ActiveFlag = 1 ORDER BY p.LastName, p.FirstName", $course_id));
    wp_send_json_success($results);
} 