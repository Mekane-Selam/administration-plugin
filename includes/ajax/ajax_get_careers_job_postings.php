<?php
// AJAX handler for public careers job postings list
add_action('wp_ajax_nopriv_get_careers_job_postings', 'ajax_get_careers_job_postings');
add_action('wp_ajax_get_careers_job_postings', 'ajax_get_careers_job_postings');
function ajax_get_careers_job_postings() {
    global $wpdb;
    $table = $wpdb->prefix . 'hr_jobpostings';
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT JobPostingID, Title, DepartmentName, JobType, Location, ClosingDate, Description FROM $table WHERE Status = %s ORDER BY PostedDate DESC",
        'Active'
    ));
    wp_send_json_success($results);
    wp_die();
} 