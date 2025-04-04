jQuery(document).ready(function($) {
    'use strict';

    // Initialize tabs
    initTabs();
    
    // Initialize form handlers
    initFormHandlers();

    console.log('Administration plugin initialized');
    
    // Debug: Check if we can find our elements
    console.log('Sidebar menu items found:', $('.sidebar-menu li').length);
    console.log('Page content sections found:', $('.page-content').length);

    // Menu Toggle Functionality
    $('.menu-toggle').on('click', function() {
        console.log('Menu toggle clicked');
        $('.administration-sidebar').toggleClass('collapsed');
        $('.administration-main').toggleClass('expanded');
    });
    
    // Page Switching Functionality
    $(document).on('click', '.sidebar-menu li', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $this = $(this);
        const pageId = $this.data('page');
        
        console.log('Menu item clicked:', pageId);
        
        // Update active states
        $('.sidebar-menu li').removeClass('active');
        $this.addClass('active');
        
        // Hide all pages first
        $('.page-content').hide();
        console.log('Hidden all pages');
        
        // Show selected page
        const $targetPage = $('#' + pageId + '-page');
        console.log('Target page found:', $targetPage.length > 0);
        
        if ($targetPage.length) {
            $targetPage.show();
            console.log('Showed page:', pageId);
            
            // Update search placeholder
            $('.administration-search input').attr(
                'placeholder',
                'Search ' + pageId.charAt(0).toUpperCase() + pageId.slice(1)
            );
        } else {
            console.error('Target page not found:', pageId);
        }
    });
    
    // Debug: Log initial state
    console.log('Initial active menu item:', $('.sidebar-menu li.active').data('page'));
    console.log('Initial visible page:', $('.page-content:visible').attr('id'));

    // Add button handlers
    $('.add-button').on('click', function() {
        const section = $(this).closest('.administration-section');
        const sectionTitle = section.find('h3').text().trim();
        alert('Add ' + sectionTitle + ' feature coming soon!');
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