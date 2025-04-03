(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize tabs
        initTabs();
        
        // Initialize form handlers
        initFormHandlers();

        // Menu Toggle Functionality
        $('.menu-toggle').on('click', function() {
            $('.administration-sidebar').toggleClass('collapsed');
            $('.administration-main').toggleClass('expanded');
        });
        
        // Page Switching Functionality
        $('.sidebar-menu li').on('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all menu items
            $('.sidebar-menu li').removeClass('active');
            
            // Add active class to clicked menu item
            $(this).addClass('active');
            
            // Hide all pages with fade
            $('.page-content').fadeOut(200).removeClass('active');
            
            // Show selected page
            const pageId = $(this).data('page') + '-page';
            $('#' + pageId).fadeIn(200).addClass('active');
        });
    });

    function initTabs() {
        $('.administration-tab-nav .nav-tab').on('click', function() {
            const tabId = $(this).data('tab');
            
            // Update active tab
            $('.administration-tab-nav .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            // Show corresponding tab content
            $('.administration-tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
        });
    }

    function initFormHandlers() {
        // Handle select all checkboxes
        $('#select-all-roles').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('input[name="administration_access_roles[]"]').prop('checked', isChecked);
        });
    }

})(jQuery);