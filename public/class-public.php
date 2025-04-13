<?php
/**
 * The public-facing functionality of the plugin.
 */
class Administration_Public {

    /**
     * Initialize the class.
     */
    public function init() {
        // Add shortcode
        add_shortcode('administration', [$this, 'render_administration']);
        add_shortcode('job_postings', [$this, 'job_postings_shortcode']);
        add_shortcode('job_application', [$this, 'job_application_shortcode']);
        
        // Enqueue public scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }
    
    /**
     * Enqueue public scripts and styles.
     */
    public function enqueue_scripts() {
        // Only enqueue if shortcode is present
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'administration')) {
            wp_enqueue_style(
                'administration-css',
                ADMINISTRATION_PLUGIN_URL . 'assets/css/admin.css',
                [],
                ADMINISTRATION_VERSION
            );
            
            // Enqueue WordPress default dashicons
            wp_enqueue_style('dashicons');
            
            wp_enqueue_script(
                'administration-admin',
                ADMINISTRATION_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                ADMINISTRATION_VERSION,
                true
            );
            
            wp_localize_script(
                'administration-admin',
                'administrationData',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'rest_url' => esc_url_raw(rest_url('administration/v1/')),
                    'nonce' => wp_create_nonce('administration_nonce'),
                    'debug' => WP_DEBUG
                ]
            );
        }
    }
    
    /**
     * Render the administration interface.
     */
    public function render_administration($atts) {
        // Check if user has access
        if (!$this->user_has_access()) {
            return '<p>' . __('You do not have permission to access this content.', 'administration') . '</p>';
        }
        
        // Get data for the interface
        $programs = Administration_Database::get_all_programs();
        $persons = Administration_Database::get_all_persons();
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include ADMINISTRATION_PLUGIN_DIR . 'templates/main-interface.php';
        
        // Return the buffered content
        return ob_get_clean();
    }
    
    /**
     * Check if current user has access.
     */
    private function user_has_access() {
        // Get allowed roles
        $allowed_roles = get_option('administration_access_roles', ['administrator']);
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return false;
        }
        
        // Get current user
        $user = wp_get_current_user();
        
        // Check if user has any allowed role
        foreach ($allowed_roles as $role) {
            if (in_array($role, $user->roles)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Test shortcode
     */
    public function test_shortcode($atts) {
        error_log('test_shortcode called');
        return '<p>Test shortcode is working!</p>';
    }

    /**
     * Display job postings
     */
    public function job_postings_shortcode($atts) {
        error_log('job_postings_shortcode called');
        global $wpdb;
        
        // Get open job postings
        $jobs = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}hr_jobpostings 
            WHERE Status = 'Open' 
            ORDER BY PostedDate DESC"
        );
        
        error_log('SQL Query: ' . "SELECT * FROM {$wpdb->prefix}hr_jobpostings WHERE Status = 'Open' ORDER BY PostedDate DESC");
        error_log('Query Results: ' . print_r($jobs, true));
        
        if ($wpdb->last_error) {
            error_log('Job Postings Query Error: ' . $wpdb->last_error);
            return '<p>Error loading job postings.</p>';
        }
        
        if (empty($jobs)) {
            return '<p>No job openings at this time.</p>';
        }
        
        $output = '<div class="job-postings-list">';
        
        foreach ($jobs as $job) {
            $output .= sprintf(
                '<div class="job-posting">
                    <h3>%s</h3>
                    <div class="job-meta">
                        <span class="location">Location: %s</span>
                        <span class="type">Type: %s</span>
                        <span class="posted">Posted: %s</span>
                    </div>
                    <div class="job-description">%s</div>
                    <div class="job-actions">
                        <a href="%s" class="wp-block-button__link wp-element-button">Apply Now</a>
                    </div>
                </div>',
                esc_html($job->Title),
                esc_html($job->Location),
                esc_html($job->JobType),
                esc_html(date('F j, Y', strtotime($job->PostedDate))),
                wp_kses_post($job->Description),
                esc_url(add_query_arg('job_id', $job->JobPostingID, home_url('/job-application/')))
            );
        }
        
        $output .= '</div>';
        
        // Add styles for job postings
        $output .= '
        <style>
            .job-postings-list {
                max-width: 800px;
                margin: 0 auto;
            }
            .job-posting {
                padding: 2rem;
                margin-bottom: 2rem;
                border: 1px solid #ddd;
                border-radius: 4px;
                background: #fff;
            }
            .job-posting h3 {
                margin: 0 0 1rem 0;
            }
            .job-meta {
                margin-bottom: 1rem;
                color: #666;
            }
            .job-meta span {
                margin-right: 1.5rem;
            }
            .job-description {
                margin-bottom: 1.5rem;
            }
            .job-actions {
                margin-top: 1.5rem;
            }
        </style>';
        
        return $output;
    }

    /**
     * Display job application form
     */
    public function job_application_shortcode($atts) {
        // Get job posting ID from URL
        $job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
        
        // Get job details
        global $wpdb;
        $job = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}hr_jobpostings WHERE JobPostingID = %d",
            $job_id
        ));
        
        if (!$job) {
            return '<p>Job posting not found.</p>';
        }
        
        if ($job->Status !== 'Open') {
            return '<p>This position is no longer accepting applications.</p>';
        }
        
        $output = '<div class="job-application-container">';
        
        // Job Details Section
        $output .= sprintf(
            '<div class="job-details">
                <h2>%s</h2>
                <div class="job-meta">
                    <span class="location">Location: %s</span>
                    <span class="type">Type: %s</span>
                </div>
                <div class="job-description">%s</div>
            </div>',
            esc_html($job->Title),
            esc_html($job->Location),
            esc_html($job->JobType),
            wp_kses_post($job->Description)
        );
        
        // Application Form
        $output .= '<form id="job-application-form" method="post" enctype="multipart/form-data">';
        $output .= wp_nonce_field('submit_job_application', 'job_application_nonce', true, false);
        $output .= sprintf('<input type="hidden" name="job_id" value="%d">', esc_attr($job_id));
        
        // Personal Information Section
        $output .= '
        <div class="form-section">
            <h3>Personal Information</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
            </div>
        </div>';
        
        // Application Materials Section
        $output .= '
        <div class="form-section">
            <h3>Application Materials</h3>
            <div class="form-group">
                <label for="resume">Resume (PDF)</label>
                <input type="file" id="resume" name="resume" accept=".pdf" required>
            </div>
            <div class="form-group">
                <label for="cover_letter">Cover Letter</label>
                <textarea id="cover_letter" name="cover_letter" rows="5"></textarea>
            </div>
            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" rows="3"></textarea>
            </div>
        </div>';
        
        // Submit Button
        $output .= '<div class="form-actions">
            <button type="submit" class="wp-block-button__link wp-element-button">Submit Application</button>
        </div>';
        
        $output .= '</form></div>';
        
        // Add styles
        $output .= '
        <style>
            .job-application-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 2rem;
            }
            .job-details {
                margin-bottom: 2rem;
                padding-bottom: 2rem;
                border-bottom: 1px solid #ddd;
            }
            .job-meta {
                margin: 1rem 0;
                color: #666;
            }
            .job-meta span {
                margin-right: 1.5rem;
            }
            .form-section {
                margin-bottom: 2rem;
            }
            .form-section h3 {
                margin-bottom: 1.5rem;
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
            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="tel"],
            .form-group textarea {
                width: 100%;
                padding: 0.5rem;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .form-actions {
                margin-top: 2rem;
            }
            @media (max-width: 600px) {
                .form-row {
                    flex-direction: column;
                }
            }
        </style>';
        
        // Add JavaScript for form handling
        $output .= '
        <script>
        jQuery(document).ready(function($) {
            $("#job-application-form").on("submit", function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                formData.append("action", "submit_job_application");
                
                $.ajax({
                    url: "' . admin_url('admin-ajax.php') . '",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $("#job-application-form").html("<div class=\"success-message\"><h3>Application Submitted!</h3><p>Thank you for your application. We will review it and contact you soon.</p></div>");
                        } else {
                            alert("Error submitting application. Please try again.");
                        }
                    },
                    error: function() {
                        alert("Error submitting application. Please try again.");
                    }
                });
            });
        });
        </script>';
        
        return $output;
    }
}
