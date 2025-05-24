<?php
// Careers page job list template
wp_enqueue_style('administration-plugin-careers', plugins_url('assets/css/public/careers.css', dirname(__FILE__, 3)));
wp_enqueue_script('administration-plugin-careers', plugins_url('assets/js/public/careers.js', dirname(__FILE__, 3)), array('jquery'), null, true);
// Pass AJAX URL to JS
wp_add_inline_script('administration-plugin-careers', 'window.careers_plugin_ajax_url = "' . admin_url('admin-ajax.php') . '";', 'before');
?>
<div class="careers-section">
  <div class="careers-section-title">Current Job Openings</div>
  <div class="careers-job-list"></div>
</div> 