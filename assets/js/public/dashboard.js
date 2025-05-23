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
                Dashboard.loadJobPostingsList();
                Dashboard.setupJobPostingsHandlers();
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

        loadJobPostingsList: function() {
            const $list = $('#job-postings-list');
            $list.html('<div class="loading">Loading job postings...</div>');
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_job_postings_list',
                    nonce: administration_plugin.nonce
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
                                    <option value="Inactive">Inactive</option>
                                    <option value="Draft">Draft</option>
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
    });

})(jQuery); 