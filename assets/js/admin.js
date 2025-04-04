jQuery(document).ready(function($) {
    'use strict';

    // Initialize tabs
    initTabs();
    
    // Initialize form handlers
    initFormHandlers();

    console.log('Administration plugin initialized'); // Debug log

    // Menu Toggle Functionality
    $('.menu-toggle').on('click', function() {
        $('.administration-sidebar').toggleClass('collapsed');
        $('.administration-main').toggleClass('expanded');
    });
    
    // Page Switching Functionality
    $('.sidebar-menu li').on('click', function(e) {
        e.preventDefault();
        console.log('Menu item clicked'); // Debug log
        
        // Get the page id from data attribute
        const pageId = $(this).data('page');
        console.log('Switching to page:', pageId); // Debug log
        
        // Update active menu item
        $('.sidebar-menu li').removeClass('active');
        $(this).addClass('active');
        
        // Hide all pages
        $('.page-content').removeClass('active').hide();
        
        // Show selected page
        $('#' + pageId + '-page').addClass('active').show();
        
        // Update search placeholder
        $('.administration-search input').attr('placeholder', 'Search ' + pageId.charAt(0).toUpperCase() + pageId.slice(1));
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