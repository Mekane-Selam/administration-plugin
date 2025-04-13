<?php
/**
 * Template Name: Job Application
 * 
 * @package MekaneSelam
 */

// Get header
get_header();

// Get job posting ID from URL
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Get job details
global $wpdb;
$job = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}hr_jobpostings WHERE JobPostingID = %d",
    $job_id
));

if (!$job) {
    ?>
    <div class="wp-block-group alignwide">
        <div class="wp-block-group__inner-container">
            <p>Job posting not found.</p>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="wp-block-group alignwide">
        <div class="wp-block-group__inner-container">
            <!-- Job Details Section -->
            <h2 class="wp-block-heading"><?php echo esc_html($job->Title); ?></h2>
            <div class="job-description"><?php echo wp_kses_post($job->Description); ?></div>
            
            <!-- Application Form -->
            <form id="job-application-form" class="wp-block-group" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('submit_job_application', 'job_application_nonce'); ?>
                <input type="hidden" name="job_id" value="<?php echo esc_attr($job_id); ?>">
                
                <!-- Personal Information Section -->
                <div class="form-section wp-block-group">
                    <h3 class="wp-block-heading">Personal Information</h3>
                    <div class="wp-block-columns">
                        <div class="wp-block-column">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" class="wp-block-input" required>
                        </div>
                        <div class="wp-block-column">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="wp-block-input" required>
                        </div>
                    </div>
                    
                    <div class="wp-block-columns">
                        <div class="wp-block-column">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="wp-block-input" required>
                        </div>
                        <div class="wp-block-column">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="wp-block-input">
                        </div>
                    </div>
                </div>
                
                <!-- Application Materials Section -->
                <div class="form-section wp-block-group">
                    <h3 class="wp-block-heading">Application Materials</h3>
                    <div class="wp-block-group">
                        <label for="resume">Resume (PDF)</label>
                        <input type="file" id="resume" name="resume" accept=".pdf" required>
                    </div>
                    
                    <div class="wp-block-group">
                        <label for="cover_letter">Cover Letter</label>
                        <textarea id="cover_letter" name="cover_letter" rows="5" class="wp-block-textarea"></textarea>
                    </div>
                    
                    <div class="wp-block-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="wp-block-textarea"></textarea>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="wp-block-group">
                    <button type="submit" class="wp-block-button__link wp-element-button">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

// Add custom styles for the form
?>
<style>
    .job-application-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }
    .form-section {
        margin-bottom: 2rem;
    }
    .form-section h3 {
        margin-bottom: 1.5rem;
    }
    .wp-block-columns {
        margin-bottom: 1.5rem;
    }
    .wp-block-input,
    .wp-block-textarea {
        width: 100%;
        padding: 0.5rem;
        margin-top: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .wp-block-button__link {
        margin-top: 1.5rem;
    }
</style>
<?php
// Get footer
get_footer(); 