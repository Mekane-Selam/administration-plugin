<?php
/**
 * Template for job application form
 */

// Get the job posting ID from the URL
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Get the job posting details
global $wpdb;
$job = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}hr_jobpostings WHERE JobPostingID = %d AND Status = 'Open'",
    $job_id
));

if (!$job) {
    wp_die('Job posting not found or not accepting applications.');
}

// Get current user's person record if logged in
$person = null;
if (is_user_logged_in()) {
    $person = Administration_Database::get_person_by_user_id(get_current_user_id());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('submit_job_application', 'application_nonce');
    
    // Get form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $resume_url = esc_url_raw($_POST['resume_url']);
    $cover_letter = wp_kses_post($_POST['cover_letter']);
    $notes = wp_kses_post($_POST['notes']);
    
    // Insert application
    $result = $wpdb->insert(
        $wpdb->prefix . 'hr_applications',
        array(
            'JobPostingID' => $job_id,
            'PersonID' => $person ? $person->PersonID : null,
            'Status' => 'New',
            'SubmissionDate' => current_time('mysql'),
            'LastModifiedDate' => current_time('mysql'),
            'Notes' => $notes,
            'ResumeURL' => $resume_url,
            'CoverLetterURL' => $cover_letter
        ),
        array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result) {
        // If person doesn't exist, create external applicant record
        if (!$person) {
            $wpdb->insert(
                $wpdb->prefix . 'hr_externalapplicants',
                array(
                    'FirstName' => $first_name,
                    'LastName' => $last_name,
                    'Email' => $email,
                    'Phone' => $phone,
                    'CreatedDate' => current_time('mysql'),
                    'LastModifiedDate' => current_time('mysql')
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );
            
            $external_applicant_id = $wpdb->insert_id;
            
            // Update application with external applicant ID
            $wpdb->update(
                $wpdb->prefix . 'hr_applications',
                array('ExternalApplicantID' => $external_applicant_id),
                array('ApplicationID' => $wpdb->insert_id),
                array('%d'),
                array('%d')
            );
        }
        
        // Show success message
        $success = true;
    }
}

get_header();
?>

<div class="job-application-container">
    <div class="job-application-header">
        <h1><?php echo esc_html($job->Title); ?></h1>
        <p class="job-meta">
            <span class="department"><?php echo esc_html($job->DepartmentName); ?></span>
            <span class="location"><?php echo esc_html($job->Location); ?></span>
            <span class="type"><?php echo esc_html($job->JobType); ?></span>
        </p>
    </div>

    <?php if (isset($success)): ?>
        <div class="application-success">
            <h2>Thank you for your application!</h2>
            <p>We have received your application for the <?php echo esc_html($job->Title); ?> position.</p>
            <p>We will review your application and contact you if we would like to proceed with the next steps.</p>
        </div>
    <?php else: ?>
        <form id="job-application-form" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('submit_job_application', 'application_nonce'); ?>
            
            <div class="form-section">
                <h2>Personal Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                            value="<?php echo $person ? esc_attr($person->FirstName) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                            value="<?php echo $person ? esc_attr($person->LastName) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required
                            value="<?php echo $person ? esc_attr($person->Email) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone"
                            value="<?php echo $person ? esc_attr($person->Phone) : ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Application Materials</h2>
                
                <div class="form-group">
                    <label for="resume">Resume</label>
                    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                    <p class="help-text">Please upload your resume in PDF or Word format.</p>
                </div>
                
                <div class="form-group">
                    <label for="cover_letter">Cover Letter</label>
                    <textarea id="cover_letter" name="cover_letter" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="button button-primary">Submit Application</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<style>
.job-application-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.job-application-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.job-application-header h1 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
}

.job-meta {
    display: flex;
    gap: 1rem;
    color: #666;
}

.job-meta span {
    padding: 0.25rem 0.5rem;
    background: #f5f5f5;
    border-radius: 4px;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section h2 {
    margin: 0 0 1rem 0;
    color: #2c3e50;
    font-size: 1.25rem;
}

.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.help-text {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: #666;
}

.form-actions {
    margin-top: 2rem;
    text-align: right;
}

.button-primary {
    background: #3498db;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
}

.button-primary:hover {
    background: #2980b9;
}

.application-success {
    text-align: center;
    padding: 2rem;
    background: #e8f5e9;
    border-radius: 4px;
    color: #2e7d32;
}

.application-success h2 {
    margin: 0 0 1rem 0;
    color: #2e7d32;
}
</style>

<?php get_footer(); ?> 