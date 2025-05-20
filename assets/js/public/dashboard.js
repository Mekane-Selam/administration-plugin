/**
 * Public dashboard JavaScript functionality
 */
(function($) {
    'use strict';

    const Dashboard = {
        init: function() {
            this.initEventHandlers();
            this.loadDashboardContent();
        },

        initEventHandlers: function() {
            // Menu toggle
            $('#dashboard-menu-toggle').on('click', this.toggleMenu);
            
            // Menu items
            $('.menu-item').on('click', this.handleMenuItemClick);
            
            // View all links
            $('.view-all').on('click', this.handleViewAllClick);

            // Close menu on outside click (mobile)
            $(document).on('click', this.handleDocumentClick);

            // Program modals
            $(document).on('click', '#add-program-btn', this.openAddProgramModal);
            $(document).on('click', '#close-add-program-modal, #cancel-add-program', this.closeAddProgramModal);
            $(document).on('submit', '#add-program-form', this.submitAddProgramForm);
            $('.program-card').on('click', this.showProgramDetails);

            // Filter handlers
            $('#filter-status, #filter-type').on('change', Dashboard.applyFilters);
            $('#filter-date-start, #filter-date-end').on('change', Dashboard.applyFilters);
            $('#filter-search').on('input', Dashboard.applyFilters);

            // Sync button
            $(document).on('click', '#sync-users-btn', this.handleSyncUsersClick);
            $(document).on('click', '#add-person-btn', this.handleAddPersonClick);

            // Sync/Add/Sort for Persons-Content section
            $(document).on('click', '#sync-users-content-btn', this.handleSyncUsersClick);
            $(document).on('click', '#add-person-content-btn', this.handleAddPersonClick);
            $(document).on('click', '#sort-people-btn', function(e) {
                e.preventDefault();
                $(this).siblings('.sort-dropdown').toggle();
            });
        },

        toggleMenu: function(e) {
            e.preventDefault();
            $('#dashboard-menu').toggleClass('active');
        },

        handleMenuItemClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
            if (window.innerWidth <= 782) {
                $('#dashboard-menu').removeClass('active');
            }
        },

        handleViewAllClick: function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            Dashboard.loadPage(page);
            if (window.innerWidth <= 782) {
                $('#dashboard-menu').removeClass('active');
            }
        },

        handleDocumentClick: function(e) {
            if (window.innerWidth <= 782) {
                if (!$(e.target).closest('#dashboard-menu, #dashboard-menu-toggle').length) {
                    $('#dashboard-menu').removeClass('active');
                }
            }
        },

        loadPage: function(page) {
            $('.menu-item').removeClass('active');
            $(`.menu-item[data-page="${page}"]`).addClass('active');
            $('.administration-dashboard-content').addClass('loading');

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
                        Dashboard.rebindProgramFilters();
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

        loadDashboardContent: function() {
            this.loadPage('main');
        },

        initializeWidgets: function() {
            this.initializeProgramsWidget();
            this.initializeParishWidget();
            this.initializeCalendarWidget();
            this.initializeHRWidget();
        },

        initializeProgramsWidget: function() {
            const $widget = $('#programs-overview');
            if ($widget.length) {
                this.loadProgramsData();
                this.setupProgramCardHandlers();
            }
        },

        setupProgramCardHandlers: function() {
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

            $(document).off('click', '#program-details-modal .close').on('click', '#program-details-modal .close', function() {
                $('#program-details-modal').removeClass('show');
            });

            $(document).off('click', '#program-details-content .edit-button').on('click', '#program-details-content .edit-button', this.handleEditProgramClick);
        },

        handleEditProgramClick: function() {
            var $btn = $(this);
            var programId = $btn.data('program-id');
            var $content = $('#program-details-content');
            var programData = {
                name: $content.find('h3').text(),
                type: $content.find('p:contains("Type:")').text().replace('Type:', '').trim(),
                description: $content.find('p:contains("Description:")').text().replace('Description:', '').trim(),
                startDate: $content.find('p:contains("Start Date:")').text().replace('Start Date:', '').trim(),
                endDate: $content.find('p:contains("End Date:")').text().replace('End Date:', '').trim(),
                status: $content.find('p:contains("Status:")').text().replace('Status:', '').trim().toLowerCase()
            };

            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_people_for_owner_select',
                    nonce: administration_plugin.nonce
                },
                success: function(resp) {
                    var people = resp.success && Array.isArray(resp.data) ? resp.data : [];
                    var ownerOptions = '<option value="">Select Owner</option>';
                    var currentOwner = $content.find('p:contains("Owner:")').text().replace('Owner:', '').trim();
                    
                    people.forEach(function(person) {
                        var fullName = person.FirstName + ' ' + person.LastName;
                        var selected = (fullName === currentOwner) ? 'selected' : '';
                        ownerOptions += `<option value="${person.PersonID}" ${selected}>${fullName}</option>`;
                    });

                    if (people.length === 0) {
                        ownerOptions += '<option value="" disabled>No people found. Please add people first.</option>';
                    }

                    var typeOptions = Dashboard.programTypes.map(function(opt) {
                        var selected = (opt.toLowerCase() === programData.type.toLowerCase()) ? 'selected' : '';
                        return `<option value="${opt.toLowerCase()}" ${selected}>${opt}</option>`;
                    }).join('');

                    var formHtml = `
                        <form id="edit-program-form">
                            <div class="form-field">
                                <label for="edit-program-name">Program Name</label>
                                <input type="text" id="edit-program-name" name="program_name" value="${programData.name}" required>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-type">Program Type</label>
                                <select id="edit-program-type" name="program_type">${typeOptions}</select>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-description">Description</label>
                                <textarea id="edit-program-description" name="description">${programData.description}</textarea>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-start-date">Start Date</label>
                                <input type="date" id="edit-program-start-date" name="start_date" value="${programData.startDate}">
                            </div>
                            <div class="form-field">
                                <label for="edit-program-end-date">End Date</label>
                                <input type="date" id="edit-program-end-date" name="end_date" value="${programData.endDate}">
                            </div>
                            <div class="form-field">
                                <label for="edit-program-status">Status</label>
                                <select id="edit-program-status" name="status">
                                    <option value="active" ${programData.status === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${programData.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="edit-program-owner">Program Owner</label>
                                <select id="edit-program-owner" name="program_owner" required>${ownerOptions}</select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Save Changes</button>
                                <button type="button" class="button" id="cancel-edit-program">Cancel</button>
                            </div>
                        </form>
                        <div id="edit-program-message"></div>
                    `;
                    $content.html(formHtml);
                }
            });
        },

        initializeParishWidget: function() {
            const $widget = $('#parish-overview');
            if ($widget.length) {
                this.loadParishData();
            }
        },

        initializeCalendarWidget: function() {
            const $widget = $('#calendar-overview');
            if ($widget.length) {
                this.loadCalendarData();
            }
        },

        initializeHRWidget: function() {
            const $widget = $('#hr-overview');
            if ($widget.length) {
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

        loadParishData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_people_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.parish-list').html(response.data);
                    }
                }
            });
        },

        loadCalendarData: function() {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_volunteer_ops_overview',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('.calendar-list').html(response.data);
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
                    setTimeout(function() {
                        $('#add-program-modal').removeClass('show');
                        $('#add-program-form')[0].reset();
                        $('#add-program-message').html('');
                        Dashboard.loadPage('programs');
                    }, 800);
                } else {
                    $('#add-program-message').html('<span class="error-message">' + (response.data || 'Error saving program.') + '</span>');
                }
            });
        },

        applyFilters: function() {
            const search = $('#filter-search').val().toLowerCase().trim();
            const status = $('#filter-status').val().toLowerCase();
            const type = $('#filter-type').val().toLowerCase();
            const startDate = $('#filter-date-start').val();
            const endDate = $('#filter-date-end').val();

            $('.program-card').each(function() {
                const $card = $(this);
                const cardName = $card.find('h3').text().toLowerCase();
                const cardType = $card.find('.program-type').text().toLowerCase();
                const cardStatus = $card.find('.program-status').text().toLowerCase();
                const cardDesc = $card.data('description') ? $card.data('description').toLowerCase() : '';
                
                const dates = $card.find('.program-dates').text().split(' - ');
                const cardStartDate = dates[0] ? new Date(dates[0]) : null;
                const cardEndDate = dates[1] ? new Date(dates[1]) : null;
                const filterStartDate = startDate ? new Date(startDate) : null;
                const filterEndDate = endDate ? new Date(endDate) : null;

                let show = true;

                if (search && !(cardName.includes(search) || 
                               cardType.includes(search) || 
                               cardDesc.includes(search))) {
                    show = false;
                }

                if (status && cardStatus !== status) {
                    show = false;
                }

                if (type && cardType !== type) {
                    show = false;
                }

                if (filterStartDate && cardStartDate && cardStartDate < filterStartDate) {
                    show = false;
                }
                if (filterEndDate && cardEndDate && cardEndDate > filterEndDate) {
                    show = false;
                }

                if (show) {
                    $card.removeClass('filtered-out').show();
                } else {
                    $card.addClass('filtered-out');
                    setTimeout(() => {
                        if (!$card.hasClass('filtered-out')) return;
                        $card.hide();
                    }, 200);
                }
            });

            const visibleCards = $('.program-card:visible').length;
            const $noResults = $('.no-results-message');
            
            if (visibleCards === 0) {
                if ($noResults.length === 0) {
                    $('.programs-grid').append('<p class="no-results-message">No programs match your filters.</p>');
                }
            } else {
                $noResults.remove();
            }
        },

        rebindProgramFilters: function() {
            $('#filter-status, #filter-type').off('change').on('change', Dashboard.applyFilters);
            $('#filter-date-start, #filter-date-end').off('change').on('change', Dashboard.applyFilters);
            $('#filter-search').off('input').on('input', Dashboard.applyFilters);
        },

        programTypes: (typeof administration_plugin !== 'undefined' && administration_plugin.program_types) ? administration_plugin.program_types : ['Education', 'Health', 'Social'],

        handleSyncUsersClick: function(e) {
            e.preventDefault();
            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Syncing...');
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'administration_force_sync_users',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $btn.html('<span class="dashicons dashicons-yes"></span> Synced!');
                        setTimeout(function() {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                        }, 1500);
                    } else {
                        $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                        setTimeout(function() {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                        }, 2000);
                    }
                },
                error: function() {
                    $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                    setTimeout(function() {
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                    }, 2000);
                }
            });
        },

        handleAddPersonClick: function(e) {
            e.preventDefault();
            // Stub: open modal (to be implemented)
            alert('Add Person modal coming soon!');
        }
    };

    // Initialize dashboard when document is ready
    $(document).ready(function() {
        Dashboard.init();
    });

})(jQuery); 