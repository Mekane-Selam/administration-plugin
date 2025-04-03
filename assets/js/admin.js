(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize tabs
        initTabs();
        
        // Initialize form handlers
        initFormHandlers();

        // Menu Toggle Functionality
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.administration-sidebar');
        const mainContent = document.querySelector('.administration-main');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
        
        // Page Switching Functionality
        const menuItems = document.querySelectorAll('.sidebar-menu li');
        const pageContents = document.querySelectorAll('.page-content');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all menu items
                menuItems.forEach(menuItem => menuItem.classList.remove('active'));
                
                // Add active class to clicked menu item
                this.classList.add('active');
                
                // Hide all pages
                pageContents.forEach(page => {
                    page.classList.remove('active');
                    page.style.display = 'none';
                });
                
                // Show selected page
                const pageId = this.getAttribute('data-page') + '-page';
                const selectedPage = document.getElementById(pageId);
                if (selectedPage) {
                    selectedPage.classList.add('active');
                    selectedPage.style.display = 'block';
                }
            });
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