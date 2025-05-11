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

            // Close menu on outside click (mobile)
            $(document).on('click', this.handleDocumentClick);

            // Add Program modal events
            $(document).on('click', '#add-program-btn', this.openAddProgramModal);
            $(document).on('click', '#close-add-program-modal, #cancel-add-program', this.closeAddProgramModal);
            $(document).on('submit', '#add-program-form', this.submitAddProgramForm);
        },

        toggleMenu: function(e) {
            e.preventDefault();
            $('#dashboard-menu').toggleClass('active');
        },

        handleMenuItemClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
            // Close menu on mobile after click
            if (window.innerWidth <= 782) {
                $('#dashboard-menu').removeClass('active');
            }
        },

        handleViewAllClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
            // Close menu on mobile after click
            if (window.innerWidth <= 782) {
                $('#dashboard-menu').removeClass('active');
            }
        },

        handleDocumentClick: function(e) {
            // If click is outside the menu and toggle, close the menu (on mobile)
            if (window.innerWidth <= 782) {
                if (!$(e.target).closest('#dashboard-menu, #dashboard-menu-toggle').length) {
                    $('#dashboard-menu').removeClass('active');
                }
            }
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
                // Set up click handler for program cards after AJAX load
                $(document).off('click', '.program-card').on('click', '.program-card', function() {
                    var programId = $(this).data('program-id');
                    $.ajax({
                        url: administration_plugin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_program_details',
                            program_id: programId,
                            nonce: administration_plugin.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#program-details-content').html(response.data);
                                $('#program-details-modal').addClass('show');
                            }
                        }
                    });
                });
                // Close modal handler
                $(document).off('click', '#program-details-modal .close').on('click', '#program-details-modal .close', function() {
                    $('#program-details-modal').removeClass('show');
                });
                // Edit program button handler
                $(document).off('click', '#program-details-content .edit-button').on('click', '#program-details-content .edit-button', function() {
                    var $btn = $(this);
                    var programId = $btn.data('program-id');
                    // Get current program details from the modal
                    var $content = $('#program-details-content');
                    var name = $content.find('h3').text();
                    var type = $content.find('p:contains("Type:")').text().replace('Type:', '').trim();
                    var description = $content.find('p:contains("Description:")').text().replace('Description:', '').trim();
                    var startDate = $content.find('p:contains("Start Date:")').text().replace('Start Date:', '').trim();
                    var endDate = $content.find('p:contains("End Date:")').text().replace('End Date:', '').trim();
                    var status = $content.find('p:contains("Status:")').text().replace('Status:', '').trim().toLowerCase();
                    // Show edit form
                    var formHtml = `
                        <form id="edit-program-form">
                            <div class="form-field">
                                <label for="edit-program-name">Program Name</label>
                                <input type="text" id="edit-program-name" name="program_name" value="${name}" required>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-type">Program Type</label>
                                <input type="text" id="edit-program-type" name="program_type" value="${type}">
                            </div>
                            <div class="form-field">
                                <label for="edit-program-description">Description</label>
                                <textarea id="edit-program-description" name="description">${description}</textarea>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-start-date">Start Date</label>
                                <input type="date" id="edit-program-start-date" name="start_date" value="${startDate}">
                            </div>
                            <div class="form-field">
                                <label for="edit-program-end-date">End Date</label>
                                <input type="date" id="edit-program-end-date" name="end_date" value="${endDate}">
                            </div>
                            <div class="form-field">
                                <label for="edit-program-status">Status</label>
                                <select id="edit-program-status" name="status">
                                    <option value="active" ${status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Save Changes</button>
                                <button type="button" class="button" id="cancel-edit-program">Cancel</button>
                            </div>
                        </form>
                        <div id="edit-program-message"></div>
                    `;
                    $content.html(formHtml);
                });
                // Cancel edit handler
                $(document).off('click', '#cancel-edit-program').on('click', '#cancel-edit-program', function() {
                    $('#program-details-modal').removeClass('show');
                });
                // Submit edit form handler
                $(document).off('submit', '#edit-program-form').on('submit', '#edit-program-form', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var data = $form.serializeArray();
                    data.push({ name: 'action', value: 'edit_program' });
                    data.push({ name: 'nonce', value: administration_plugin.nonce });
                    data.push({ name: 'program_id', value: $('.edit-button').data('program-id') });
                    $('#edit-program-message').html('<span class="loading">Saving...</span>');
                    $.post(administration_plugin.ajax_url, data, function(response) {
                        if (response.success) {
                            $('#edit-program-message').html('<span class="success-message">Program updated successfully!</span>');
                            Dashboard.refreshProgramsList();
                            setTimeout(function() {
                                $('#program-details-modal').removeClass('show');
                            }, 1200);
                        } else {
                            $('#edit-program-message').html('<span class="error-message">' + (response.data || 'Error updating program.') + '</span>');
                        }
                    });
                });
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
        },

        openAddProgramModal: function(e) {
            e.preventDefault();
            $('#add-program-modal').addClass('show');
        },
        closeAddProgramModal: function(e) {
            e.preventDefault();
            $('#add-program-modal').removeClass('show');
            $('#add-program-form')[0].reset();
            $('#add-program-message').html('');
        },
        submitAddProgramForm: function(e) {
            e.preventDefault();
            var $form = $(this);
            var data = $form.serializeArray();
            data.push({ name: 'action', value: 'add_program' });
            data.push({ name: 'nonce', value: administration_plugin.nonce });
            $('#add-program-message').html('<span class="loading">Saving...</span>');
            $.post(administration_plugin.ajax_url, data, function(response) {
                if (response.success) {
                    $('#add-program-message').html('<span class="success-message">Program added successfully!</span>');
                    // Refresh the programs list widget
                    Dashboard.refreshProgramsList();
                    setTimeout(function() {
                        $('#add-program-modal').fadeOut(200);
                        $('#add-program-form')[0].reset();
                        $('#add-program-message').html('');
                    }, 1200);
                } else {
                    $('#add-program-message').html('<span class="error-message">' + (response.data || 'Error saving program.') + '</span>');
                }
            });
        },
        refreshProgramsList: function() {
            // Reload the programs list widget via AJAX
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
        }
    };

    // Initialize dashboard when document is ready
    $(document).ready(function() {
        Dashboard.init();
    });

})(jQuery); 