/**
 * Public dashboard JavaScript functionality
 */
(function($) {
    'use strict';

    // Dashboard namespace
    const Dashboard = {
        init: function() {
            this.bindEvents();
            this.loadInitialContent();
        },

        bindEvents: function() {
            // Menu toggle
            $('#dashboard-menu-toggle').on('click', this.toggleMenu);
            
            // Menu items
            $('.menu-item').on('click', this.handleMenuItemClick);
            
            // View all links
            $('.view-all').on('click', this.handleViewAllClick);
        },

        toggleMenu: function(e) {
            e.preventDefault();
            $('#dashboard-menu').toggleClass('active');
        },

        handleMenuItemClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
        },

        handleViewAllClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
        },

        loadPage: function(page) {
            // Update active menu item
            $('.menu-item').removeClass('active');
            $(`.menu-item[data-page="${page}"]`).addClass('active');

            // Show loading state
            $('.administration-dashboard-content').addClass('loading');

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
                        Dashboard.initializeWidgets();
                    } else {
                        console.error('Error loading dashboard page:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $('.administration-dashboard-content').removeClass('loading');
                }
            });
        },

        loadInitialContent: function() {
            this.loadPage('main');
        },

        initializeWidgets: function() {
            // Initialize any widget-specific functionality
            this.initializeProgramsWidget();
            this.initializePeopleWidget();
            this.initializeVolunteerOpsWidget();
            this.initializeHRWidget();
        },

        initializeProgramsWidget: function() {
            const $widget = $('#programs-overview');
            if ($widget.length) {
                // Load programs data
                this.loadProgramsData();
            }
        },

        initializePeopleWidget: function() {
            const $widget = $('#people-overview');
            if ($widget.length) {
                // Load people data
                this.loadPeopleData();
            }
        },

        initializeVolunteerOpsWidget: function() {
            const $widget = $('#volunteer-ops-overview');
            if ($widget.length) {
                // Load volunteer operations data
                this.loadVolunteerOpsData();
            }
        },

        initializeHRWidget: function() {
            const $widget = $('#hr-overview');
            if ($widget.length) {
                // Load HR data
                this.loadHRData();
            }
        },

        loadProgramsData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_programs_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.programs-list').html(response.data);
                    }
                }
            });
        },

        loadPeopleData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_people_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.people-list').html(response.data);
                    }
                }
            });
        },

        loadVolunteerOpsData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_volunteer_ops_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.volunteer-ops-list').html(response.data);
                    }
                }
            });
        },

        loadHRData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_hr_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.hr-list').html(response.data);
                    }
                }
            });
        }
    };

    // Initialize dashboard when document is ready
    $(document).ready(function() {
        Dashboard.init();
    });

})(jQuery); 