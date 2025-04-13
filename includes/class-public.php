<?php

class PublicClass {
    public function __construct() {
        // Register shortcodes
        add_shortcode('job_postings', array($this, 'job_postings_shortcode'));
        // Register job application template
        $this->register_job_application_template();
    }

    /**
     * Display job postings
     */
    public function job_postings_shortcode($atts) {
        global $wpdb;
        
        // Get open job postings
        $jobs = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}hr_jobpostings 
            WHERE Status = 'Open' 
            ORDER BY PostedDate DESC"
        );
        
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
     * Register the job application template
     */
    public function register_job_application_template() {
        add_filter('theme_page_templates', array($this, 'add_job_application_template'));
        add_filter('template_include', array($this, 'load_job_application_template'));
    }

    /**
     * Add the job application template to the template list
     */
    public function add_job_application_template($templates) {
        $templates['templates/job-application-template.php'] = __('Job Application', 'mekaneselam');
        return $templates;
    }

    /**
     * Load the job application template when selected
     */
    public function load_job_application_template($template) {
        if (is_page()) {
            $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            if ('templates/job-application-template.php' === $page_template) {
                $template = plugin_dir_path(dirname(__FILE__)) . 'templates/job-application-template.php';
            }
        }
        return $template;
    }
} 