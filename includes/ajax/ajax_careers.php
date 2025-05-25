<?php
// AJAX handlers for all careers page functionality
add_action('wp_ajax_nopriv_get_careers_job_postings', 'ajax_get_careers_job_postings');
add_action('wp_ajax_get_careers_job_postings', 'ajax_get_careers_job_postings');
add_action('wp_ajax_nopriv_apply_for_job_posting', 'ajax_apply_for_job_posting');
add_action('wp_ajax_apply_for_job_posting', 'ajax_apply_for_job_posting');

// Google Drive integration config
require_once dirname(__FILE__, 2) . '/integrations/class-google-drive.php';
$google_drive_credentials = '/var/credentials/ninth-arena-450804-u2-0c435a1bf729.json';
$job_postings_parent_folder = '15uxOSGKsmbEh1ojQZTADpGZ10grYs4LB';

function create_job_posting_drive_folder($job_title, $job_posting_id) {
    global $google_drive_credentials, $job_postings_parent_folder;
    $drive = new Administration_Google_Drive($google_drive_credentials, $job_postings_parent_folder);
    $folder_name = 'Job - ' . preg_replace('/[^a-zA-Z0-9 _-]/', '', $job_title) . ' - ' . $job_posting_id;
    return $drive->createFolder($folder_name);
}

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
    $required = array('job_posting_id', 'first_name', 'last_name', 'email', 'phone');
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
    $phone = sanitize_text_field($_POST['phone']);
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    // Check if applicant exists in core_person
    $person_id = $wpdb->get_var($wpdb->prepare("SELECT PersonID FROM {$wpdb->prefix}core_person WHERE Email = %s", $email));
    $external_id = null;
    $external_applicant_created = false;
    if (!$person_id) {
        // Generate unique ExternalApplicantID
        do {
            $unique_code = mt_rand(10000, 99999);
            $external_id = 'EXTAPP' . $unique_code;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hr_externalapplicants WHERE ExternalApplicantID = %s", $external_id));
        } while ($exists);
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
        $external_applicant_created = true;
    }
    // Generate unique ApplicationID
    do {
        $unique_code = mt_rand(10000, 99999);
        $application_id = 'APP' . $unique_code;
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}hr_applications WHERE ApplicationID = %s", $application_id));
    } while ($exists);
    // Get job posting folder ID
    $job = $wpdb->get_row($wpdb->prepare("SELECT DriveFolderID, Title FROM {$wpdb->prefix}hr_jobpostings WHERE JobPostingID = %s", $job_posting_id));
    $applicant_folder_id = null;
    $resume_url = '';
    $cover_letter_url = '';
    $has_file = isset($_FILES['resume']) && $_FILES['resume']['size'] > 0;
    $has_cover = isset($_FILES['cover_letter']) && $_FILES['cover_letter']['size'] > 0;
    if ($job && $job->DriveFolderID) {
        require_once dirname(__FILE__, 2) . '/integrations/class-google-drive.php';
        $google_drive_credentials = '/var/credentials/ninth-arena-450804-u2-0c435a1bf729.json';
        $job_postings_parent_folder = '15uxOSGKsmbEh1ojQZTADpGZ10grYs4LB';
        $drive = new Administration_Google_Drive($google_drive_credentials, $job_postings_parent_folder);
        $applicant_folder_name = 'Applicant - ' . preg_replace('/[^a-zA-Z0-9 _-]/', '', $last_name) . ', ' . preg_replace('/[^a-zA-Z0-9 _-]/', '', $first_name) . ' - ' . $application_id;
        try {
            // Always create the applicant folder
            $applicant_folder_id = $drive->createFolder($applicant_folder_name, $job->DriveFolderID);
            // Only upload files if present
            if ($has_file && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $tmp_path = $_FILES['resume']['tmp_name'];
                $file_name = 'Resume - ' . $last_name . ', ' . $first_name . ' - ' . $application_id . '.pdf';
                $file_id = $drive->uploadFile($tmp_path, $file_name, $applicant_folder_id);
                $resume_url = $drive->getFileUrl($file_id);
            }
            if ($has_cover && $_FILES['cover_letter']['error'] === UPLOAD_ERR_OK) {
                $tmp_path = $_FILES['cover_letter']['tmp_name'];
                $file_name = 'Cover Letter - ' . $last_name . ', ' . $first_name . ' - ' . $application_id . '.pdf';
                $file_id = $drive->uploadFile($tmp_path, $file_name, $applicant_folder_id);
                $cover_letter_url = $drive->getFileUrl($file_id);
            }
        } catch (Exception $e) {
            error_log('Google Drive error (application upload): ' . $e->getMessage());
            if (!$applicant_folder_id) {
                wp_send_json_error('Failed to create applicant folder in Google Drive: ' . $e->getMessage());
                wp_die();
            }
            // If folder was created but file upload failed, continue but log the error
        }
    }
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
            'Notes' => $notes,
            'ResumeURL' => $resume_url,
            'CoverLetterURL' => $cover_letter_url,
            'ApplicantDriveFolderID' => $applicant_folder_id,
        ),
        array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
    );
    if ($result) {
        wp_send_json_success();
    } else {
        // If we just created an external applicant, delete it to avoid orphaned records
        if ($external_applicant_created && $external_id) {
            $wpdb->delete($wpdb->prefix . 'hr_externalapplicants', array('ExternalApplicantID' => $external_id));
        }
        wp_send_json_error('Failed to save application.');
    }
    wp_die();
}
// Future careers AJAX handlers can be added below 