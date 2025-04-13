<?php

class PublicClass {
    public function __construct() {
        // Register shortcodes
        error_log('PublicClass constructor called');
        add_shortcode('job_postings', array($this, 'job_postings_shortcode'));
        add_shortcode('test_shortcode', array($this, 'test_shortcode'));
        // Register job application template
        $this->register_job_application_template();
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
        global $wpdb;
        
        // Get open job postings
        $jobs = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}hr_jobpostings 
            WHERE Status = 'Open' 
            ORDER BY PostedDate DESC"
        );
        
        // Start output buffering
        ob_start();
        
        // Main container
        echo '<div class="job-postings-container">';
        
        // If no jobs found
        if (empty($jobs)) {
            echo '<div class="no-jobs-message">';
            echo '<p>There are currently no job openings. Please check back later.</p>';
            echo '</div>';
            echo '</div>'; // Close container
            return ob_get_clean();
        }
        
        // List view container
        echo '<div class="job-postings-list">';
        echo '<h2>Current Job Openings</h2>';
        
        foreach ($jobs as $job) {
            echo '<div class="job-posting-item" data-job-id="' . esc_attr($job->JobPostingID) . '">';
            echo '<div class="job-posting-header">';
            echo '<h3>' . esc_html($job->Title) . '</h3>';
            echo '<span class="job-meta">' . esc_html($job->DepartmentName) . ' • ' . esc_html($job->Location) . '</span>';
            echo '</div>';
            echo '<div class="job-posting-summary">';
            echo '<p>' . wp_trim_words(wp_strip_all_tags($job->Description), 30) . '</p>';
            echo '</div>';
            echo '<div class="job-posting-actions">';
            echo '<button class="view-details-btn">View Details</button>';
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>'; // Close list view
        
        // Detail view container (initially hidden)
        echo '<div class="job-posting-detail" style="display: none;">';
        echo '<button class="back-to-list-btn">← Back to Jobs</button>';
        echo '<div class="job-detail-content"></div>';
        echo '</div>';
        
        echo '</div>'; // Close main container
        
        // Add JavaScript for handling view switching
        wp_enqueue_script('jquery');
        ?>
        <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                console.log('Job postings script loaded');
                
                // View details button click handler
                $('.view-details-btn').on('click', function() {
                    console.log('View details clicked');
                    const jobId = $(this).closest('.job-posting-item').data('job-id');
                    const $listView = $('.job-postings-list');
                    const $detailView = $('.job-posting-detail');
                    const $detailContent = $('.job-detail-content');
                    
                    console.log('Job ID:', jobId);
                    
                    // Hide list view and show detail view
                    $listView.hide();
                    $detailView.show();
                    
                    // Load job details
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'GET',
                        data: {
                            action: 'get_job_posting',
                            id: jobId
                        },
                        success: function(response) {
                            console.log('AJAX response:', response);
                            if (response.success) {
                                const job = response.data;
                                let html = '<div class="job-detail-header">';
                                html += '<h2>' + job.Title + '</h2>';
                                html += '<div class="job-meta">';
                                html += '<span class="department">' + job.DepartmentName + '</span>';
                                html += '<span class="location">' + job.Location + '</span>';
                                html += '<span class="type">' + job.JobType + '</span>';
                                html += '</div>';
                                html += '</div>';
                                
                                html += '<div class="job-detail-content">';
                                html += '<div class="job-section">';
                                html += '<h3>Job Description</h3>';
                                html += '<div class="content">' + job.Description + '</div>';
                                html += '</div>';
                                
                                html += '<div class="job-section">';
                                html += '<h3>Requirements</h3>';
                                html += '<div class="content">' + job.Requirements + '</div>';
                                html += '</div>';
                                
                                html += '<div class="job-actions">';
                                html += '<a href="' + '<?php echo home_url('job-application'); ?>?job_id=' + job.JobPostingID + '" class="apply-btn">Apply Now</a>';
                                html += '</div>';
                                html += '</div>';
                                
                                $detailContent.html(html);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                });
                
                // Back to list button click handler
                $('.back-to-list-btn').on('click', function() {
                    console.log('Back to list clicked');
                    $('.job-posting-detail').hide();
                    $('.job-postings-list').show();
                });
            });
        })(jQuery);
        </script>
        
        <style>
        .job-postings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .job-postings-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .job-posting-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .job-posting-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .job-posting-header h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .job-meta {
            color: #666;
            font-size: 0.9em;
        }
        
        .job-posting-summary {
            margin: 15px 0;
            color: #444;
        }
        
        .view-details-btn {
            background: #0073aa;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .view-details-btn:hover {
            background: #005177;
        }
        
        .job-posting-detail {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .back-to-list-btn {
            background: none;
            border: none;
            color: #0073aa;
            cursor: pointer;
            padding: 10px 0;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .job-detail-header {
            margin-bottom: 30px;
        }
        
        .job-detail-header h2 {
            margin: 0 0 15px 0;
            color: #333;
        }
        
        .job-section {
            margin-bottom: 30px;
        }
        
        .job-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .job-section .content {
            color: #444;
            line-height: 1.6;
        }
        
        .apply-btn {
            display: inline-block;
            background: #0073aa;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }
        
        .apply-btn:hover {
            background: #005177;
        }
        
        .no-jobs-message {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        </style>
        <?php
        
        return ob_get_clean();
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