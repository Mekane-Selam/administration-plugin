jQuery(document).ready(function($) {
    'use strict';
    
    console.log('Administration plugin initialized');
    
    // Debug: Check if we can find our elements
    console.log('Sidebar menu items found:', $('.sidebar-menu li').length);
    console.log('Page content sections found:', $('.page-content').length);
    
    // Initialize sidebar state
    const $sidebar = $('.administration-sidebar');
    const $main = $('.administration-main');
    const $menuToggle = $('.menu-toggle');
    
    // Set initial state (menu expanded)
    $main.addClass('collapsed').removeClass('expanded');
    
    // Menu Toggle Functionality
    $menuToggle.on('click', function() {
        // Toggle menu button state
        $(this).toggleClass('active');
        
        // Toggle sidebar state
        $sidebar.toggleClass('collapsed');
        
        // Toggle main content state - IMPORTANT: only one class should be present at a time
        if ($sidebar.hasClass('collapsed')) {
            $main.removeClass('collapsed').addClass('expanded');
        } else {
            $main.removeClass('expanded').addClass('collapsed');
        }
    });
    
    // Page Switching Functionality
    $(document).on('click', '.sidebar-menu li a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $menuItem = $(this).parent('li');
        const pageId = $menuItem.data('page');
        
        console.log('Menu item clicked:', pageId);
        
        // Update active states
        $('.sidebar-menu li').removeClass('active');
        $menuItem.addClass('active');
        
        // Hide all pages first
        $('.page-content').hide();
        console.log('Hidden all pages');
        
        // Show selected page
        const $targetPage = $('#' + pageId + '-page');
        console.log('Target page found:', $targetPage.length > 0);
        
        if ($targetPage.length) {
            $targetPage.fadeIn(200);
            console.log('Showed page:', pageId);
            
            // Update search placeholder
            $('.administration-search input').attr(
                'placeholder',
                'Search ' + pageId.charAt(0).toUpperCase() + pageId.slice(1)
            );
        } else {
            console.error('Target page not found:', pageId);
        }
        
        return false;
    });
    
    // Add button handlers
    $('.add-button').on('click', function() {
        const section = $(this).closest('.administration-section');
        const sectionTitle = section.find('h3').text().trim();
        alert('Add ' + sectionTitle + ' feature coming soon!');
    });
    
    // Initialize the active page
    const $activePage = $('.sidebar-menu li.active');
    if ($activePage.length) {
        $activePage.find('a').trigger('click');
    } else {
        $('.sidebar-menu li:first-child a').trigger('click');
    }
});