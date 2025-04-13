<?php

class PublicClass {
    public function __construct() {
        // Register shortcodes
        add_shortcode('job_postings', array($this, 'job_postings_shortcode'));
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
        
        if (empty($jobs)) {
            return '<p>No job openings at this time.</p>';
        }
        
        $output = '<div class="job-postings-list">';
        
        foreach ($jobs as $job) {
            $output .= sprintf(
                '<div class="job-posting">
                    <h3>%s</h3>
                    <div class="job-meta">
                        <span class="department">%s</span>
                        <span class="location">%s</span>
                        <span class="type">%s</span>
                    </div>
                    <div class="job-description">%s</div>
                    <div class="job-actions">
                        <a href="%s" class="button">Apply Now</a>
                    </div>
                </div>',
                esc_html($job->Title),
                esc_html($job->DepartmentName),
                esc_html($job->Location),
                esc_html($job->JobType),
                wp_kses_post($job->Description),
                esc_url(add_query_arg('job_id', $job->JobPostingID, home_url('/job-application/')))
            );
        }
        
        $output .= '</div>';
        
        return $output;
    }
} 