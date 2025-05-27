<?php
/**
 * Main public dashboard template
 */

// Add a body class for the public dashboard
add_filter('body_class', function($classes) {
    $classes[] = 'administration-dashboard-public';
    return $classes;
});

// Check if user has access
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'administration-plugin'));
}
?>

<div class="administration-public-dashboard">
    <?php 
    // Include header partial
    include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/header.php';
    
    // Include main content partial
    include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/dashboard-content.php';
    ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Menu toggle functionality
    $('#dashboard-menu-toggle').on('click', function() {
        $('#dashboard-menu').toggleClass('active');
    });

    // Menu item click handler
    $('.menu-item').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadDashboardPage(page);
    });

    // View all click handler
    $('.view-all').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadDashboardPage(page);
    });

    // Function to load dashboard content
    function loadDashboardPage(page) {
        // Update active menu item
        $('.menu-item').removeClass('active');
        $(`.menu-item[data-page="${page}"]`).addClass('active');

        // Load content via AJAX
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: {
                action: 'load_dashboard_page',
                page: page,
                nonce: administration_plugin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.administration-dashboard-content').html(response.data);
                } else {
                    console.error('Error loading dashboard page:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }

    // Load initial dashboard content
    loadDashboardPage('parish');
});
</script> 