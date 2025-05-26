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
            console.log('Initializing event handlers...');
            
            // Debug all potential button selectors
            console.log('All potential add buttons:', $('[id*="add-person"], [class*="add-person"]').length);
            console.log('All potential sync buttons:', $('[id*="sync"], [class*="sync"]').length);
            
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

            // Sync button - bind to all possible selectors
            $(document).on('click', '[id*="sync"], [class*="sync"]', function(e) {
                console.log('Sync button clicked:', this);
                Dashboard.handleSyncUsersClick.call(this, e);
            });
            
            // Add Person modal handlers
            $(document).on('click', '#add-person-content-btn', this.openAddPersonModal);
            $(document).on('click', '#add-person-modal .close, #cancel-add-person', this.closeAddPersonModal);
            $(document).on('submit', '#add-person-form', this.submitAddPersonForm);

            // Sort/Filter for Persons-Content section
            $(document).on('click', '#sort-people-btn', function(e) {
                e.preventDefault();
                $(this).siblings('.sort-dropdown').toggle();
            });
            $(document).on('click', '.sort-dropdown li a', this.handleSortPeopleClick);

            // Load person details in right column when a person is clicked
            $(document).on('click', '.person-row', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var personId = $(this).data('person-id');
                if (!personId) return;
                $('.person-row').removeClass('selected');
                $(this).addClass('selected');
                $('#person-details-panel').hide();
                $('#person-details-general-content, #person-details-family-content, #person-details-roles-content').html('<div class="loading">Loading...</div>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_full_person_details',
                        nonce: administration_plugin.nonce,
                        person_id: personId
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            Dashboard._renderPersonDetails(response.data);
                            $('#person-details-panel').show();
                        } else {
                            $('#person-details-general-content, #person-details-family-content, #person-details-roles-content').html('<div class="error-message">Failed to load details.</div>');
                            $('#person-details-panel').show();
                        }
                    },
                    error: function() {
                        $('#person-details-general-content, #person-details-family-content, #person-details-roles-content').html('<div class="error-message">Failed to load details.</div>');
                        $('#person-details-panel').show();
                    }
                });
            });

            // Add edit button handler for inline editing (General section only for now)
            $(document).off('click', '.person-details-edit-btn').on('click', '.person-details-edit-btn', function() {
                var section = $(this).data('section');
                if (section === 'general') {
                    var $content = $('#person-details-general-content');
                    var d = Dashboard._lastPersonDetails;
                    // Gender dropdown
                    var genderOptions = [
                        { value: '', label: '' },
                        { value: 'Male', label: 'Male' },
                        { value: 'Female', label: 'Female' }
                    ];
                    var genderSelect = `<select class='person-detail-value' name='Gender'>`;
                    genderOptions.forEach(function(opt) {
                        genderSelect += `<option value='${opt.value}'${d.general.Gender === opt.value ? ' selected' : ''}>${opt.label}</option>`;
                    });
                    genderSelect += `</select>`;
                    // State dropdown
                    var states = ['', 'AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];
                    var stateSelect = `<select class='person-detail-value' name='State'>`;
                    states.forEach(function(st) {
                        stateSelect += `<option value='${st}'${d.general.State === st ? ' selected' : ''}>${st}</option>`;
                    });
                    stateSelect += `</select>`;
                    var editHtml = `<div class='person-details-card'><form id='edit-person-general-form'><div class='person-details-grid'>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>First Name</span><input class='person-detail-value' type='text' name='FirstName' value='${d.general.FirstName || ''}' required /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Last Name</span><input class='person-detail-value' type='text' name='LastName' value='${d.general.LastName || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Title</span><input class='person-detail-value' type='text' name='Title' value='${d.general.Title || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Gender</span>${genderSelect}</div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Email</span><input class='person-detail-value' type='email' name='Email' value='${d.general.Email || ''}' required /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Phone</span><input class='person-detail-value' type='text' name='Phone' value='${d.general.Phone || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 1</span><input class='person-detail-value' type='text' name='AddressLine1' value='${d.general.AddressLine1 || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 2</span><input class='person-detail-value' type='text' name='AddressLine2' value='${d.general.AddressLine2 || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>City</span><input class='person-detail-value' type='text' name='City' value='${d.general.City || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>State</span>${stateSelect}</div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Zip</span><input class='person-detail-value' type='text' name='Zip' value='${d.general.Zip || ''}' /></div>`;
                    editHtml += `<div class='person-detail-row'><span class='person-detail-label'>Birthday</span><input class='person-detail-value' type='date' name='Birthday' value='${d.general.Birthday ? d.general.Birthday.split('T')[0] : ''}' /></div>`;
                    editHtml += `</div><div class='edit-person-actions' style='margin-top:18px; text-align:right;'><button type='submit' class='button button-primary'>Save</button> <button type='button' class='button button-secondary' id='cancel-edit-person-general'>Cancel</button></div></form></div>`;
                    $content.html(editHtml);
                    // Cancel handler
                    $(document).off('click', '#cancel-edit-person-general').on('click', '#cancel-edit-person-general', function() {
                        Dashboard._renderPersonDetails(Dashboard._lastPersonDetails);
                    });
                    // Save handler
                    $(document).off('submit', '#edit-person-general-form').on('submit', '#edit-person-general-form', function(e) {
                        e.preventDefault();
                        var formData = $(this).serializeArray();
                        var data = { action: 'edit_person', nonce: administration_plugin.nonce, person_id: d.general.PersonID };
                        formData.forEach(function(f) {
                            if (f.name === 'FirstName') data['first_name'] = f.value;
                            else if (f.name === 'LastName') data['last_name'] = f.value;
                            else if (f.name === 'Email') data['email'] = f.value;
                            else data[f.name] = f.value;
                        });
                        $.ajax({
                            url: administration_plugin.ajax_url,
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                if (response.success) {
                                    // Reload details
                                    $.ajax({
                                        url: administration_plugin.ajax_url,
                                        type: 'POST',
                                        data: { action: 'get_full_person_details', nonce: administration_plugin.nonce, person_id: d.general.PersonID },
                                        success: function(resp) {
                                            if (resp.success && resp.data) {
                                                Dashboard._renderPersonDetails(resp.data);
                                            }
                                        }
                                    });
                                } else {
                                    alert(response.data || 'Failed to save changes.');
                                }
                            },
                            error: function() { alert('Failed to save changes.'); }
                        });
                    });
                }
            });
            Dashboard.initEditJobPostingHandlers();
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
                        
                        // Ensure people list loads every time people-content is shown
                        if (page === 'people-content' && $('.parish-content.two-column-layout').length) {
                            Dashboard.initializePeopleContent();
                        }
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
            // Initialize people-content if present
            if ($('.parish-content.two-column-layout').length) {
                Dashboard.initializePeopleContent();
            }
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
            // Also initialize job postings logic if the job postings card is present
            if ($('.job-postings-list-header').length) {
                // Default: only active
                Dashboard.loadJobPostingsList(false);
                Dashboard.setupJobPostingsHandlers();
                // Toggle handler
                $(document).off('change', '#toggle-all-job-postings').on('change', '#toggle-all-job-postings', function() {
                    Dashboard.loadJobPostingsList(this.checked);
                    $('#toggle-job-postings-label').text(this.checked ? 'Show All' : 'Show Active');
                });
                // Set initial label
                $('#toggle-job-postings-label').text($('#toggle-all-job-postings').is(':checked') ? 'Show All' : 'Show Active');
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
            console.log('Loading parish data...');
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
                        console.log('Parish list updated successfully');
                    } else {
                        console.error('Failed to load parish data:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading parish data:', error);
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

        loadJobPostingsList: function(showAll) {
            const $list = $('#job-postings-list');
            $list.html('<div class="loading">Loading job postings...</div>');
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_job_postings_list',
                    nonce: administration_plugin.nonce,
                    show_all: showAll ? 1 : 0
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        Dashboard.renderJobPostingsList(response.data);
                    } else {
                        $list.html('<div class="error-message">Failed to load job postings.</div>');
                    }
                },
                error: function() {
                    $list.html('<div class="error-message">Failed to load job postings.</div>');
                }
            });
        },

        renderJobPostingsList: function(jobs) {
            const $list = $('#job-postings-list');
            if (!jobs.length) {
                $list.html('<div class="no-data">No job postings found.</div>');
                return;
            }
            let html = '<div class="hr-admin-staff-table-wrapper"><table class="hr-admin-staff-table job-listings-table"><thead><tr><th>Job Name</th><th>Status</th></tr></thead><tbody>';
            jobs.forEach(function(job) {
                html += `<tr class="job-posting-row" data-job-posting-id="${job.JobPostingID}">` +
                    `<td>${job.Title ? Dashboard.escapeHtml(job.Title) : ''}</td>` +
                    `<td>${job.Status ? Dashboard.escapeHtml(job.Status) : ''}</td>` +
                    `</tr>`;
            });
            html += '</tbody></table></div>';
            $list.html(html);
        },

        setupJobPostingsHandlers: function() {
            // Open add job posting modal (to be implemented)
            $(document).off('click', '#add-job-posting-btn').on('click', '#add-job-posting-btn', function(e) {
                e.preventDefault();
                Dashboard.openAddJobPostingModal();
            });
            // Show job posting details modal
            $(document).off('click', '.job-posting-row').on('click', '.job-posting-row', function(e) {
                e.preventDefault();
                const jobPostingId = $(this).data('job-posting-id');
                Dashboard.showJobPostingDetails(jobPostingId);
            });
            // Go to Job Posting button (delegated)
            $(document).off('click', '.job-goto-btn').on('click', '.job-goto-btn', function(e) {
                e.preventDefault();
                const jobPostingId = $(this).data('job-posting-id');
                $('#job-posting-details-modal').removeClass('show');
                setTimeout(function() {
                    Dashboard.showJobPostingFullView(jobPostingId);
                }, 250); // Wait for modal to close for smooth transition
            });
        },

        showJobPostingDetails: function(jobPostingId) {
            // Create or select the modal
            let $modal = $('#job-posting-details-modal');
            if (!$modal.length) {
                $modal = $('<div id="job-posting-details-modal" class="modal"><div class="modal-content"><span class="close" id="close-job-posting-details-modal" tabindex="0" role="button" aria-label="Close">&times;</span><div id="job-posting-details-content"></div></div></div>');
                $('body').append($modal);
            }
            $('#job-posting-details-content').html('<div class="loading">Loading...</div>');
            $modal.addClass('show');
            // Fetch details via AJAX
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_job_posting_details',
                    nonce: administration_plugin.nonce,
                    job_posting_id: jobPostingId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Wrap the returned HTML in a styled card/grid for consistency
                        $('#job-posting-details-content').html('<div class="person-details-card">' + response.data + '</div>');
                        // Add Go to Job Posting button in a consistent footer area
                        if ($('#job-posting-details-content .job-goto-btn').length === 0) {
                            $('#job-posting-details-content').append('<div class="job-details-actions" style="text-align:center; margin-top:24px;"><button class="button job-goto-btn" data-job-posting-id="' + jobPostingId + '">Go to Job Posting</button></div>');
                        }
                    } else {
                        $('#job-posting-details-content').html('<div class="error-message">Failed to load job posting details.</div>');
                    }
                },
                error: function() {
                    $('#job-posting-details-content').html('<div class="error-message">Failed to load job posting details.</div>');
                }
            });
        },

        showJobPostingFullView: function(jobPostingId) {
            // Hide dashboard, show job posting view container
            $('.administration-public-dashboard').hide();
            let $container = $('#job-posting-view-container');
            if (!$container.length) {
                $container = $('<div id="job-posting-view-container" class="job-posting-view-container" style="display:none;"></div>');
                $('.administration-public-dashboard').parent().append($container);
            }
            $container.html('<div class="loading">Loading job posting...</div>');
            $container.show();
            // Fetch full view via AJAX
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_job_posting_full_view',
                    nonce: administration_plugin.nonce,
                    job_posting_id: jobPostingId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        $container.html(response.data);
                    } else {
                        $container.html('<div class="error-message">Failed to load job posting.</div>');
                    }
                },
                error: function() {
                    $container.html('<div class="error-message">Failed to load job posting.</div>');
                }
            });
        },

        openAddJobPostingModal: function(e) {
            // Remove any existing modal
            $('#add-job-posting-modal').remove();
            // Modal HTML
            var modalHtml = `
                <div id="add-job-posting-modal" class="modal">
                    <div class="modal-content">
                        <span class="close" id="close-add-job-posting-modal" tabindex="0" role="button" aria-label="Close">&times;</span>
                        <h2>Add New Job Posting</h2>
                        <form id="add-job-posting-form">
                            <div class="form-field">
                                <label for="job-title">Job Title</label>
                                <input type="text" id="job-title" name="title" required>
                            </div>
                            <div class="form-field">
                                <label for="job-department">Department</label>
                                <input type="text" id="job-department" name="department_name">
                            </div>
                            <div class="form-field">
                                <label for="job-type">Job Type</label>
                                <input type="text" id="job-type" name="job_type" required>
                            </div>
                            <div class="form-field">
                                <label for="job-status">Status</label>
                                <select id="job-status" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Done">Done</option>
                                    <option value="Cancelled">Cancelled</option>
                                    <option value="Backlog">Backlog</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="job-location">Location</label>
                                <input type="text" id="job-location" name="location">
                            </div>
                            <div class="form-field">
                                <label for="job-salary-range">Salary Range</label>
                                <input type="text" id="job-salary-range" name="salary_range">
                            </div>
                            <div class="form-field">
                                <label for="job-posted-date">Posted Date</label>
                                <input type="date" id="job-posted-date" name="posted_date" disabled placeholder="Auto-set">
                            </div>
                            <div class="form-field">
                                <label for="job-closing-date">Closing Date</label>
                                <input type="date" id="job-closing-date" name="closing_date">
                            </div>
                            <div class="form-field">
                                <label for="job-program-id">Program</label>
                                <select id="job-program-id" name="program_id">
                                    <option value="">-- None --</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="job-reports-to">Reports To</label>
                                <select id="job-reports-to" name="reports_to">
                                    <option value="">-- None --</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="job-is-internal">Internal Posting</label>
                                <select id="job-is-internal" name="is_internal">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="job-description">Description</label>
                                <textarea id="job-description" name="description"></textarea>
                            </div>
                            <div class="form-field">
                                <label for="job-requirements">Requirements</label>
                                <textarea id="job-requirements" name="requirements"></textarea>
                            </div>
                            <div class="form-field">
                                <label for="job-responsibilities">Responsibilities</label>
                                <textarea id="job-responsibilities" name="responsibilities"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Save Job Posting</button>
                                <button type="button" class="button" id="cancel-add-job-posting">Cancel</button>
                            </div>
                        </form>
                        <div id="add-job-posting-message"></div>
                    </div>
                </div>
            `;
            $('body').append(modalHtml);
            $('#add-job-posting-modal').addClass('show');
            // Populate Program select
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'get_programs_for_select', nonce: administration_plugin.nonce },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        var options = '<option value="">-- None --</option>';
                        response.data.forEach(function(program) {
                            options += `<option value="${program.ProgramID}">${Dashboard.escapeHtml(program.ProgramName)}</option>`;
                        });
                        $('#job-program-id').html(options);
                    }
                }
            });
            // Populate Reports To select
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'get_people_for_owner_select', nonce: administration_plugin.nonce },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        var options = '<option value="">-- None --</option>';
                        response.data.forEach(function(person) {
                            options += `<option value="${person.PersonID}">${Dashboard.escapeHtml(person.FirstName + ' ' + person.LastName)}</option>`;
                        });
                        $('#job-reports-to').html(options);
                    }
                }
            });
            // Close handlers
            $(document).off('click', '#close-add-job-posting-modal, #cancel-add-job-posting').on('click', '#close-add-job-posting-modal, #cancel-add-job-posting', function(e) {
                e.preventDefault();
                $('#add-job-posting-modal').removeClass('show');
                setTimeout(function() { $('#add-job-posting-modal').remove(); }, 300);
            });
            // Submit handler
            $(document).off('submit', '#add-job-posting-form').on('submit', '#add-job-posting-form', Dashboard.submitAddJobPostingForm);
        },

        submitAddJobPostingForm: function(e) {
            e.preventDefault();
            var $form = $(this);
            var $message = $('#add-job-posting-message');
            // Gather form data
            var formData = $form.serializeArray();
            var data = { action: 'add_job_posting', nonce: administration_plugin.nonce };
            formData.forEach(function(field) { data[field.name] = field.value; });
            // Validate required fields
            if (!data.title || !data.job_type || !data.status) {
                $message.html('<span class="error-message">Job Title, Job Type, and Status are required.</span>');
                return;
            }
            $message.html('<span class="loading">Saving job posting...</span>');
            // AJAX submit
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $message.html('<span class="success-message">Job posting added successfully!</span>');
                        setTimeout(function() {
                            $('#add-job-posting-modal').removeClass('show');
                            $form[0].reset();
                            $message.html('');
                            Dashboard.loadJobPostingsList();
                        }, 800);
                    } else {
                        $message.html('<span class="error-message">' + (response.data || 'Error saving job posting.') + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    $message.html('<span class="error-message">Error saving job posting. Please try again.</span>');
                }
            });
        },

        escapeHtml: function(text) {
            return $('<div>').text(text).html();
        },

        openAddProgramModal: function(e) {
            e.preventDefault();
            var $modal = $('#add-program-modal');
            var $form = $('#add-program-form');
            
            // Reset form if it exists
            if ($form.length) {
                $form[0].reset();
            }
            
            // Clear any previous messages
            $('#add-program-message').html('');
            
            // Show modal
            $modal.addClass('show');
            
            // Load people for owner select
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_people_for_owner_select',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        var ownerOptions = '<option value="">Select Owner</option>';
                        response.data.forEach(function(person) {
                            ownerOptions += `<option value="${person.PersonID}">${person.FirstName} ${person.LastName}</option>`;
                        });
                        $('#program-owner').html(ownerOptions);
                    }
                }
            });
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
            var $message = $('#add-program-message');
            
            // Get form values
            var programName = $('#program-name').val().trim();
            var programType = $('#program-type').val().trim();
            var programOwner = $('#program-owner').val().trim();
            var description = $('#program-description').val().trim();
            var startDate = $('#program-start-date').val();
            var endDate = $('#program-end-date').val();
            var status = $('#program-status').val();
            
            // Validate required fields
            if (!programName || !programType || !programOwner) {
                $message.html('<span class="error-message">Program name, type, and owner are required.</span>');
                return;
            }
            
            // Show loading message
            $message.html('<span class="loading">Saving program...</span>');
            
            // Prepare data
            var data = {
                action: 'add_program',
                nonce: administration_plugin.nonce,
                program_name: programName,
                program_type: programType,
                program_owner: programOwner,
                description: description,
                start_date: startDate,
                end_date: endDate,
                status: status
            };
            
            // Send AJAX request
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    console.log('Program add response:', response);
                    if (response.success) {
                        $message.html('<span class="success-message">Program added successfully!</span>');
                        setTimeout(function() {
                            $('#add-program-modal').removeClass('show');
                            $form[0].reset();
                            $message.html('');
                            Dashboard.loadPage('programs');
                        }, 800);
                    } else {
                        $message.html('<span class="error-message">' + (response.data || 'Error saving program.') + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Program add error:', {xhr, status, error});
                    $message.html('<span class="error-message">Error saving program. Please try again.</span>');
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
            console.log('Handling sync users click...');
            e.preventDefault();
            e.stopPropagation();
            
            var $btn = $(this);
            console.log('Sync button found:', $btn.length, 'Button HTML:', $btn.prop('outerHTML'));
            
            if (!$btn.length) {
                console.error('Sync button not found!');
                return;
            }
            
            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Syncing...');
            
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'administration_force_sync_users',
                    nonce: administration_plugin.nonce
                },
                success: function(response) {
                    console.log('Sync response:', response);
                    if (response.success) {
                        $btn.html('<span class="dashicons dashicons-yes"></span> Synced!');
                        setTimeout(function() {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                            // Reload the people list after sync
                            Dashboard.loadPeopleList();
                        }, 1500);
                    } else {
                        console.error('Sync failed:', response.data);
                        $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                        setTimeout(function() {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Sync AJAX error:', {xhr, status, error});
                    $btn.html('<span class="dashicons dashicons-warning"></span> Error');
                    setTimeout(function() {
                        $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync');
                    }, 2000);
                }
            });
        },

        // Sort logic
        handleSortPeopleClick: function(e) {
            e.preventDefault();
            var sortBy = $(this).text().toLowerCase().replace(' ', '_');
            Dashboard.currentPeopleSort = sortBy;
            $('.sort-dropdown').hide();
            Dashboard.loadPeopleList($('#people-content-filter-input').val(), sortBy);
            $('#sort-people-btn').html('Sort by <span class="dashicons dashicons-arrow-down"></span>');
        },
        loadPeopleList: function(search, sort) {
            var $container = $('.people-list-content');
            if (!$container.length) return; // Guard against missing container
            
            $container.html('<div class="loading">Loading people...</div>');
            
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_people_list',
                    nonce: administration_plugin.nonce,
                    search: search || '',
                    sort: sort || Dashboard.currentPeopleSort || 'name_asc' // Default sort if none specified
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data);
                    } else {
                        $container.html('<div class="error-message">Failed to load people.</div>');
                    }
                },
                error: function() {
                    $container.html('<div class="error-message">Failed to load people.</div>');
                }
            });
        },

        initializePeopleContent: function() {
            // Load people list immediately
            Dashboard.loadPeopleList();
            
            // Set up filter handler
            $(document).off('input', '#people-content-filter-input').on('input', '#people-content-filter-input', Dashboard.debouncedPeopleFilter);
            
            // Set up sort handler
            $(document).off('click', '#sort-people-btn').on('click', '#sort-people-btn', function(e) {
                e.preventDefault();
                $(this).siblings('.sort-dropdown').toggle();
            });
        },
        debouncedPeopleFilter: function() {
            clearTimeout(Dashboard.peopleFilterTimeout);
            Dashboard.peopleFilterTimeout = setTimeout(function() {
                var search = $('#people-content-filter-input').val();
                Dashboard.loadPeopleList(search);
            }, 250);
        },

        // Store last details for editing
        _renderPersonDetails: function(d) {
            Dashboard._lastPersonDetails = d;
            // General
            var generalHtml = '';
            generalHtml += `<div class='person-details-card'>`;
            generalHtml += `<div class='person-details-grid'>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Person ID</span><span class='person-detail-value'>${d.general.PersonID || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>First Name</span><span class='person-detail-value'>${d.general.FirstName || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Last Name</span><span class='person-detail-value'>${d.general.LastName || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Title</span><span class='person-detail-value'>${d.general.Title || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Gender</span><span class='person-detail-value'>${d.general.Gender || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Email</span><span class='person-detail-value'>${d.general.Email || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Phone</span><span class='person-detail-value'>${d.general.Phone || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 1</span><span class='person-detail-value'>${d.general.AddressLine1 || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 2</span><span class='person-detail-value'>${d.general.AddressLine2 || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>City</span><span class='person-detail-value'>${d.general.City || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>State</span><span class='person-detail-value'>${d.general.State || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Zip</span><span class='person-detail-value'>${d.general.Zip || ''}</span></div>`;
            generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Birthday</span><span class='person-detail-value'>${d.general.Birthday || ''}</span></div>`;
            generalHtml += `</div>`;
            generalHtml += `</div>`;
            $('#person-details-general-content').html(generalHtml);
            // Relationships
            Dashboard._renderRelationshipsSection(d);
            // Roles
            var rolesHtml = '';
            rolesHtml += `<div class='person-details-card'><div class='person-details-grid'>`;
            if (d.roles && d.roles.length) {
                d.roles.forEach(function(role) {
                    rolesHtml += `<div class='person-detail-row'><span class='person-detail-label'>${role.ProgramName}</span><span class='person-detail-value'>${role.RoleName}</span></div>`;
                });
            } else {
                rolesHtml += `<div class='person-detail-row'><span class='person-detail-label'>Roles</span><span class='person-detail-value'>No roles found.</span></div>`;
            }
            rolesHtml += `</div></div>`;
            $('#person-details-roles-content').html(rolesHtml);
            // Ensure relationship edit handlers are initialized
            Dashboard.initRelationshipsEditHandlers();
        },

        _renderRelationshipsSection: function(d) {
            var $section = $('.person-details-family');
            var $header = $section.find('.person-details-section-header h3');
            $header.text('Relationships');
            var $content = $('#person-details-family-content');
            var isEdit = $section.hasClass('edit-mode');
            if (!isEdit) {
                // View mode
                if (d.relationships && d.relationships.length > 0) {
                    var relHtml = `<div class='person-details-card'><div class='person-details-grid'>`;
                    d.relationships.forEach(function(rel) {
                        relHtml += `<div class='person-detail-row'><span class='person-detail-label'>${rel.RelationshipType}</span><span class='person-detail-value'>${rel.RelatedPersonName}</span></div>`;
                    });
                    relHtml += `</div></div>`;
                    $content.html(relHtml);
                } else {
                    $content.html('');
                }
                // Show edit button
                $section.find('.person-details-edit-btn').show();
            } else {
                // Edit mode
                var rels = d.relationships || [];
                var relHtml = `<form id='edit-relationships-form'><div class='person-details-card relationships-edit-table' id='edit-relationships-rows'>`;
                rels.forEach(function(rel, idx) {
                    relHtml += Dashboard._relationshipEditRow(rel, idx);
                });
                relHtml += `</div>`;
                relHtml += `<div class='edit-person-actions' style='margin-top:18px; text-align:right;'>`;
                relHtml += `<button type='button' class='add-relationship-btn' id='add-relationship-btn'>+ Add Relationship</button> `;
                relHtml += `<button type='submit' class='button button-primary'>Save</button> `;
                relHtml += `<button type='button' class='button button-secondary' id='cancel-edit-relationships'>Cancel</button>`;
                relHtml += `</div></form>`;
                $content.html(relHtml);
                // Hide edit button
                $section.find('.person-details-edit-btn').hide();
            }
        },

        _relationshipEditRow: function(rel, idx) {
            // rel: {RelatedPersonID, RelatedPersonName, RelationshipType}
            var types = ['Mother','Father','Child','Sibling','Other'];
            var typeOptions = types.map(function(type) {
                return `<option value='${type}'${rel.RelationshipType === type ? ' selected' : ''}>${type}</option>`;
            }).join('');
            return `<div class='relationship-edit-row' data-idx='${idx}'>
                <div class='relationship-person-col'>
                    <input type='hidden' name='RelatedPersonID' value='${rel.RelatedPersonID}'>
                    <input class='relationship-person-input' type='text' name='RelatedPersonName' value='${rel.RelatedPersonName || ''}' autocomplete='off' placeholder='Search person...'>
                    <ul class='relationships-typeahead-list' style='display:none;'></ul>
                </div>
                <div class='relationship-type-col'>
                    <select class='relationship-type-select' name='RelationshipType'>${typeOptions}</select>
                </div>
                <div class='relationship-actions-col'>
                    <button type='button' class='delete-relationship-btn' title='Delete'>&#128465;</button>
                </div>
            </div>`;
        },

        // Event handlers for Relationships edit mode
        initRelationshipsEditHandlers: function() {
            // Edit button
            $(document).off('click', '.person-details-family .person-details-edit-btn').on('click', '.person-details-family .person-details-edit-btn', function() {
                var d = Dashboard._lastPersonDetails;
                $('.person-details-family').addClass('edit-mode');
                Dashboard._renderRelationshipsSection(d);
            });
            // Cancel button
            $(document).off('click', '#cancel-edit-relationships').on('click', '#cancel-edit-relationships', function() {
                $('.person-details-family').removeClass('edit-mode');
                Dashboard._renderRelationshipsSection(Dashboard._lastPersonDetails);
            });
            // Add relationship
            $(document).off('click', '#add-relationship-btn').on('click', '#add-relationship-btn', function() {
                var $rows = $('#edit-relationships-rows');
                var idx = $rows.children('.relationship-edit-row').length;
                var rel = {RelatedPersonID:'', RelatedPersonName:'', RelationshipType:'Mother'};
                $rows.append(Dashboard._relationshipEditRow(rel, idx));
            });
            // Delete relationship
            $(document).off('click', '.delete-relationship-btn').on('click', '.delete-relationship-btn', function() {
                $(this).closest('.relationship-edit-row').remove();
            });
            // Typeahead for person search
            $(document).off('input', '.relationship-person-input').on('input', '.relationship-person-input', function() {
                var $input = $(this);
                var query = $input.val();
                var $row = $input.closest('.relationship-edit-row');
                var excludeId = Dashboard._lastPersonDetails.general.PersonID;
                var $list = $row.find('.relationships-typeahead-list');
                $list.hide().empty();
                if (query.length < 2) return;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'search_people',
                        nonce: administration_plugin.nonce,
                        q: query,
                        exclude_id: excludeId
                    },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            response.data.forEach(function(person) {
                                $list.append('<li data-id="' + person.PersonID + '">' + person.Name + ' <span style="color:#888;font-size:0.95em;">(' + person.Email + ')</span></li>');
                            });
                            $list.show();
                        }
                    }
                });
            });
            // Select from typeahead
            $(document).off('click', '.relationships-typeahead-list li').on('click', '.relationships-typeahead-list li', function() {
                var $li = $(this);
                var $row = $li.closest('.relationship-edit-row');
                $row.find('.relationship-person-input').val($li.text().replace(/\s*\(.+\)$/, ''));
                $row.find('input[name="RelatedPersonID"]').val($li.data('id'));
                $row.find('.relationships-typeahead-list').hide();
            });
            // Hide typeahead on blur
            $(document).off('blur', '.relationship-person-input').on('blur', '.relationship-person-input', function() {
                var $input = $(this);
                setTimeout(function() {
                    $input.closest('.relationship-edit-row').find('.relationships-typeahead-list').hide();
                }, 200);
            });
            // Save relationships
            $(document).off('submit', '#edit-relationships-form').on('submit', '#edit-relationships-form', function(e) {
                e.preventDefault();
                var $rows = $('#edit-relationships-rows .relationship-edit-row');
                var relationships = [];
                $rows.each(function() {
                    var $row = $(this);
                    var rel = {
                        RelationshipID: $row.find('input[name="RelationshipID"]').val() || '',
                        RelatedPersonID: $row.find('input[name="RelatedPersonID"]').val() || '',
                        RelationshipType: $row.find('select[name="RelationshipType"]').val() || ''
                    };
                    // Only include if RelatedPersonID and RelationshipType are present
                    if (rel.RelatedPersonID && rel.RelationshipType) {
                        relationships.push(rel);
                    }
                });
                var personId = Dashboard._lastPersonDetails.general.PersonID;
                var $form = $(this);
                var $actions = $form.find('.edit-person-actions');
                $actions.append('<span class="loading" id="relationships-saving-msg">Saving...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'save_person_relationships',
                        nonce: administration_plugin.nonce,
                        person_id: personId,
                        relationships: relationships
                    },
                    success: function(response) {
                        $('#relationships-saving-msg').remove();
                        if (response.success) {
                            $('.person-details-family').removeClass('edit-mode');
                            // Reload details
                            $.ajax({
                                url: administration_plugin.ajax_url,
                                type: 'POST',
                                data: { action: 'get_full_person_details', nonce: administration_plugin.nonce, person_id: personId },
                                success: function(resp) {
                                    if (resp.success && resp.data) {
                                        Dashboard._renderPersonDetails(resp.data);
                                    }
                                }
                            });
                        } else {
                            $actions.append('<span class="error-message">Failed to save relationships.</span>');
                        }
                    },
                    error: function() {
                        $('#relationships-saving-msg').remove();
                        $actions.append('<span class="error-message">Failed to save relationships.</span>');
                    }
                });
            });
        },

        openAddPersonModal: function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $modal = $('#add-person-modal');
            var $form = $('#add-person-form');
            
            // Reset form if it exists
            if ($form.length) {
                $form[0].reset();
            }
            
            // Clear any previous messages
            $('#add-person-message').html('');
            
            // Show modal
            $modal.addClass('show');
        },

        closeAddPersonModal: function(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            var $modal = $('#add-person-modal');
            var $form = $('#add-person-form');
            
            $modal.removeClass('show');
            if ($form.length) {
                $form[0].reset();
            }
            $('#add-person-message').html('');
        },

        submitAddPersonForm: function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $form = $(this);
            var $message = $('#add-person-message');
            
            // Get form values
            var formData = $form.serializeArray();
            var data = {
                action: 'add_person',
                nonce: administration_plugin.nonce
            };
            
            // Convert form data to object
            formData.forEach(function(field) {
                data[field.name] = field.value;
            });
            
            // Show loading message
            $message.html('<span class="loading">Saving person...</span>');
            
            // Send AJAX request
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $message.html('<span class="success-message">Person added successfully!</span>');
                        setTimeout(function() {
                            Dashboard.closeAddPersonModal();
                            Dashboard.loadPeopleList();
                        }, 800);
                    } else {
                        $message.html('<span class="error-message">' + (response.data || 'Error saving person.') + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Person add error:', {xhr, status, error});
                    $message.html('<span class="error-message">Error saving person. Please try again.</span>');
                }
            });
        },

        backToDashboard: function() {
            $('#job-posting-view-container').hide();
            $('.administration-public-dashboard').show();
        },

        initEditJobPostingHandlers: function() {
            $(document).off('click', '#edit-job-posting-btn').on('click', '#edit-job-posting-btn', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.person-details-card');
                var $container = $('.job-posting-full-view');
                var jobId = $(this).data('job-posting-id') || $container.data('job-posting-id');
                // Gather current values from the DOM and data attributes
                var title = $card.find('h2').text().replace(/^Job Posting:\s*/, '').trim();
                var status = $card.find('.person-detail-label:contains("Status")').next('.person-detail-value').text().trim();
                var department = $card.find('.person-detail-label:contains("Department")').next('.person-detail-value').text().trim();
                var jobType = $card.find('.person-detail-label:contains("Job Type")').next('.person-detail-value').text().trim();
                var location = $card.find('.person-detail-label:contains("Location")').next('.person-detail-value').text().trim();
                var isInternal = $card.find('.person-detail-label:contains("Internal?")').next('.person-detail-value').text().trim() === 'Yes' ? '1' : '0';
                var postedDate = $card.find('.person-detail-label:contains("Posted Date")').next('.person-detail-value').text().trim();
                var closingDate = $card.find('.person-detail-label:contains("Closing Date")').next('.person-detail-value').text().trim();
                var programId = $card.find('.person-detail-label:contains("Program")').next('.person-detail-value').data('program-id') || $container.data('program-id') || '';
                var reportsToId = $card.find('.person-detail-label:contains("Reports To")').next('.person-detail-value').data('person-id') || $container.data('reports-to-id') || '';
                var programType = $card.find('.person-detail-label:contains("Program Type")').next('.person-detail-value').text().trim();
                var description = $card.find('.person-detail-label:contains("Description")').parent().find('.person-detail-value').text().trim();
                var requirements = $card.find('.person-detail-label:contains("Requirements")').parent().find('.person-detail-value').text().trim();
                var responsibilities = $card.find('.person-detail-label:contains("Responsibilities")').parent().find('.person-detail-value').text().trim();
                // Format dates for input[type=date]
                var formatDate = function(dateStr) {
                    if (!dateStr) return '';
                    var d = new Date(dateStr);
                    if (isNaN(d)) return dateStr;
                    return d.toISOString().slice(0,10);
                };
                postedDate = formatDate(postedDate);
                closingDate = formatDate(closingDate);
                // Build edit form HTML
                var statusOptions = ['Active','Done','Cancelled','Backlog'].map(function(opt) {
                    return `<option value="${opt}"${status === opt ? ' selected' : ''}>${opt}</option>`;
                }).join('');
                var internalOptions = `<option value="0"${isInternal==='0'?' selected':''}>No</option><option value="1"${isInternal==='1'?' selected':''}>Yes</option>`;
                var programSelect = `<select id="edit-job-program-id" name="program_id" class="person-detail-value">`+
                    `<option value="">Loading...</option></select>`;
                var reportsToSelect = `<select id="edit-job-reports-to" name="reports_to" class="person-detail-value">`+
                    `<option value="">Loading...</option></select>`;
                var editHtml = `<form id="edit-job-posting-form"><div class="job-posting-full-view" data-job-posting-id="${jobId}"><div class="job-posting-details-two-col">
                    <div class="job-posting-details-left">
                        <div class="job-posting-details-left-inner">
                            <div class="person-detail-row job-detail-status" style="grid-column: 1 / span 2;">
                                <span class="person-detail-label">Status</span>
                                <select class="person-detail-value" name="status">${statusOptions}</select>
                            </div>
                            <div class="job-posting-details-left-col">
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Job Title</span><input class="person-detail-value" type="text" name="title" value="${Dashboard.escapeHtml(title)}" required></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Department</span><input class="person-detail-value" type="text" name="department_name" value="${Dashboard.escapeHtml(department)}"></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Job Type</span><input class="person-detail-value" type="text" name="job_type" value="${Dashboard.escapeHtml(jobType)}"></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Location</span><input class="person-detail-value" type="text" name="location" value="${Dashboard.escapeHtml(location)}"></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Internal?</span><select class="person-detail-value" name="is_internal">${internalOptions}</select></div>
                            </div>
                            <div class="job-posting-details-left-col">
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Posted Date</span><input class="person-detail-value" type="date" name="posted_date" value="${postedDate}" disabled></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Closing Date</span><input class="person-detail-value" type="date" name="closing_date" value="${closingDate}"></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Program</span>${programSelect}</div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Program Type</span><input class="person-detail-value" type="text" name="program_type" value="${Dashboard.escapeHtml(programType)}" disabled></div>
                                <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Reports To</span>${reportsToSelect}</div>
                            </div>
                        </div>
                    </div>
                    <div class="job-posting-details-right">
                        <div class="person-detail-row job-detail-long"><span class="person-detail-label">Description</span><textarea class="person-detail-value job-detail-long-value" name="description">${Dashboard.escapeHtml(description)}</textarea></div>
                        <div class="person-detail-row job-detail-long"><span class="person-detail-label">Requirements</span><textarea class="person-detail-value job-detail-long-value" name="requirements">${Dashboard.escapeHtml(requirements)}</textarea></div>
                        <div class="person-detail-row job-detail-long"><span class="person-detail-label">Responsibilities</span><textarea class="person-detail-value job-detail-long-value" name="responsibilities">${Dashboard.escapeHtml(responsibilities)}</textarea></div>
                    </div>
                </div>
                <div class="edit-job-actions" style="margin-top:24px; display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
                    <button type="submit" class="button button-primary save-job-posting-btn">Save</button>
                    <button type="button" class="button button-secondary cancel-job-posting-btn">Cancel</button>
                    <button type="button" class="button button-danger delete-job-posting-btn">Delete Job Posting</button>
                </div>
                <div id="edit-job-posting-message"></div>
                </form>`;
                $container.html(editHtml);
                // Populate Program select
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_programs_for_select', nonce: administration_plugin.nonce },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            var options = '<option value="">-- None --</option>';
                            response.data.forEach(function(program) {
                                options += `<option value="${program.ProgramID}"${program.ProgramID==programId?' selected':''}>${Dashboard.escapeHtml(program.ProgramName)}</option>`;
                            });
                            $('#edit-job-program-id').html(options);
                        }
                    }
                });
                // Populate Reports To select
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_people_for_owner_select', nonce: administration_plugin.nonce },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            var options = '<option value="">-- None --</option>';
                            response.data.forEach(function(person) {
                                var fullName = person.FirstName + ' ' + person.LastName;
                                options += `<option value="${person.PersonID}"${person.PersonID==reportsToId?' selected':''}>${Dashboard.escapeHtml(fullName)}</option>`;
                            });
                            $('#edit-job-reports-to').html(options);
                        }
                    }
                });
            });
            // Add submit handler for edit job posting form
            $(document).off('submit', '#edit-job-posting-form').on('submit', '#edit-job-posting-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $container = $('.job-posting-full-view');
                var jobId = $container.data('job-posting-id');
                var formData = $form.serializeArray();
                var data = { action: 'edit_job_posting', nonce: administration_plugin.nonce, job_posting_id: jobId };
                formData.forEach(function(field) { data[field.name] = field.value; });
                var $msg = $('#edit-job-posting-message');
                $msg.html('<span class="loading">Saving changes...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $msg.html('<span class="success-message">Job posting updated!</span>');
                            setTimeout(function() {
                                // Reload the full view
                                Dashboard.showJobPostingFullView(jobId);
                            }, 700);
                        } else {
                            $msg.html('<span class="error-message">' + (response.data || 'Failed to update job posting.') + '</span>');
                        }
                    },
                    error: function() {
                        $msg.html('<span class="error-message">Failed to update job posting.</span>');
                    }
                });
            });
        },

        // Applicant card click handler
        renderApplicantDetailsPanel: function(data) {
            var statusOptions = [
                { value: 'New', label: 'New' },
                { value: 'Interview(s) Scheduled', label: 'Interview(s) Scheduled' },
                { value: 'Pending Decision', label: 'Pending Decision' },
                { value: 'Decision Made', label: 'Decision Made' }
            ];
            var html = '<div class="job-applicant-details-card">';
            html += '<div class="job-applicant-details-name">' +
                (data.Applicant && data.Applicant.FirstName ? data.Applicant.FirstName : '') + ' ' +
                (data.Applicant && data.Applicant.LastName ? data.Applicant.LastName : '') + '</div>';
            html += '<div class="job-applicant-details-meta">';
            if (data.Applicant && data.Applicant.Email) html += '<span class="job-applicant-details-email">' + data.Applicant.Email + '</span>';
            if (data.Applicant && data.Applicant.Phone) html += '<span class="job-applicant-details-phone">' + data.Applicant.Phone + '</span>';
            html += '</div>';
            html += '<div class="job-applicant-details-divider"></div>';
            html += '<div class="job-applicant-details-status-row">';
            html += '<span class="job-applicant-details-status-label">Status:</span>';
            html += '<select class="job-applicant-details-status-select" data-application-id="' + data.ApplicationID + '">';
            statusOptions.forEach(function(opt) {
                html += '<option value="' + opt.value + '"' + (data.Status === opt.value ? ' selected' : '') + '>' + opt.label + '</option>';
            });
            html += '</select>';
            html += '<span class="status-updated-message" style="display:none;">Status Updated!</span>';
            html += '</div>';
            html += '<div class="job-applicant-details-row"><span class="job-applicant-details-label">Applied:</span> <span>' + (data.SubmissionDate ? data.SubmissionDate.split(' ')[0] : '-') + '</span></div>';
            if (data.ResumeURL) {
                html += '<div class="job-applicant-details-row"><span class="job-applicant-details-label">Resume:</span> <a href="' + data.ResumeURL + '" target="_blank" class="job-applicant-details-link">View Resume</a></div>';
            }
            if (data.CoverLetterURL) {
                html += '<div class="job-applicant-details-row"><span class="job-applicant-details-label">Cover Letter:</span> <a href="' + data.CoverLetterURL + '" target="_blank" class="job-applicant-details-link">View Cover Letter</a></div>';
            }
            html += '</div>';
            // Notes card
            html += '<div class="job-applicant-notes-card">';
            html += '<div class="job-applicant-notes-label">Notes</div>';
            html += '<textarea class="job-applicant-notes-textarea" rows="4" placeholder="Add notes...">' + (data.Notes ? data.Notes : '') + '</textarea>';
            html += '<button class="button button-primary job-applicant-notes-save-btn" data-application-id="' + data.ApplicationID + '">Save</button>';
            html += '<span class="notes-updated-message" style="display:none;">Notes Updated!</span>';
            html += '</div>';
            $('.job-applicant-details-panel').html(html).show();

            // Attach change handler for status dropdown
            $('.job-applicant-details-status-select').off('change').on('change', function() {
                var $select = $(this);
                var newStatus = $select.val();
                var applicationId = $select.data('application-id');
                var $msg = $select.closest('.job-applicant-details-status-row').find('.status-updated-message');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_job_applicant_status',
                        nonce: administration_plugin.nonce,
                        application_id: applicationId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            $msg.stop(true, true).fadeIn(120).removeClass('hide');
                            setTimeout(function() {
                                $msg.addClass('hide');
                                setTimeout(function() { $msg.hide(); }, 400);
                            }, 1500);
                        } else {
                            alert(response.data || 'Failed to update status.');
                        }
                    },
                    error: function() {
                        alert('Failed to update status.');
                    }
                });
            });
            // Attach save handler for notes
            $('.job-applicant-notes-save-btn').off('click').on('click', function() {
                var $btn = $(this);
                var applicationId = $btn.data('application-id');
                var $textarea = $btn.siblings('.job-applicant-notes-textarea');
                var notes = $textarea.val();
                var $msg = $btn.siblings('.notes-updated-message');
                $btn.prop('disabled', true).text('Saving...');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_job_applicant_notes',
                        nonce: administration_plugin.nonce,
                        application_id: applicationId,
                        notes: notes
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).text('Save');
                        if (response.success) {
                            $msg.stop(true, true).fadeIn(120).removeClass('hide');
                            setTimeout(function() {
                                $msg.addClass('hide');
                                setTimeout(function() { $msg.hide(); }, 400);
                            }, 1500);
                        } else {
                            alert(response.data || 'Failed to update notes.');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).text('Save');
                        alert('Failed to update notes.');
                    }
                });
            });
        },

        // --- Course Assignments Tab Logic ---
        loadCourseAssignments: function(courseId) {
            var $listGrid = $('.course-assignments-list-grid');
            var $detailsPanel = $('.course-assignment-details-panel');
            $listGrid.html('<div class="loading">Loading assignments...</div>');
            $detailsPanel.hide().empty();
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_course_assignments',
                    nonce: administration_plugin.nonce,
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.data && response.data.length) {
                        var html = '';
                        response.data.forEach(function(a) {
                            html += '<div class="course-assignment-row" data-assignment-id="' + a.AssignmentID + '">';
                            html += '<div class="course-assignment-title">' + Dashboard.escapeHtml(a.Title) + '</div>';
                            html += '<div class="course-assignment-meta">';
                            if (a.DueDate) html += '<span class="course-assignment-duedate">Due: ' + a.DueDate + '</span>';
                            if (a.MaxScore) html += '<span class="course-assignment-maxscore">Max: ' + a.MaxScore + '</span>';
                            html += '</div></div>';
                        });
                        $listGrid.html(html);
                    } else {
                        $listGrid.html('<div class="course-assignment-empty">No assignments yet.</div>');
                    }
                },
                error: function() {
                    $listGrid.html('<div class="error-message">Failed to load assignments.</div>');
                }
            });
        },

        renderAssignmentDetailsPanel: function(data) {
            var html = '';
            html += '<div class="assignment-details-title" style="font-size:1.32rem;font-weight:700;color:#2271b1;margin-bottom:8px;line-height:1.2;margin-top:24px;">' + Dashboard.escapeHtml(data.Title) + '</div>';
            html += '<div class="assignment-details-meta" style="display:flex;gap:18px;font-size:1.01rem;color:#6a7a8c;margin-bottom:16px;">';
            if (data.DueDate) html += '<span class="assignment-details-duedate" style="display:inline-block;">Due: <b>' + data.DueDate + '</b></span>';
            if (data.MaxScore) html += '<span class="assignment-details-maxscore" style="display:inline-block;">Max: <b>' + data.MaxScore + '</b></span>';
            html += '</div>';
            if (data.Description) html += '<div class="assignment-details-desc" style="font-size:1.04rem;color:#1d2327;margin-bottom:22px;white-space:pre-line;">' + Dashboard.escapeHtml(data.Description) + '</div>';
            html += '<div class="assignment-details-actions" style="display:flex;gap:10px;margin-top:10px;">';
            html += '<button class="button button-secondary button-sm edit-assignment-btn" data-assignment-id="' + data.AssignmentID + '">Edit</button>';
            html += '<button class="button button-danger button-sm delete-assignment-btn" data-assignment-id="' + data.AssignmentID + '">Delete</button>';
            html += '</div>';
            $('.course-assignment-details-panel').html(html).addClass('active').show();
        },

        // Add Assignment Modal
        showAddAssignmentModal: function(courseId) {
            var modalHtml = '<div class="modal assignment-modal" id="add-assignment-modal">';
            modalHtml += '<div class="modal-content" style="max-width: 480px;">';
            modalHtml += '<button class="close" id="close-add-assignment-modal">&times;</button>';
            modalHtml += '<h2>Add Assignment</h2>';
            modalHtml += '<form id="add-assignment-form">';
            modalHtml += '<input type="hidden" name="course_id" value="' + courseId + '">';
            modalHtml += '<div class="form-field"><label>Title <span class="required">*</span></label><input type="text" name="title" required maxlength="150"></div>';
            modalHtml += '<div class="form-field"><label>Description</label><textarea name="description" rows="3"></textarea></div>';
            modalHtml += '<div class="form-field"><label>Due Date</label><input type="date" name="due_date"></div>';
            modalHtml += '<div class="form-field"><label>Max Score</label><input type="number" name="max_score" min="0" step="0.01"></div>';
            modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">Add Assignment</button></div>';
            modalHtml += '<div class="form-message" style="display:none;"></div>';
            modalHtml += '</form></div></div>';
            $('body').append(modalHtml);
            setTimeout(function() { $('#add-assignment-modal').addClass('show'); }, 10);
        },

        escapeHtml: function(str) {
            return String(str).replace(/[&<>\"]/g, function(s) {
                return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;'})[s];
            });
        },

        // Delete Assignment
        deleteAssignment: function(assignmentId) {
            if (!confirm('Are you sure you want to delete this assignment?')) return;
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_assignment',
                    nonce: administration_plugin.nonce,
                    assignment_id: assignmentId
                },
                success: function(response) {
                    if (response.success) {
                        Dashboard.loadCourseAssignments($('.course-detail-tab-content').data('course-id'));
                        $('.course-assignment-details-panel').removeClass('active').hide();
                    } else {
                        alert(response.data || 'Failed to delete assignment.');
                    }
                },
                error: function() {
                    alert('Failed to delete assignment.');
                }
            });
        },

        // Edit Assignment
        showEditAssignmentModal: function(data) {
            var modalHtml = '<div class="modal assignment-modal" id="edit-assignment-modal">';
            modalHtml += '<div class="modal-content" style="max-width: 480px;">';
            modalHtml += '<button class="close" id="close-edit-assignment-modal">&times;</button>';
            modalHtml += '<h2>Edit Assignment</h2>';
            modalHtml += '<form id="edit-assignment-form">';
            modalHtml += '<input type="hidden" name="assignment_id" value="' + data.AssignmentID + '">';
            modalHtml += '<div class="form-field"><label>Title <span class="required">*</span></label><input type="text" name="title" required maxlength="150" value="' + Dashboard.escapeHtml(data.Title) + '"></div>';
            modalHtml += '<div class="form-field"><label>Description</label><textarea name="description" rows="3">' + (data.Description ? Dashboard.escapeHtml(data.Description) : '') + '</textarea></div>';
            modalHtml += '<div class="form-field"><label>Due Date</label><input type="date" name="due_date" value="' + (data.DueDate ? data.DueDate : '') + '"></div>';
            modalHtml += '<div class="form-field"><label>Max Score</label><input type="number" name="max_score" min="0" step="0.01" value="' + (data.MaxScore ? data.MaxScore : '') + '"></div>';
            modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">Save Changes</button></div>';
            modalHtml += '<div class="form-message" style="display:none;"></div>';
            modalHtml += '</form></div></div>';
            $('body').append(modalHtml);
            setTimeout(function() { $('#edit-assignment-modal').addClass('show'); }, 10);
        },

        // Edit Assignment
        editAssignment: function(assignmentId, data) {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'edit_assignment',
                    nonce: administration_plugin.nonce,
                    assignment_id: assignmentId,
                    title: data.Title,
                    description: data.Description,
                    due_date: data.DueDate,
                    max_score: data.MaxScore
                },
                success: function(response) {
                    if (response.success) {
                        Dashboard.loadCourseAssignments($('.course-detail-tab-content').data('course-id'));
                        $('.course-assignment-details-panel').removeClass('active').hide();
                    } else {
                        alert(response.data || 'Failed to update assignment.');
                    }
                },
                error: function() {
                    alert('Failed to update assignment.');
                }
            });
        },

        // --- Course Grades Tab Logic ---
        loadCourseGradesAssignments: function(courseId) {
            var $listGrid = $('.course-grades-assignments-list-grid');
            var $detailsPanel = $('.course-grades-details-panel');
            $listGrid.html('<div class="loading">Loading assignments...</div>');
            $detailsPanel.removeClass('active').hide().empty();
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_course_assignments',
                    nonce: administration_plugin.nonce,
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.data && response.data.length) {
                        var html = '';
                        response.data.forEach(function(a) {
                            html += '<div class="course-grades-assignment-row" data-assignment-id="' + a.AssignmentID + '">';
                            html += '<div class="course-grades-assignment-title">' + Dashboard.escapeHtml(a.Title) + '</div>';
                            html += '<div class="course-grades-assignment-meta">';
                            if (a.DueDate) html += '<span class="course-grades-assignment-duedate">Due: ' + a.DueDate + '</span>';
                            if (a.MaxScore) html += '<span class="course-grades-assignment-maxscore">Max: ' + a.MaxScore + '</span>';
                            html += '</div></div>';
                        });
                        $listGrid.html(html);
                    } else {
                        $listGrid.html('<div class="course-grades-assignment-empty">No assignments yet.</div>');
                    }
                },
                error: function() {
                    $listGrid.html('<div class="error-message">Failed to load assignments.</div>');
                }
            });
        },

        renderAssignmentGradesPanel: function(assignmentId, grades) {
            var html = '<div class="assignment-grades-list-table grades-card-ui">';
            html += '<div class="assignment-grades-list-header" style="display:flex;gap:18px;font-weight:600;color:#2271b1;margin-bottom:8px;">';
            html += '<div style="flex:2;">Student</div>';
            html += '<div style="flex:1;">Score</div>';
            html += '<div style="flex:2;">Feedback</div>';
            html += '<div style="width:80px;"></div>';
            html += '</div>';
            if (grades.length) {
                grades.forEach(function(g, i) {
                    var striped = (i % 2 === 1) ? ' grades-striped-row' : '';
                    html += '<div class="assignment-grades-list-row' + striped + '" style="display:flex;gap:18px;align-items:center;margin-bottom:0;min-height:38px;">';
                    html += '<div style="flex:2;font-size:1.08em;">' + Dashboard.escapeHtml((g.FirstName||'') + ' ' + (g.LastName||'')) + '</div>';
                    html += '<div style="flex:1;font-size:1.08em;">' + (g.Score !== null ? g.Score : '-') + '</div>';
                    html += '<div style="flex:2;font-size:1.08em;">' + (g.Feedback ? Dashboard.escapeHtml(g.Feedback) : '-') + '</div>';
                    html += '<div style="width:80px;display:flex;gap:6px;justify-content:flex-end;">';
                    html += '<button class="button button-secondary button-xs edit-grade-btn" data-grade-id="' + g.GradeID + '" title="Edit">&#9998;</button>';
                    html += '<button class="button button-danger button-xs delete-grade-btn" data-grade-id="' + g.GradeID + '" title="Delete">&#128465;</button>';
                    html += '</div>';
                    html += '</div>';
                });
            } else {
                html += '<div class="assignment-grades-list-empty" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 0;">No grades yet.</div>';
            }
            html += '</div>';
            $('.course-grades-details-panel').html(html).addClass('active').show();
        },

        // Add Grade Modal
        showAddGradeModal: function(courseId) {
            // Fetch assignments and students for dropdowns
            $.when(
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_course_assignments', nonce: administration_plugin.nonce, course_id: courseId }
                }),
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_course_students', nonce: administration_plugin.nonce, course_id: courseId }
                })
            ).done(function(assignmentsResp, studentsResp) {
                var assignments = (assignmentsResp[0].success && assignmentsResp[0].data) ? assignmentsResp[0].data : [];
                var students = (studentsResp[0].success && studentsResp[0].data) ? studentsResp[0].data : [];
                var modalHtml = '<div class="modal grade-modal" id="add-grade-modal">';
                modalHtml += '<div class="modal-content" style="max-width: 480px;">';
                modalHtml += '<button class="close" id="close-add-grade-modal">&times;</button>';
                modalHtml += '<h2>Add Grade</h2>';
                modalHtml += '<form id="add-grade-form">';
                modalHtml += '<div class="form-field"><label>Assignment <span class="required">*</span></label><select name="assignment_id" required>';
                assignments.forEach(function(a) {
                    modalHtml += '<option value="' + a.AssignmentID + '">' + Dashboard.escapeHtml(a.Title) + '</option>';
                });
                modalHtml += '</select></div>';
                modalHtml += '<div class="form-field"><label>Student <span class="required">*</span></label><select name="person_id" required>';
                students.forEach(function(s) {
                    modalHtml += '<option value="' + s.PersonID + '">' + Dashboard.escapeHtml(s.FirstName + ' ' + s.LastName) + '</option>';
                });
                modalHtml += '</select></div>';
                modalHtml += '<div class="form-field"><label>Score <span class="required">*</span></label><input type="number" name="score" min="0" step="0.01" required></div>';
                modalHtml += '<div class="form-field"><label>Feedback</label><textarea name="feedback" rows="2"></textarea></div>';
                modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">Add Grade</button></div>';
                modalHtml += '<div class="form-message" style="display:none;"></div>';
                modalHtml += '</form></div></div>';
                $('body').append(modalHtml);
                setTimeout(function() { $('#add-grade-modal').addClass('show'); }, 10);
            });
        },

        // Edit Grade Modal
        showEditGradeModal: function(grade) {
            var modalHtml = '<div class="modal grade-modal" id="edit-grade-modal">';
            modalHtml += '<div class="modal-content" style="max-width: 480px;">';
            modalHtml += '<button class="close" id="close-edit-grade-modal">&times;</button>';
            modalHtml += '<h2>Edit Grade</h2>';
            modalHtml += '<form id="edit-grade-form">';
            modalHtml += '<input type="hidden" name="grade_id" value="' + grade.GradeID + '">';
            modalHtml += '<div class="form-field"><label>Student</label><input type="text" value="' + Dashboard.escapeHtml((grade.FirstName||'') + ' ' + (grade.LastName||'')) + '" disabled></div>';
            modalHtml += '<div class="form-field"><label>Score <span class="required">*</span></label><input type="number" name="score" min="0" step="0.01" required value="' + (grade.Score !== null ? grade.Score : '') + '"></div>';
            modalHtml += '<div class="form-field"><label>Feedback</label><textarea name="feedback" rows="2">' + (grade.Feedback ? Dashboard.escapeHtml(grade.Feedback) : '') + '</textarea></div>';
            modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">Save Changes</button></div>';
            modalHtml += '<div class="form-message" style="display:none;"></div>';
            modalHtml += '</form></div></div>';
            $('body').append(modalHtml);
            setTimeout(function() { $('#edit-grade-modal').addClass('show'); }, 10);
        },

        // Delete Grade
        deleteGrade: function(gradeId) {
            if (!confirm('Are you sure you want to delete this grade?')) return;
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'delete_grade', nonce: administration_plugin.nonce, grade_id: gradeId },
                success: function(response) {
                    if (response.success) {
                        // Reload grades for the selected assignment if visible
                        var selectedAssignment = $('.course-grades-assignment-row.selected').data('assignment-id');
                        if (selectedAssignment) {
                            $('.course-grades-assignment-row.selected').click();
                        }
                    } else {
                        alert(response.data || 'Failed to delete grade.');
                    }
                },
                error: function() {
                    alert('Failed to delete grade.');
                }
            });
        },

        // Edit Grade
        editGrade: function(gradeId, data) {
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'edit_grade',
                    nonce: administration_plugin.nonce,
                    grade_id: gradeId,
                    score: data.Score,
                    feedback: data.Feedback
                },
                success: function(response) {
                    if (response.success) {
                        // Reload grades for the selected assignment if visible
                        var selectedAssignment = $('.course-grades-assignment-row.selected').data('assignment-id');
                        if (selectedAssignment) {
                            $('.course-grades-assignment-row.selected').click();
                        }
                    } else {
                        alert(response.data || 'Failed to update grade.');
                    }
                },
                error: function() {
                    alert('Failed to update grade.');
                }
            });
        },

        // --- Curriculum Tab Logic ---
        loadCurriculum: function(courseId) {
            var $listGrid = $('.curriculum-list-grid');
            var $detailsPanel = $('.curriculum-details-panel');
            $listGrid.html('<div class="loading">Loading curriculum...</div>');
            $detailsPanel.hide().empty();
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_curriculum',
                    nonce: administration_plugin.nonce,
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.data.length) {
                        var html = '';
                        response.data.forEach(function(row) {
                            html += '<tr data-curriculum-id="' + row.CurriculumID + '">';
                            html += '<td style="padding:10px 16px;">' + row.WeekNumber + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Objective || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Materials || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.VideoLinks || '') + '</td>';
                            html += '<td style="padding:10px 16px;text-align:right;">';
                            html += '<button class="button button-secondary button-xs edit-curriculum-btn" data-curriculum-id="' + row.CurriculumID + '">&#9998;</button> ';
                            html += '<button class="button button-danger button-xs delete-curriculum-btn" data-curriculum-id="' + row.CurriculumID + '">&#128465;</button>';
                            html += '</td></tr>';
                        });
                        $listGrid.html(html);
                    } else {
                        $listGrid.html('<tr class="curriculum-empty-row"><td colspan="5" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">No curriculum yet.</td></tr>');
                    }
                },
                error: function() {
                    $listGrid.html('<tr><td colspan="5" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">Failed to load curriculum.</td></tr>');
                }
            });
        },

        // Add/Edit/Delete handlers (modals to be implemented)
        addCurriculum: function() {
            // Show modal for add (to be implemented)
            alert('Add Week modal coming soon!');
        },

        editCurriculum: function(curriculumId) {
            // Show modal for edit (to be implemented)
            alert('Edit Week modal coming soon!');
        },

        deleteCurriculum: function(curriculumId) {
            var id = $(this).data('curriculum-id');
            if (!confirm('Delete this week?')) return;
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'delete_curriculum', nonce: administration_plugin.nonce, curriculum_id: id },
                success: function(response) {
                    if (response.success) loadCurriculum();
                    else $msg.html('<span class="error-message">Failed to delete.</span>');
                },
                error: function() { $msg.html('<span class="error-message">Failed to delete.</span>'); }
            });
        },

        // --- Lesson Plan Tab Logic ---
        loadLessonPlans: function(courseId) {
            var $listGrid = $('.lessonplan-list-grid');
            var $detailsPanel = $('.lessonplan-details-panel');
            $listGrid.html('<div class="loading">Loading lesson plans...</div>');
            $detailsPanel.hide().empty();
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_lessonplans',
                    nonce: administration_plugin.nonce,
                    course_id: courseId
                },
                success: function(response) {
                    if (response.success && response.data.length) {
                        var html = '';
                        response.data.forEach(function(row) {
                            html += '<tr data-lessonplan-id="' + row.LessonPlanID + '">';
                            html += '<td style="padding:10px 16px;">' + (row.Date || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Title || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Description || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Materials || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.VideoLinks || '') + '</td>';
                            html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Notes || '') + '</td>';
                            html += '<td style="padding:10px 16px;text-align:right;">';
                            html += '<button class="button button-secondary button-xs edit-lessonplan-btn" data-lessonplan-id="' + row.LessonPlanID + '">&#9998;</button> ';
                            html += '<button class="button button-danger button-xs delete-lessonplan-btn" data-lessonplan-id="' + row.LessonPlanID + '">&#128465;</button>';
                            html += '</td></tr>';
                        });
                        $listGrid.html(html);
                    } else {
                        $listGrid.html('<tr class="lessonplan-empty-row"><td colspan="7" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">No lesson plans yet.</td></tr>');
                    }
                },
                error: function() {
                    $listGrid.html('<tr><td colspan="7" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">Failed to load lesson plans.</td></tr>');
                }
            });
        },

        addLessonPlan: function() {
            // Show modal for add (to be implemented)
            alert('Add Lesson modal coming soon!');
        },

        editLessonPlan: function(lessonPlanId) {
            // Show modal for edit (to be implemented)
            alert('Edit Lesson modal coming soon!');
        },

        deleteLessonPlan: function(lessonPlanId) {
            var id = $(this).data('lessonplan-id');
            if (!confirm('Delete this lesson?')) return;
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'delete_lessonplan', nonce: administration_plugin.nonce, lessonplan_id: id },
                success: function(response) {
                    if (response.success) loadLessonPlans();
                    else $msg.html('<span class="error-message">Failed to delete.</span>');
                },
                error: function() { $msg.html('<span class="error-message">Failed to delete.</span>'); }
            });
        }
    };

    // Initialize dashboard when document is ready
    $(document).ready(function() {
        Dashboard.init();
        // Ensure program view container exists
        if ($('.program-view-container').length === 0) {
            $('.administration-public-dashboard').after('<div class="program-view-container" id="program-view-container" style="display:none;"></div>');
        }
        // Delegate Go to Program button click
        $(document).on('click', '.program-goto-btn', function(e) {
            e.preventDefault();
            // Try to get programId from data attribute on the button
            var programId = $(this).data('program-id');
            // If not found, try to get from the modal content (h3 title or parent container)
            if (!programId) {
                programId = $(this).closest('.program-details-modal-content').data('program-id');
            }
            // If still not found, try to get from a hidden field or fallback
            if (!programId) {
                // Try to get from a hidden field or fallback logic if needed
                var $editBtn = $(this).closest('.program-details-modal-content').find('.edit-button');
                if ($editBtn.length) {
                    programId = $editBtn.data('program-id');
                } else {
                    // Try to get from the modal's parent card
                    programId = $(this).closest('.program-card').data('program-id');
                }
            }
            if (programId) {
                $('#program-details-modal').removeClass('show');
                $('.administration-public-dashboard').hide();
                ProgramView.show(programId);
            } else {
                alert('Could not determine program ID.');
            }
        });

        // Add trace logging for staff row modal
        console.log('[HR Modal] Attaching .staff-row click handler (delegated)');
        $(document).on('click', '.staff-row', function(e) {
            e.preventDefault();
            const personId = $(this).data('person-id');
            console.log('[HR Modal] .staff-row clicked. personId:', personId, this);
            const $staffModal = $('#staff-details-modal');
            const $staffDetailsContent = $('#staff-details-general-content');
            $staffDetailsContent.html('<div class="loading">Loading...</div>');
            $staffModal.addClass('show');
            console.log('[HR Modal] Modal should now be visible.');
            // Fetch details via AJAX
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_full_person_details',
                    nonce: administration_plugin.nonce,
                    person_id: personId
                },
                success: function(response) {
                    if (response.success && response.data && response.data.general) {
                        // Render general details in the same style as people-content
                        var d = response.data;
                        var generalHtml = '';
                        generalHtml += `<div class='person-details-card'>`;
                        generalHtml += `<div class='person-details-grid'>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Person ID</span><span class='person-detail-value'>${d.general.PersonID || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>First Name</span><span class='person-detail-value'>${d.general.FirstName || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Last Name</span><span class='person-detail-value'>${d.general.LastName || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Title</span><span class='person-detail-value'>${d.general.Title || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Gender</span><span class='person-detail-value'>${d.general.Gender || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Email</span><span class='person-detail-value'>${d.general.Email || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Phone</span><span class='person-detail-value'>${d.general.Phone || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 1</span><span class='person-detail-value'>${d.general.AddressLine1 || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Address Line 2</span><span class='person-detail-value'>${d.general.AddressLine2 || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>City</span><span class='person-detail-value'>${d.general.City || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>State</span><span class='person-detail-value'>${d.general.State || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Zip</span><span class='person-detail-value'>${d.general.Zip || ''}</span></div>`;
                        generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>Birthday</span><span class='person-detail-value'>${d.general.Birthday || ''}</span></div>`;
                        generalHtml += `</div>`;
                        generalHtml += `</div>`;

                        // Add job roles section if present
                        if (d.roles && d.roles.length) {
                            generalHtml += `<div class='person-details-section' style='margin-top:24px;'>`;
                            generalHtml += `<div class='person-details-section-header'><h3>Job Roles</h3></div>`;
                            generalHtml += `<div class='person-details-card'><div class='person-details-grid'>`;
                            d.roles.forEach(function(role) {
                                generalHtml += `<div class='person-detail-row'><span class='person-detail-label'>${role.ProgramName || 'Program'}</span><span class='person-detail-value'>${role.RoleTitle || role.RoleName || ''}</span></div>`;
                            });
                            generalHtml += `</div></div></div>`;
                        }

                        $staffDetailsContent.html(generalHtml);
                    } else {
                        $staffDetailsContent.html('<div class="error-message">Failed to load staff details.</div>');
                    }
                },
                error: function() {
                    $staffDetailsContent.html('<div class="error-message">Failed to load staff details.</div>');
                }
            });
        });

        // Modal close button
        $(document).on('click', '#close-staff-details-modal', function(e) {
            e.preventDefault();
            $('#staff-details-modal').removeClass('show');
            console.log('[HR Modal] Modal closed via close button.');
        });
        // Optional: Close modal on outside click
        $(window).on('click', function(event) {
            const $modal = $('#staff-details-modal');
            if (event.target === $modal[0]) {
                $modal.removeClass('show');
                console.log('[HR Modal] Modal closed via outside click.');
            }
        });

        // Back to Dashboard button for job posting view
        $(document).on('click', '#back-to-dashboard-btn', function(e) {
            e.preventDefault();
            Dashboard.backToDashboard();
        });

        // Applicant card click handler
        $(document).on('click', '.job-applicant-card', function(e) {
            e.preventDefault();
            var $card = $(this);
            var applicationId = $card.data('application-id');
            console.log('[Applicant] Card clicked. applicationId:', applicationId);
            if (!applicationId) return;
            $('.job-applicant-card').removeClass('selected');
            $card.addClass('selected');
            var $panel = $('.job-applicant-details-panel');
            $panel.html('<div class="loading" style="padding: 32px 0 0 18px; color: #2271b1;">Loading applicant details...</div>').show();
            console.log('[Applicant] Sending AJAX for get_job_applicant_details', applicationId);
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_job_applicant_details',
                    nonce: administration_plugin.nonce,
                    application_id: applicationId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        Dashboard.renderApplicantDetailsPanel(response.data);
                    } else {
                        $panel.html('<div class="error-message">Failed to load applicant details.</div>');
                    }
                },
                error: function() {
                    $panel.html('<div class="error-message">Failed to load applicant details.</div>');
                }
            });
        });

        $(document).on('click', '.tab-button[data-tab="assignments"]', function() {
            var $tabContent = $(this).closest('.course-detail-tabs').next('.course-detail-tab-content');
            var courseId = $tabContent.data('course-id');
            Dashboard.loadCourseAssignments(courseId);
        });

        $(document).on('click', '#close-add-assignment-modal, #add-assignment-modal', function(e) {
            if ($(e.target).is('#close-add-assignment-modal') || $(e.target).is('#add-assignment-modal')) {
                $('#add-assignment-modal').removeClass('show');
                setTimeout(function() { $('#add-assignment-modal').remove(); }, 200);
            }
        });
        $(document).on('click', '.modal-content', function(e) { e.stopPropagation(); });

        $(document).on('submit', '#add-assignment-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (!valid) {
                $msg.text('Please fill in all required fields.').addClass('error').show();
                return;
            }
            $btn.prop('disabled', true).text('Adding...');
            var formData = $form.serializeArray();
            var data = { action: 'add_assignment', nonce: administration_plugin.nonce };
            formData.forEach(function(f) { data[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $btn.prop('disabled', false).text('Add Assignment');
                    if (response.success) {
                        $msg.text('Assignment added!').addClass('success').show();
                        setTimeout(function() {
                            $('#add-assignment-modal').removeClass('show');
                            setTimeout(function() { $('#add-assignment-modal').remove(); }, 200);
                            Dashboard.loadCourseAssignments(data.course_id);
                        }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to add assignment.').addClass('error').show();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Add Assignment');
                    $msg.text('Failed to add assignment.').addClass('error').show();
                }
            });
        });

        // Replace old add-assignment-btn event with new class
        $(document).off('click', '.add-course-assignment-btn').on('click', '.add-course-assignment-btn', function() {
            var $tabContent = $(this).closest('.tab-pane');
            var courseId = $tabContent.closest('.course-detail-tab-content').data('course-id');
            Dashboard.showAddAssignmentModal(courseId);
        });

        // Assignment search filter
        $(document).off('input', '.course-detail-assignments-search').on('input', '.course-detail-assignments-search', function() {
            var search = $(this).val().toLowerCase();
            $('.course-assignments-list-grid .course-assignment-row').each(function() {
                var title = $(this).find('.course-assignment-title').text().toLowerCase();
                var meta = $(this).find('.course-assignment-meta').text().toLowerCase();
                if (title.includes(search) || meta.includes(search)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // Show/hide empty message
            var visible = $('.course-assignments-list-grid .course-assignment-row:visible').length;
            if (visible === 0) {
                if ($('.course-assignments-list-grid .course-assignment-empty').length === 0) {
                    $('.course-assignments-list-grid').append('<div class="course-assignment-empty">No assignments found.</div>');
                }
            } else {
                $('.course-assignments-list-grid .course-assignment-empty').remove();
            }
        });

        // Delete Assignment
        $(document).off('click', '.delete-assignment-btn').on('click', '.delete-assignment-btn', function() {
            var assignmentId = $(this).data('assignment-id');
            if (!confirm('Are you sure you want to delete this assignment?')) return;
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'delete_assignment',
                    nonce: administration_plugin.nonce,
                    assignment_id: assignmentId
                },
                success: function(response) {
                    if (response.success) {
                        Dashboard.loadCourseAssignments($('.course-detail-tab-content').data('course-id'));
                        $('.course-assignment-details-panel').removeClass('active').hide();
                    } else {
                        alert(response.data || 'Failed to delete assignment.');
                    }
                },
                error: function() {
                    alert('Failed to delete assignment.');
                }
            });
        });

        // Edit Assignment
        $(document).off('click', '.edit-assignment-btn').on('click', '.edit-assignment-btn', function() {
            var assignmentId = $(this).data('assignment-id');
            // Fetch details for editing
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_assignment_details',
                    nonce: administration_plugin.nonce,
                    assignment_id: assignmentId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        Dashboard.showEditAssignmentModal(response.data);
                    } else {
                        alert('Failed to load assignment details.');
                    }
                },
                error: function() {
                    alert('Failed to load assignment details.');
                }
            });
        });

        // Edit Assignment
        $(document).off('submit', '#edit-assignment-form').on('submit', '#edit-assignment-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (!valid) {
                $msg.text('Please fill in all required fields.').addClass('error').show();
                return;
            }
            $btn.prop('disabled', true).text('Saving...');
            var formData = $form.serializeArray();
            var data = { action: 'edit_assignment', nonce: administration_plugin.nonce };
            formData.forEach(function(f) { data[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $btn.prop('disabled', false).text('Save Changes');
                    if (response.success) {
                        $msg.text('Assignment updated!').addClass('success').show();
                        setTimeout(function() {
                            $('#edit-assignment-modal').removeClass('show');
                            setTimeout(function() { $('#edit-assignment-modal').remove(); }, 200);
                            Dashboard.loadCourseAssignments($('.course-detail-tab-content').data('course-id'));
                            $('.course-assignment-details-panel').removeClass('active').hide();
                        }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to update assignment.').addClass('error').show();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Save Changes');
                    $msg.text('Failed to update assignment.').addClass('error').show();
                }
            });
        });

        $(document).on('click', '.tab-button[data-tab="grades"]', function() {
            var $tabContent = $(this).closest('.course-detail-tabs').next('.course-detail-tab-content');
            var courseId = $tabContent.data('course-id');
            Dashboard.loadCourseGradesAssignments(courseId);
        });

        $(document).on('click', '#close-add-grade-modal, #add-grade-modal', function(e) {
            if ($(e.target).is('#close-add-grade-modal') || $(e.target).is('#add-grade-modal')) {
                $('#add-grade-modal').removeClass('show');
                setTimeout(function() { $('#add-grade-modal').remove(); }, 200);
            }
        });
        $(document).on('click', '.modal-content', function(e) { e.stopPropagation(); });

        $(document).on('submit', '#add-grade-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (!valid) {
                $msg.text('Please fill in all required fields.').addClass('error').show();
                return;
            }
            $btn.prop('disabled', true).text('Adding...');
            var formData = $form.serializeArray();
            var data = { action: 'add_grade', nonce: administration_plugin.nonce };
            formData.forEach(function(f) { data[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $btn.prop('disabled', false).text('Add Grade');
                    if (response.success) {
                        $msg.text('Grade added!').addClass('success').show();
                        setTimeout(function() {
                            $('#add-grade-modal').removeClass('show');
                            setTimeout(function() { $('#add-grade-modal').remove(); }, 200);
                            // Reload grades for the selected assignment if visible
                            var selectedAssignment = $('.course-grades-assignment-row.selected').data('assignment-id');
                            if (selectedAssignment) {
                                $('.course-grades-assignment-row.selected').click();
                            }
                        }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to add grade.').addClass('error').show();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Add Grade');
                    $msg.text('Failed to add grade.').addClass('error').show();
                }
            });
        });

        // Edit Grade Modal
        $(document).on('click', '.edit-grade-btn', function() {
            var gradeId = $(this).data('grade-id');
            // Find the assignmentId from the selected assignment
            var assignmentId = $('.course-grades-assignment-row.selected').data('assignment-id');
            // Fetch grade details (reuse get_assignment_grades and filter client-side)
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: { action: 'get_assignment_grades', nonce: administration_plugin.nonce, assignment_id: assignmentId },
                success: function(response) {
                    if (response.success && response.data) {
                        var grade = response.data.find(function(g) { return g.GradeID === gradeId; });
                        if (grade) {
                            Dashboard.showEditGradeModal(grade);
                        } else {
                            alert('Grade not found.');
                        }
                    } else {
                        alert('Failed to load grade details.');
                    }
                },
                error: function() {
                    alert('Failed to load grade details.');
                }
            });
        });

        $(document).on('submit', '#edit-grade-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (!valid) {
                $msg.text('Please fill in all required fields.').addClass('error').show();
                return;
            }
            $btn.prop('disabled', true).text('Saving...');
            var formData = $form.serializeArray();
            var data = { action: 'edit_grade', nonce: administration_plugin.nonce };
            formData.forEach(function(f) { data[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    $btn.prop('disabled', false).text('Save Changes');
                    if (response.success) {
                        $msg.text('Grade updated!').addClass('success').show();
                        setTimeout(function() {
                            $('#edit-grade-modal').removeClass('show');
                            setTimeout(function() { $('#edit-grade-modal').remove(); }, 200);
                            // Reload grades for the selected assignment if visible
                            var selectedAssignment = $('.course-grades-assignment-row.selected').data('assignment-id');
                            if (selectedAssignment) {
                                $('.course-grades-assignment-row.selected').click();
                            }
                        }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to update grade.').addClass('error').show();
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Save Changes');
                    $msg.text('Failed to update grade.').addClass('error').show();
                }
            });
        });

        $(document).on('click', '.course-grades-assignment-row', function() {
            $('.course-grades-assignment-row').removeClass('selected');
            $(this).addClass('selected');
            var assignmentId = $(this).data('assignment-id');
            var $panel = $('.course-grades-details-panel');
            $panel.html('<div class="loading">Loading grades...</div>').addClass('active').show();
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_assignment_grades',
                    nonce: administration_plugin.nonce,
                    assignment_id: assignmentId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        Dashboard.renderAssignmentGradesPanel(assignmentId, response.data);
                    } else {
                        $panel.html('<div class="error-message">Failed to load grades.</div>');
                    }
                },
                error: function() {
                    $panel.html('<div class="error-message">Failed to load grades.</div>');
                }
            });
        });

        $(document).off('click', '.add-course-grade-btn').on('click', '.add-course-grade-btn', function() {
            var $tabContent = $(this).closest('.tab-pane');
            var courseId = $tabContent.closest('.course-detail-tab-content').data('course-id');
            Dashboard.showAddGradeModal(courseId);
        });

        // Grades tab assignment search filter
        $(document).off('input', '.course-detail-grades-search').on('input', '.course-detail-grades-search', function() {
            var search = $(this).val().toLowerCase();
            var $rows = $('.course-grades-assignments-list-grid .course-grades-assignment-row');
            $rows.each(function() {
                var title = $(this).find('.course-grades-assignment-title').text().toLowerCase();
                var meta = $(this).find('.course-grades-assignment-meta').text().toLowerCase();
                if (title.includes(search) || meta.includes(search)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // Show/hide empty message
            var visible = $rows.filter(':visible').length;
            if (visible === 0) {
                if ($('.course-grades-assignments-list-grid .course-grades-assignment-empty').length === 0) {
                    $('.course-grades-assignments-list-grid').append('<div class="course-grades-assignment-empty">No assignments found.</div>');
                }
            } else {
                $('.course-grades-assignments-list-grid .course-grades-assignment-empty').remove();
            }
        });

        // Attendance tab logic
        $(document).on('click', '.tab-button[data-tab="attendance"]', function() {
            var $tabContent = $(this).closest('.course-detail-tabs').next('.course-detail-tab-content');
            var courseId = $tabContent.data('course-id');
            var $attendanceTab = $tabContent.find('#attendance');
            var $dateInput = $attendanceTab.find('#attendance-date');
            var $searchInput = $attendanceTab.find('.course-detail-attendance-search');
            var $listGrid = $attendanceTab.find('.attendance-list-grid');
            var $emptyMsg = $attendanceTab.find('.attendance-list-empty');
            var $saveBtn = $attendanceTab.find('.save-attendance-btn');
            var $msg = $attendanceTab.find('.attendance-save-message');
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var dd = String(today.getDate()).padStart(2, '0');
            var defaultDate = yyyy + '-' + mm + '-' + dd;
            if (!$dateInput.val()) $dateInput.val(defaultDate);
            var attendanceState = { original: [], checked: [], students: [] };
            function renderList() {
                var search = $searchInput.val().toLowerCase();
                var html = '';
                var visibleCount = 0;
                attendanceState.students.forEach(function(s, i) {
                    var name = (s.FirstName + ' ' + s.LastName).toLowerCase();
                    if (!search || name.includes(search)) {
                        visibleCount++;
                        var checked = attendanceState.checked.includes(s.PersonID) ? 'checked' : '';
                        var striped = (visibleCount % 2 === 0) ? ' grades-striped-row' : '';
                        html += '<div class="attendance-row' + striped + '" style="display:flex;align-items:center;gap:0;padding:8px 18px;font-size:1.05em;min-height:38px;">';
                        html += '<span style="flex:1 1 0;overflow:visible;white-space:nowrap;font-weight:500;margin-right:32px;">' + Dashboard.escapeHtml(s.FirstName + ' ' + s.LastName) + '</span>';
                        html += '<div style="flex:0 0 32px;display:flex;justify-content:center;align-items:center;">';
                        html += '<input type="checkbox" class="attendance-checkbox" data-person-id="' + s.PersonID + '" ' + checked + ' />';
                        html += '</div>';
                        html += '</div>';
                    }
                });
                $listGrid.html(html);
                $emptyMsg.toggle(visibleCount === 0);
            }
            function loadAttendance() {
                $listGrid.html('<div class="loading" style="padding:24px 0 0 18px;">Loading...</div>');
                $emptyMsg.hide();
                $msg.html('');
                $saveBtn.prop('disabled', true);
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_course_attendance',
                        nonce: administration_plugin.nonce,
                        course_id: courseId,
                        session_date: $dateInput.val()
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            attendanceState.students = response.data.students;
                            attendanceState.original = response.data.attendance.slice();
                            attendanceState.checked = response.data.attendance.slice();
                            renderList();
                        } else {
                            $listGrid.html('<div class="error-message">Failed to load attendance.</div>');
                        }
                    },
                    error: function() {
                        $listGrid.html('<div class="error-message">Failed to load attendance.</div>');
                    }
                });
            }
            // Initial load
            loadAttendance();
            // Date change
            $dateInput.off('change').on('change', function() {
                loadAttendance();
            });
            // Search filter
            $searchInput.off('input').on('input', function() {
                renderList();
            });
            // Checkbox change (delegated)
            $listGrid.off('change', '.attendance-checkbox').on('change', '.attendance-checkbox', function() {
                var personId = $(this).data('person-id');
                if ($(this).is(':checked')) {
                    if (!attendanceState.checked.includes(personId)) attendanceState.checked.push(personId);
                } else {
                    attendanceState.checked = attendanceState.checked.filter(function(id) { return id !== personId; });
                }
                // Enable save if changed
                var changed = attendanceState.checked.sort().join(',') !== attendanceState.original.sort().join(',');
                $saveBtn.prop('disabled', !changed);
            });
            // Save button
            $saveBtn.off('click').on('click', function() {
                $saveBtn.prop('disabled', true).text('Saving...');
                $msg.html('');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'save_course_attendance',
                        nonce: administration_plugin.nonce,
                        course_id: courseId,
                        session_date: $dateInput.val(),
                        present_person_ids: attendanceState.checked
                    },
                    success: function(response) {
                        $saveBtn.text('Save');
                        if (response.success) {
                            attendanceState.original = attendanceState.checked.slice();
                            $msg.html('<span class="success-message">Attendance saved!</span>');
                        } else {
                            $msg.html('<span class="error-message">' + (response.data || 'Failed to save attendance.') + '</span>');
                        }
                        $saveBtn.prop('disabled', true);
                    },
                    error: function() {
                        $saveBtn.text('Save');
                        $msg.html('<span class="error-message">Failed to save attendance.</span>');
                        $saveBtn.prop('disabled', false);
                    }
                });
            });
        });

        // --- Curriculum Tab Logic ---
        $(document).on('click', '.tab-button[data-tab="curriculum"]', function() {
            var $tabContent = $(this).closest('.course-detail-tabs').next('.course-detail-tab-content');
            var courseId = $tabContent.data('course-id');
            var $tab = $tabContent.find('#curriculum');
            var $tableBody = $tab.find('.curriculum-table-body');
            var $msg = $tab.find('.curriculum-message');
            var $addBtn = $tab.find('.add-curriculum-btn');
            function reload() { loadCurriculum(); }
            function loadCurriculum() {
                $tableBody.html('<tr><td colspan="5" style="padding:18px 0 0 18px;">Loading...</td></tr>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_curriculum', nonce: administration_plugin.nonce, course_id: courseId },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            var html = '';
                            response.data.forEach(function(row) {
                                html += '<tr data-curriculum-id="' + row.CurriculumID + '">';
                                html += '<td style="padding:10px 16px;">' + row.WeekNumber + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Objective || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Materials || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.VideoLinks || '') + '</td>';
                                html += '<td style="padding:10px 16px;text-align:right;">';
                                html += '<button class="button button-secondary button-xs edit-curriculum-btn" data-curriculum-id="' + row.CurriculumID + '">&#9998;</button> ';
                                html += '<button class="button button-danger button-xs delete-curriculum-btn" data-curriculum-id="' + row.CurriculumID + '">&#128465;</button>';
                                html += '</td></tr>';
                            });
                            $tableBody.html(html);
                        } else {
                            $tableBody.html('<tr class="curriculum-empty-row"><td colspan="5" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">No curriculum yet.</td></tr>');
                        }
                    },
                    error: function() {
                        $tableBody.html('<tr><td colspan="5" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">Failed to load curriculum.</td></tr>');
                    }
                });
            }
            $addBtn.off('click').on('click', function() {
                showCurriculumModal('add', { CourseID: courseId }, reload);
            });
            $tableBody.off('click', '.edit-curriculum-btn').on('click', '.edit-curriculum-btn', function() {
                var id = $(this).data('curriculum-id');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_curriculum', nonce: administration_plugin.nonce, course_id: courseId },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            var row = response.data.find(function(r) { return r.CurriculumID === id; });
                            if (row) showCurriculumModal('edit', row, reload);
                        }
                    }
                });
            });
            $tableBody.off('click', '.delete-curriculum-btn').on('click', '.delete-curriculum-btn', function() {
                var id = $(this).data('curriculum-id');
                if (!confirm('Delete this week?')) return;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'delete_curriculum', nonce: administration_plugin.nonce, curriculum_id: id },
                    success: function(response) {
                        if (response.success) loadCurriculum();
                        else $msg.html('<span class="error-message">Failed to delete.</span>');
                    },
                    error: function() { $msg.html('<span class="error-message">Failed to delete.</span>'); }
                });
            });
            loadCurriculum();
        });

        // --- Lesson Plan Tab Logic ---
        $(document).on('click', '.tab-button[data-tab="lessonplan"]', function() {
            var $tabContent = $(this).closest('.course-detail-tabs').next('.course-detail-tab-content');
            var courseId = $tabContent.data('course-id');
            var $tab = $tabContent.find('#lessonplan');
            var $tableBody = $tab.find('.lessonplan-table-body');
            var $msg = $tab.find('.lessonplan-message');
            var $addBtn = $tab.find('.add-lessonplan-btn');
            var $weekFilter = $tab.find('.lessonplan-week-filter');
            var weekOptions = [];
            function loadWeeks(cb) {
                // Populate week filter from curriculum
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_curriculum', nonce: administration_plugin.nonce, course_id: courseId },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            weekOptions = response.data.map(function(row) { return { value: row.WeekNumber }; });
                            var html = '<option value="">All Weeks</option>';
                            weekOptions.forEach(function(opt) { html += '<option value="' + opt.value + '">Week ' + opt.value + '</option>'; });
                            $weekFilter.html(html);
                        } else {
                            weekOptions = [];
                            $weekFilter.html('<option value="">All Weeks</option>');
                        }
                        if (cb) cb();
                    }
                });
            }
            function loadLessonPlans() {
                $tableBody.html('<tr><td colspan="7" style="padding:18px 0 0 18px;">Loading...</td></tr>');
                var week = $weekFilter.val();
                var data = { action: 'get_lessonplans', nonce: administration_plugin.nonce, course_id: courseId };
                if (week) data.week_number = week;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success && response.data.length) {
                            var html = '';
                            response.data.forEach(function(row) {
                                html += '<tr data-lessonplan-id="' + row.LessonPlanID + '">';
                                html += '<td style="padding:10px 16px;">' + (row.Date || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Title || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Description || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Materials || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.VideoLinks || '') + '</td>';
                                html += '<td style="padding:10px 16px;">' + Dashboard.escapeHtml(row.Notes || '') + '</td>';
                                html += '<td style="padding:10px 16px;text-align:right;">';
                                html += '<button class="button button-secondary button-xs edit-lessonplan-btn" data-lessonplan-id="' + row.LessonPlanID + '">&#9998;</button> ';
                                html += '<button class="button button-danger button-xs delete-lessonplan-btn" data-lessonplan-id="' + row.LessonPlanID + '">&#128465;</button>';
                                html += '</td></tr>';
                            });
                            $tableBody.html(html);
                        } else {
                            $tableBody.html('<tr class="lessonplan-empty-row"><td colspan="7" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">No lesson plans yet.</td></tr>');
                        }
                    },
                    error: function() {
                        $tableBody.html('<tr><td colspan="7" style="color:#b6b6b6;font-style:italic;padding:18px 0 0 18px;">Failed to load lesson plans.</td></tr>');
                    }
                });
            }
            $weekFilter.off('change').on('change', function() { loadLessonPlans(); });
            $addBtn.off('click').on('click', function() {
                showLessonPlanModal('add', { CourseID: courseId }, courseId, weekOptions, function() { loadLessonPlans(); });
            });
            $tableBody.off('click', '.edit-lessonplan-btn').on('click', '.edit-lessonplan-btn', function() {
                var id = $(this).data('lessonplan-id');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_lessonplans', nonce: administration_plugin.nonce, course_id: courseId },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            var row = response.data.find(function(r) { return r.LessonPlanID === id; });
                            if (row) showLessonPlanModal('edit', row, courseId, weekOptions, function() { loadLessonPlans(); });
                        }
                    }
                });
            });
            $tableBody.off('click', '.delete-lessonplan-btn').on('click', '.delete-lessonplan-btn', function() {
                var id = $(this).data('lessonplan-id');
                if (!confirm('Delete this lesson?')) return;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'delete_lessonplan', nonce: administration_plugin.nonce, lessonplan_id: id },
                    success: function(response) {
                        if (response.success) loadLessonPlans();
                        else $msg.html('<span class="error-message">Failed to delete.</span>');
                    },
                    error: function() { $msg.html('<span class="error-message">Failed to delete.</span>'); }
                });
            });
            // Show details modal when row is clicked (not edit/delete)
            $tableBody.off('click', 'tr').on('click', 'tr', function(e) {
                if ($(e.target).closest('button').length) return; // ignore if clicking a button
                var id = $(this).data('lessonplan-id');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: { action: 'get_lessonplans', nonce: administration_plugin.nonce, course_id: courseId },
                    success: function(response) {
                        if (response.success && response.data.length) {
                            var row = response.data.find(function(r) { return r.LessonPlanID === id; });
                            if (row) showLessonPlanDetailsModal(row);
                        }
                    }
                });
            });
            loadWeeks(function() { loadLessonPlans(); });
        });
    });

    // Add event handler for Cancel button
    $(document).off('click', '.cancel-job-posting-btn').on('click', '.cancel-job-posting-btn', function(e) {
        e.preventDefault();
        var jobId = $('.job-posting-full-view').data('job-posting-id');
        Dashboard.showJobPostingFullView(jobId);
    });
    // Add event handler for Delete button
    $(document).off('click', '.delete-job-posting-btn').on('click', '.delete-job-posting-btn', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this job posting? This action cannot be undone.')) return;
        var jobId = $('.job-posting-full-view').data('job-posting-id');
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_job_posting',
                nonce: administration_plugin.nonce,
                job_posting_id: jobId
            },
            success: function(response) {
                if (response.success) {
                    Dashboard.backToDashboard();
                } else {
                    alert(response.data || 'Failed to delete job posting.');
                }
            },
            error: function() {
                alert('Failed to delete job posting.');
            }
        });
    });

    $(document).on('click', '.course-assignment-row', function() {
        $('.course-assignment-row').removeClass('selected');
        $(this).addClass('selected');
        var assignmentId = $(this).data('assignment-id');
        var $panel = $('.course-assignment-details-panel');
        $panel.html('<div class="loading">Loading assignment...</div>').addClass('active').show();
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: {
                action: 'get_assignment_details',
                nonce: administration_plugin.nonce,
                assignment_id: assignmentId
            },
            success: function(response) {
                if (response.success && response.data) {
                    Dashboard.renderAssignmentDetailsPanel(response.data);
                } else {
                    $panel.html('<div class="error-message">Failed to load assignment details.</div>');
                }
            },
            error: function() {
                $panel.html('<div class="error-message">Failed to load assignment details.</div>');
            }
        });
    });

    // Add CSS for .grades-card-ui, .button-xs, and .grades-striped-row if not present
    if (!$('style#grades-card-ui-style').length) {
        var cardCss = `.grades-card-ui { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(34,113,177,0.07); padding: 32px 36px 28px 36px; min-height: 120px; margin-top: 10px; }\n.button-xs { font-size: 0.85em !important; padding: 2px 8px !important; border-radius: 6px !important; min-width: 0 !important; line-height: 1.2 !important; letter-spacing:0; }\n.grades-striped-row { background: #f7fafd; }`;
        $('head').append('<style id="grades-card-ui-style">' + cardCss + '</style>');
    }
    // Ensure delete-grade-btn is functional
    $(document).off('click', '.delete-grade-btn').on('click', '.delete-grade-btn', function() {
        var gradeId = $(this).data('grade-id');
        if (!confirm('Are you sure you want to delete this grade?')) return;
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: { action: 'delete_grade', nonce: administration_plugin.nonce, grade_id: gradeId },
            success: function(response) {
                if (response.success) {
                    // Reload grades for the selected assignment if visible
                    var selectedAssignment = $('.course-grades-assignment-row.selected').data('assignment-id');
                    if (selectedAssignment) {
                        $('.course-grades-assignment-row.selected').click();
                    }
                } else {
                    alert(response.data || 'Failed to delete grade.');
                }
            },
            error: function() {
                alert('Failed to delete grade.');
            }
        });
    });

    // --- Curriculum Add/Edit Modal Logic ---
    function showCurriculumModal(mode, data, onSave) {
        var isEdit = mode === 'edit';
        var modalHtml = '<div class="modal curriculum-modal" id="curriculum-modal">';
        modalHtml += '<div class="modal-content" style="max-width: 480px;">';
        modalHtml += '<button class="close" id="close-curriculum-modal">&times;</button>';
        modalHtml += '<h2>' + (isEdit ? 'Edit Week' : 'Add Week') + '</h2>';
        modalHtml += '<form id="curriculum-form">';
        if (isEdit) modalHtml += '<input type="hidden" name="curriculum_id" value="' + (data.CurriculumID || '') + '">';
        modalHtml += '<div class="form-field"><label>Week Number <span class="required">*</span></label><input type="number" name="week_number" required min="1" value="' + (data.WeekNumber || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Objective <span class="required">*</span></label><textarea name="objective" rows="2" required>' + (data.Objective || '') + '</textarea></div>';
        modalHtml += '<div class="form-field"><label>Materials</label><input type="text" name="materials" value="' + (data.Materials || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Video Links</label><input type="text" name="video_links" value="' + (data.VideoLinks || '') + '"></div>';
        modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">' + (isEdit ? 'Save Changes' : 'Add Week') + '</button></div>';
        modalHtml += '<div class="form-message" style="display:none;"></div>';
        modalHtml += '</form></div></div>';
        $('body').append(modalHtml);
        setTimeout(function() { $('#curriculum-modal').addClass('show'); }, 10);
        $('#close-curriculum-modal').on('click', function() { $('#curriculum-modal').removeClass('show'); setTimeout(function() { $('#curriculum-modal').remove(); }, 200); });
        $('#curriculum-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() { if (!$(this).val().trim()) { valid = false; $(this).addClass('input-error'); } else { $(this).removeClass('input-error'); } });
            if (!valid) { $msg.text('Please fill in all required fields.').addClass('error').show(); return; }
            $btn.prop('disabled', true).text(isEdit ? 'Saving...' : 'Adding...');
            var formData = $form.serializeArray();
            var ajaxData = { action: isEdit ? 'edit_curriculum' : 'add_curriculum', nonce: administration_plugin.nonce, course_id: data.CourseID };
            formData.forEach(function(f) { ajaxData[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    $btn.prop('disabled', false).text(isEdit ? 'Save Changes' : 'Add Week');
                    if (response.success) {
                        $msg.text(isEdit ? 'Week updated!' : 'Week added!').addClass('success').show();
                        setTimeout(function() { $('#curriculum-modal').removeClass('show'); setTimeout(function() { $('#curriculum-modal').remove(); }, 200); if (onSave) onSave(); }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to save.').addClass('error').show();
                    }
                },
                error: function() { $btn.prop('disabled', false).text(isEdit ? 'Save Changes' : 'Add Week'); $msg.text('Failed to save.').addClass('error').show(); }
            });
        });
    }
    // --- Lesson Plan Add/Edit Modal Logic ---
    function showLessonPlanModal(mode, data, courseId, weekOptions, onSave) {
        var isEdit = mode === 'edit';
        var modalHtml = '<div class="modal lessonplan-modal" id="lessonplan-modal">';
        modalHtml += '<div class="modal-content" style="max-width: 540px;">';
        modalHtml += '<button class="close" id="close-lessonplan-modal">&times;</button>';
        modalHtml += '<h2>' + (isEdit ? 'Edit Lesson' : 'Add Lesson') + '</h2>';
        modalHtml += '<form id="lessonplan-form">';
        if (isEdit) modalHtml += '<input type="hidden" name="lessonplan_id" value="' + (data.LessonPlanID || '') + '">';
        modalHtml += '<div class="form-field"><label>Week <span class="required">*</span></label><select name="week_number" required>';
        weekOptions.forEach(function(opt) { modalHtml += '<option value="' + opt.value + '"' + (data.WeekNumber == opt.value ? ' selected' : '') + '>Week ' + opt.value + '</option>'; });
        modalHtml += '</select></div>';
        modalHtml += '<div class="form-field"><label>Date <span class="required">*</span></label><input type="date" name="date" required value="' + (data.Date || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Title <span class="required">*</span></label><input type="text" name="title" required maxlength="150" value="' + (data.Title || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Description</label><textarea name="description" rows="2">' + (data.Description || '') + '</textarea></div>';
        modalHtml += '<div class="form-field"><label>Materials</label><input type="text" name="materials" value="' + (data.Materials || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Video Links</label><input type="text" name="video_links" value="' + (data.VideoLinks || '') + '"></div>';
        modalHtml += '<div class="form-field"><label>Notes</label><textarea name="notes" rows="2">' + (data.Notes || '') + '</textarea></div>';
        modalHtml += '<div class="form-actions"><button type="submit" class="button button-primary">' + (isEdit ? 'Save Changes' : 'Add Lesson') + '</button></div>';
        modalHtml += '<div class="form-message" style="display:none;"></div>';
        modalHtml += '</form></div></div>';
        $('body').append(modalHtml);
        setTimeout(function() { $('#lessonplan-modal').addClass('show'); }, 10);
        $('#close-lessonplan-modal').on('click', function() { $('#lessonplan-modal').removeClass('show'); setTimeout(function() { $('#lessonplan-modal').remove(); }, 200); });
        $('#lessonplan-form').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $msg = $form.find('.form-message');
            var $btn = $form.find('button[type="submit"]');
            $msg.hide().removeClass('error success');
            var valid = true;
            $form.find('[required]').each(function() { if (!$(this).val().trim()) { valid = false; $(this).addClass('input-error'); } else { $(this).removeClass('input-error'); } });
            if (!valid) { $msg.text('Please fill in all required fields.').addClass('error').show(); return; }
            $btn.prop('disabled', true).text(isEdit ? 'Saving...' : 'Adding...');
            var formData = $form.serializeArray();
            var ajaxData = { action: isEdit ? 'edit_lessonplan' : 'add_lessonplan', nonce: administration_plugin.nonce, course_id: courseId };
            formData.forEach(function(f) { ajaxData[f.name] = f.value; });
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: ajaxData,
                success: function(response) {
                    $btn.prop('disabled', false).text(isEdit ? 'Save Changes' : 'Add Lesson');
                    if (response.success) {
                        $msg.text(isEdit ? 'Lesson updated!' : 'Lesson added!').addClass('success').show();
                        setTimeout(function() { $('#lessonplan-modal').removeClass('show'); setTimeout(function() { $('#lessonplan-modal').remove(); }, 200); if (onSave) onSave(); }, 900);
                    } else {
                        $msg.text(response.data || 'Failed to save.').addClass('error').show();
                    }
                },
                error: function() { $btn.prop('disabled', false).text(isEdit ? 'Save Changes' : 'Add Lesson'); $msg.text('Failed to save.').addClass('error').show(); }
            });
        });
    }
    // --- Lesson Plan Details Modal ---
    function showLessonPlanDetailsModal(data) {
        var modalHtml = '<div class="modal lessonplan-details-modal" id="lessonplan-details-modal">';
        modalHtml += '<div class="modal-content grades-card-ui" style="max-width: 540px; padding: 32px 36px 28px 36px; border-radius: 14px; box-shadow: 0 2px 12px rgba(34,113,177,0.09); background: #fff;">';
        modalHtml += '<button class="close" id="close-lessonplan-details-modal" style="position:absolute;top:18px;right:18px;font-size:1.6em;color:#b6b6b6;background:none;border:none;cursor:pointer;">&times;</button>';
        modalHtml += '<h2 style="font-size:1.32rem;font-weight:700;color:#2271b1;margin-bottom:18px;line-height:1.2;">' + Dashboard.escapeHtml(data.Title || 'Lesson Plan Details') + '</h2>';
        modalHtml += '<div class="lessonplan-details-fields" style="display:flex;flex-direction:column;gap:18px;">';
        modalHtml += '<div style="display:flex;gap:32px;">';
        modalHtml += '<div><span style="color:#6a7a8c;font-weight:500;">Date:</span> <span style="color:#1d2327;">' + (data.Date || '-') + '</span></div>';
        modalHtml += '<div><span style="color:#6a7a8c;font-weight:500;">Week:</span> <span style="color:#1d2327;">' + (data.WeekNumber || '-') + '</span></div>';
        modalHtml += '</div>';
        if (data.Description) {
            modalHtml += '<div><div style="color:#6a7a8c;font-weight:500;margin-bottom:2px;">Description</div><div style="color:#1d2327;white-space:pre-line;">' + Dashboard.escapeHtml(data.Description) + '</div></div>';
        }
        if (data.Materials) {
            modalHtml += '<div><div style="color:#6a7a8c;font-weight:500;margin-bottom:2px;">Materials</div><div style="color:#1d2327;white-space:pre-line;">' + Dashboard.escapeHtml(data.Materials) + '</div></div>';
        }
        if (data.VideoLinks) {
            // Try to auto-link URLs (comma or space separated)
            var links = data.VideoLinks.split(/[,\s]+/).filter(Boolean);
            var linksHtml = links.map(function(link) {
                var url = link.match(/^https?:\/\//) ? link : 'https://' + link;
                return '<a href="' + url + '" target="_blank" style="color:#2271b1;text-decoration:underline;word-break:break-all;">' + Dashboard.escapeHtml(link) + '</a>';
            }).join('<br>');
            modalHtml += '<div><div style="color:#6a7a8c;font-weight:500;margin-bottom:2px;">Video Links</div><div>' + linksHtml + '</div></div>';
        }
        if (data.Notes) {
            modalHtml += '<div><div style="color:#6a7a8c;font-weight:500;margin-bottom:2px;">Notes</div><div style="color:#1d2327;white-space:pre-line;">' + Dashboard.escapeHtml(data.Notes) + '</div></div>';
        }
        modalHtml += '</div></div></div>';
        $('body').append(modalHtml);
        setTimeout(function() { $('#lessonplan-details-modal').addClass('show'); }, 10);
        $('#close-lessonplan-details-modal').on('click', function() { $('#lessonplan-details-modal').removeClass('show'); setTimeout(function() { $('#lessonplan-details-modal').remove(); }, 200); });
    }

})(jQuery); 