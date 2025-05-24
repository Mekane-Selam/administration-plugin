<?php
// AJAX handlers for all careers page functionality
add_action('wp_ajax_nopriv_get_careers_job_postings', 'ajax_get_careers_job_postings');
add_action('wp_ajax_get_careers_job_postings', 'ajax_get_careers_job_postings');
add_action('wp_ajax_nopriv_apply_for_job_posting', 'ajax_apply_for_job_posting');
add_action('wp_ajax_apply_for_job_posting', 'ajax_apply_for_job_posting');
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
function ajax_apply_for_job_posting() {
    global $wpdb;
    $required = array('job_posting_id', 'first_name', 'last_name', 'email');
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            wp_send_json_error('Missing required fields.');
            wp_die();
        }
    }
    $job_posting_id = sanitize_text_field($_POST['job_posting_id']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $cover_letter = isset($_POST['cover_letter']) ? sanitize_textarea_field($_POST['cover_letter']) : '';
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    $combined_notes = $cover_letter;
    if ($notes) {
        $combined_notes .= ($cover_letter ? "\n\n" : "") . $notes;
    }
    // Check if applicant exists in core_person
    $person_id = $wpdb->get_var($wpdb->prepare("SELECT PersonID FROM {$wpdb->prefix}core_person WHERE Email = %s", $email));
    $external_id = null;
    if (!$person_id) {
        // Generate unique ExternalApplicantID
        do {
            $unique_code = mt_rand(10000, 99999);
            $external_id = 'EXTAPP' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hr_externalapplicants WHERE ExternalApplicantID = %s", $external_id));
        } while ($exists);
        // Insert into hr_externalapplicants if not exists
        $wpdb->insert(
            $wpdb->prefix . 'hr_externalapplicants',
            array(
                'ExternalApplicantID' => $external_id,
                'FirstName' => $first_name,
                'LastName' => $last_name,
                'Email' => $email,
                'Phone' => $phone,
                'CreatedDate' => current_time('mysql'),
                'LastModifiedDate' => current_time('mysql'),
            ),
            array('%s','%s','%s','%s','%s','%s','%s')
        );
    }
    // Generate unique ApplicationID
    do {
        $unique_code = mt_rand(10000, 99999);
        $application_id = 'APP' . $unique_code;
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hr_applications WHERE ApplicationID = %s", $application_id));
    } while ($exists);
    // Insert into hr_applications
    $result = $wpdb->insert(
        $wpdb->prefix . 'hr_applications',
        array(
            'ApplicationID' => $application_id,
            'JobPostingID' => $job_posting_id,
            'PersonID' => $person_id ? $person_id : null,
            'ExternalApplicantID' => $person_id ? null : $external_id,
            'Status' => 'New',
            'SubmissionDate' => current_time('mysql'),
            'LastModifiedDate' => current_time('mysql'),
            'Notes' => $combined_notes,
        ),
        array('%s','%s','%s','%s','%s','%s','%s','%s')
    );
    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to save application.');
    }
    wp_die();
}
// Future careers AJAX handlers can be added below 