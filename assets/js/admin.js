(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize tabs
        initTabs();
        
        // Initialize form handlers
        initFormHandlers();
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