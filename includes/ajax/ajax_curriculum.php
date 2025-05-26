<?php
// AJAX handlers for curriculum and lesson plans
add_action('wp_ajax_get_curriculum', 'ajax_get_curriculum');
add_action('wp_ajax_add_curriculum', 'ajax_add_curriculum');
add_action('wp_ajax_edit_curriculum', 'ajax_edit_curriculum');
add_action('wp_ajax_delete_curriculum', 'ajax_delete_curriculum');
add_action('wp_ajax_get_lessonplans', 'ajax_get_lessonplans');
add_action('wp_ajax_add_lessonplan', 'ajax_add_lessonplan');
add_action('wp_ajax_edit_lessonplan', 'ajax_edit_lessonplan');
add_action('wp_ajax_delete_lessonplan', 'ajax_delete_lessonplan');

function ajax_get_curriculum() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    if (!$course_id) wp_send_json_error('Missing course_id');
    $table = $wpdb->prefix . 'progtype_edu_curriculum';
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE CourseID = %s ORDER BY WeekNumber ASC", $course_id));
    wp_send_json_success($results);
}

function ajax_add_curriculum() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    $created_by = $person && !empty($person->PersonID) ? $person->PersonID : null;
    $table = $wpdb->prefix . 'progtype_edu_curriculum';
    $data = [
        'CurriculumID' => 'CURR' . uniqid(),
        'CourseID' => sanitize_text_field($_POST['course_id']),
        'WeekNumber' => intval($_POST['week_number']),
        'Objective' => sanitize_textarea_field($_POST['objective']),
        'Materials' => sanitize_text_field($_POST['materials']),
        'VideoLinks' => sanitize_text_field($_POST['video_links']),
        'CreatedBy' => $created_by,
        'CreatedDate' => current_time('mysql'),
        'LastModifiedDate' => current_time('mysql')
    ];
    $result = $wpdb->insert($table, $data);
    if ($result) wp_send_json_success($data);
    else wp_send_json_error('Failed to add curriculum.');
}

function ajax_edit_curriculum() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_curriculum';
    $curriculum_id = sanitize_text_field($_POST['curriculum_id']);
    $data = [
        'WeekNumber' => intval($_POST['week_number']),
        'Objective' => sanitize_textarea_field($_POST['objective']),
        'Materials' => sanitize_text_field($_POST['materials']),
        'VideoLinks' => sanitize_text_field($_POST['video_links']),
        'LastModifiedDate' => current_time('mysql')
    ];
    $result = $wpdb->update($table, $data, ['CurriculumID' => $curriculum_id]);
    if ($result !== false) wp_send_json_success();
    else wp_send_json_error('Failed to update curriculum.');
}

function ajax_delete_curriculum() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_curriculum';
    $curriculum_id = sanitize_text_field($_POST['curriculum_id']);
    $result = $wpdb->delete($table, ['CurriculumID' => $curriculum_id]);
    if ($result) wp_send_json_success();
    else wp_send_json_error('Failed to delete curriculum.');
}

function ajax_get_lessonplans() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
    $week_number = isset($_POST['week_number']) ? intval($_POST['week_number']) : null;
    $table = $wpdb->prefix . 'progtype_edu_lessonplans';
    $where = 'WHERE CourseID = %s';
    $params = [$course_id];
    if ($week_number !== null) {
        $where .= ' AND WeekNumber = %d';
        $params[] = $week_number;
    }
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table $where ORDER BY Date ASC", $params));
    wp_send_json_success($results);
}

function ajax_add_lessonplan() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    require_once dirname(__DIR__) . '/database/class-administration-database.php';
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
    $created_by = $person && !empty($person->PersonID) ? $person->PersonID : null;
    // Look up CurriculumID for this course and week
    $curriculum_id = null;
    if (!empty($_POST['course_id']) && !empty($_POST['week_number'])) {
        $curriculum_table = $wpdb->prefix . 'progtype_edu_curriculum';
        $curriculum_id = $wpdb->get_var($wpdb->prepare(
            "SELECT CurriculumID FROM $curriculum_table WHERE CourseID = %s AND WeekNumber = %d",
            sanitize_text_field($_POST['course_id']),
            intval($_POST['week_number'])
        ));
    }
    $table = $wpdb->prefix . 'progtype_edu_lessonplans';
    $data = [
        'LessonPlanID' => 'LESSON' . uniqid(),
        'CurriculumID' => $curriculum_id,
        'CourseID' => sanitize_text_field($_POST['course_id']),
        'WeekNumber' => intval($_POST['week_number']),
        'Date' => sanitize_text_field($_POST['date']),
        'Title' => sanitize_text_field($_POST['title']),
        'Description' => sanitize_textarea_field($_POST['description']),
        'Materials' => sanitize_text_field($_POST['materials']),
        'VideoLinks' => sanitize_text_field($_POST['video_links']),
        'Notes' => sanitize_textarea_field($_POST['notes']),
        'CreatedBy' => $created_by,
        'CreatedDate' => current_time('mysql'),
        'LastModifiedDate' => current_time('mysql')
    ];
    $result = $wpdb->insert($table, $data);
    if ($result) wp_send_json_success($data);
    else wp_send_json_error('Failed to add lesson plan.');
}

function ajax_edit_lessonplan() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_lessonplans';
    $lessonplan_id = sanitize_text_field($_POST['lessonplan_id']);
    $data = [
        'WeekNumber' => intval($_POST['week_number']),
        'Date' => sanitize_text_field($_POST['date']),
        'Title' => sanitize_text_field($_POST['title']),
        'Description' => sanitize_textarea_field($_POST['description']),
        'Materials' => sanitize_text_field($_POST['materials']),
        'VideoLinks' => sanitize_text_field($_POST['video_links']),
        'Notes' => sanitize_textarea_field($_POST['notes']),
        'LastModifiedDate' => current_time('mysql')
    ];
    $result = $wpdb->update($table, $data, ['LessonPlanID' => $lessonplan_id]);
    if ($result !== false) wp_send_json_success();
    else wp_send_json_error('Failed to update lesson plan.');
}

function ajax_delete_lessonplan() {
    check_ajax_referer('administration_plugin_nonce', 'nonce');
    global $wpdb;
    $table = $wpdb->prefix . 'progtype_edu_lessonplans';
    $lessonplan_id = sanitize_text_field($_POST['lessonplan_id']);
    $result = $wpdb->delete($table, ['LessonPlanID' => $lessonplan_id]);
    if ($result) wp_send_json_success();
    else wp_send_json_error('Failed to delete lesson plan.');
} 