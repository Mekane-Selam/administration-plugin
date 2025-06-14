// Program View JS Module
(function($) {
    'use strict';
    window.ProgramView = {
        show: function(programId) {
            var $container = $('#program-view-container');
            $container.html('<div class="loading">Loading program...</div>').show();
            // Store the program ID in the container's data
            $container.data('program-id', programId);
            $.ajax({
                url: administration_plugin.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_program_full_view',
                    nonce: administration_plugin.nonce,
                    program_id: programId
                },
                success: function(response) {
                    if (response.success && response.data && response.data.html) {
                        var html = '<div class="program-view-header">' +
                            '<button class="program-view-back-btn" title="Back to Dashboard"><span class="dashicons dashicons-admin-home"></span></button>' +
                            '</div>' + response.data.html;
                        $container.html(html);
                        // Populate Overview fields
                        if (response.data.program) {
                            var program = response.data.program;
                            $container.data('program-owner', program.ProgramOwner || ''); // Store owner for later use
                            $container.find('.overview-field[data-field="ProgramName"]').text(program.ProgramName);
                            $container.find('.overview-field[data-field="ProgramType"]').text(program.ProgramType);
                            $container.find('.overview-field[data-field="ProgramDescription"]').text(program.ProgramDescription);
                            $container.find('.overview-field[data-field="StartDate"]').text(program.StartDate);
                            $container.find('.overview-field[data-field="EndDate"]').text(program.EndDate);
                            $container.find('.overview-field[data-field="ActiveFlag"]').text(program.ActiveFlag ? 'Active' : 'Inactive');
                            // Set edit form values
                            var $editForm = $container.find('.overview-edit-mode');
                            $editForm.find('input[name="ProgramName"]').val(program.ProgramName);
                            $editForm.find('input[name="ProgramType"]').val(program.ProgramType);
                            $editForm.find('textarea[name="ProgramDescription"]').val(program.ProgramDescription);
                            $editForm.find('input[name="StartDate"]').val(program.StartDate);
                            $editForm.find('input[name="EndDate"]').val(program.EndDate);
                            $editForm.find('select[name="ActiveFlag"]').val(program.ActiveFlag ? '1' : '0');
                        }
                        // Add placeholder for courses list if education type
                        if (response.data.program && response.data.program.ProgramType && response.data.program.ProgramType.toLowerCase() === 'education') {
                            if ($container.find('.program-courses-list-placeholder').length === 0) {
                                $container.find('.program-type-education').append('<div class="program-courses-list-placeholder">[Courses list will appear here]</div>');
                            }
                            // Reload staff list for education programs
                            ProgramView.reloadStaffList(programId);
                        }
                    } else {
                        $container.html('<div class="error-message">Failed to load program view.</div>');
                    }
                },
                error: function() {
                    $container.html('<div class="error-message">Failed to load program view.</div>');
                }
            });
        },
        hide: function() {
            $('#program-view-container').hide().empty();
            $('.administration-public-dashboard').show();
        },
        init: function() {
            // Add modal templates to the page
            $('body').append(`
                <div id="add-course-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Add New Course</h2>
                        <form id="add-course-form">
                            <div class="form-field">
                                <label for="course-name">Course Name</label>
                                <input type="text" id="course-name" name="CourseName" required>
                            </div>
                            <div class="form-field">
                                <label for="course-level">Level</label>
                                <input type="text" id="course-level" name="Level">
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Add Course</button>
                                <button type="button" class="button" id="cancel-add-course">Cancel</button>
                            </div>
                        </form>
                        <div id="add-course-message"></div>
                    </div>
                </div>
                <div id="add-enrollment-modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Add New Enrollment</h2>
                        <form id="add-enrollment-form">
                            <div class="form-field">
                                <label for="enrollment-person-search">Person</label>
                                <input type="text" id="enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                                <div id="enrollment-person-list" style="margin-top:8px; max-height:180px; overflow-y:auto; border:1px solid #e3e7ee; border-radius:6px; background:#fff;"></div>
                                <div id="enrollment-selected-count" style="margin-top:6px; font-size:0.95em; color:#2271b1;"></div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Add Enrollment</button>
                                <button type="button" class="button" id="cancel-add-enrollment">Cancel</button>
                            </div>
                        </form>
                        <div id="add-enrollment-message"></div>
                    </div>
                </div>
            `);

            // Bind Go Back button
            $(document).on('click', '.program-view-back-btn', function(e) {
                ProgramView.hide();
            });

            // Overview Edit button
            $(document).on('click', '.program-view-edu-edit-btn', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.program-view-edu-overview');
                $card.find('.overview-display-mode').hide();
                $card.find('.overview-edit-mode').show();
            });

            // Overview Cancel button
            $(document).on('click', '.overview-cancel-btn', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.program-view-edu-overview');
                $card.find('.overview-edit-mode').hide();
                $card.find('.overview-display-mode').show();
            });

            // Overview Save button
            $(document).on('submit', '.overview-edit-mode', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $card = $form.closest('.program-view-edu-overview');
                var $container = $('#program-view-container');
                var programId = $container.data('program-id');
                var programOwner = $container.data('program-owner') || '';
                if (!programId) {
                    alert('Error: Program ID not found. Please try refreshing the page.');
                    return;
                }
                var data = {
                    action: 'edit_program',
                    nonce: administration_plugin.nonce,
                    program_id: programId,
                    program_name: $form.find('input[name="ProgramName"]').val(),
                    program_type: $form.find('input[name="ProgramType"]').val(),
                    description: $form.find('textarea[name="ProgramDescription"]').val(),
                    start_date: $form.find('input[name="StartDate"]').val(),
                    end_date: $form.find('input[name="EndDate"]').val(),
                    status: $form.find('select[name="ActiveFlag"]').val() === '1' ? 'active' : 'inactive',
                    program_owner: programOwner
                };
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            // Reload the program view to reflect changes
                            ProgramView.show(programId);
                        } else {
                            alert(response.data || 'Failed to save changes.');
                        }
                    },
                    error: function() {
                        alert('Failed to save changes.');
                    }
                });
            });

            // Add Course button
            $(document).on('click', '.program-view-edu-add-course-btn', function(e) {
                e.preventDefault();
                $('#add-course-modal').addClass('show');
                $('#add-course-form')[0].reset();
                $('#add-course-message').html('');
            });

            // Define selectedPeople in a higher scope so it is accessible to both modal and form
            var selectedPeople = [];

            // Add Enrollment button
            $(document).on('click', '.program-view-edu-add-enrollment-btn', function(e) {
                e.preventDefault();
                var programId = $('#program-view-container').data('program-id');
                // Remove course dropdown and add search/filter for person
                $('#add-enrollment-form .form-field').remove();
                $('#add-enrollment-form').prepend(`
                    <div class="form-field">
                        <label for="enrollment-person-search">Person</label>
                        <input type="text" id="enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                        <div id="enrollment-person-list" style="margin-top:8px; max-height:180px; overflow-y:auto; border:1px solid #e3e7ee; border-radius:6px; background:#fff;"></div>
                        <div id="enrollment-selected-count" style="margin-top:6px; font-size:0.95em; color:#2271b1;"></div>
                    </div>
                `);
                var allPeople = [];
                selectedPeople = [];
                function renderPeopleList(query) {
                    var filtered = allPeople.filter(function(person) {
                        var fullName = person.FirstName + ' ' + person.LastName;
                        return !query || fullName.toLowerCase().includes(query.toLowerCase());
                    });
                    var html = '';
                    filtered.forEach(function(person) {
                        var fullName = person.FirstName + ' ' + person.LastName;
                        var checked = selectedPeople.includes(person.PersonID) ? 'checked' : '';
                        html += `<div class='enrollment-person-list-row' style='display:flex;align-items:center;padding:4px 8px;width:100%;'>
                            <div style='width:10%;display:flex;align-items:center;justify-content:center;'>
                                <input type='checkbox' class='enrollment-person-checkbox' value='${person.PersonID}' style='vertical-align:middle;' ${checked}>
                            </div>
                            <span style='width:90%;vertical-align:middle;display:inline-block;'>${fullName}</span>
                        </div>`;
                    });
                    $('#enrollment-person-list').html(html);
                    $('#enrollment-selected-count').text(selectedPeople.length + ' selected');
                }
                function loadPeopleList(query) {
                    $.ajax({
                        url: administration_plugin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_people_for_owner_select',
                            nonce: administration_plugin.nonce
                        },
                        success: function(response) {
                            if (response.success && Array.isArray(response.data)) {
                                allPeople = response.data;
                                renderPeopleList(query || '');
                            }
                        }
                    });
                }
                loadPeopleList('');
                $(document).off('input', '#enrollment-person-search').on('input', '#enrollment-person-search', function() {
                    renderPeopleList($(this).val());
                });
                $(document).off('change', '.enrollment-person-checkbox').on('change', '.enrollment-person-checkbox', function() {
                    var personId = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!selectedPeople.includes(personId)) selectedPeople.push(personId);
                    } else {
                        selectedPeople = selectedPeople.filter(function(id) { return id !== personId; });
                    }
                    $('#enrollment-selected-count').text(selectedPeople.length + ' selected');
                });
                $('#add-enrollment-modal').addClass('show');
                $('#add-enrollment-form')[0].reset();
                $('#add-enrollment-message').html('');
            });

            // Close modals
            $(document).on('click', '.modal .close, .modal .button:not(.button-primary)', function() {
                $(this).closest('.modal').removeClass('show');
            });

            // Add Course form submission
            $(document).on('submit', '#add-course-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-course-message');
                var programId = $('#program-view-container').data('program-id');
                
                var data = {
                    action: 'add_edu_course',
                    nonce: administration_plugin.nonce,
                    program_id: programId,
                    CourseName: $('#course-name').val().trim(),
                    Level: $('#course-level').val().trim()
                };

                if (!data.CourseName) {
                    $message.html('<span class="error-message">Course name is required.</span>');
                    return;
                }

                $message.html('<span class="loading">Adding course...</span>');

                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">Course added successfully!</span>');
                            setTimeout(function() {
                                $('#add-course-modal').removeClass('show');
                                $form[0].reset();
                                $message.html('');
                                // Reload only the courses list
                                ProgramView.reloadCoursesList(programId);
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add course.') + '</span>');
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add course. Please try again.</span>');
                    }
                });
            });

            // Add Enrollment form submission: use selectedPeople array
            $(document).off('submit', '#add-enrollment-form').on('submit', '#add-enrollment-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-enrollment-message');
                var programId = $('#program-view-container').data('program-id');
                if (!selectedPeople.length) {
                    $message.html('<span class="error-message">Please select at least one person.</span>');
                    return;
                }
                $message.html('<span class="loading">Adding enrollment...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'add_edu_enrollment',
                        nonce: administration_plugin.nonce,
                        program_id: programId,
                        'PersonIDs[]': selectedPeople // send as array with []
                    },
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">' + (response.data && response.data.summary ? response.data.summary : 'Enrollment(s) added successfully!') + '</span>');
                            if (response.data && response.data.debug) {
                                console.log('DEBUG:', response.data.debug);
                            }
                            setTimeout(function() {
                                $('#add-enrollment-modal').removeClass('show');
                                $form[0].reset();
                                $message.html('');
                                selectedPeople = [];
                                $('.enrollment-person-checkbox').prop('checked', false);
                                // Reload only the enrollment list
                                ProgramView.reloadEnrollmentList(programId);
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add enrollment.') + '</span>');
                            if (response.data && response.data.debug) {
                                console.log('DEBUG:', response.data.debug);
                            }
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add enrollment. Please try again.</span>');
                    }
                });
            });

            // Helper to reload only the courses list
            ProgramView.reloadCoursesList = function(programId) {
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_program_full_view',
                        nonce: administration_plugin.nonce,
                        program_id: programId
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.html) {
                            var $html = $('<div>' + response.data.html + '</div>');
                            var $newCourses = $html.find('.program-view-edu-courses-list').html();
                            $('#program-view-container .program-view-edu-courses-list').html($newCourses);
                        }
                    }
                });
            };

            // Helper to reload only the enrollment list
            ProgramView.reloadEnrollmentList = function(programId) {
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_program_full_view',
                        nonce: administration_plugin.nonce,
                        program_id: programId
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.html) {
                            var $html = $('<div>' + response.data.html + '</div>');
                            var $newEnrollment = $html.find('.program-view-edu-enrollment-content').html();
                            $('#program-view-container .program-view-edu-enrollment-content').html($newEnrollment);
                            // Reset Edit button state
                            var $editBtn = $('.program-view-edu-edit-enrollment-btn');
                            if ($editBtn.length) {
                                $editBtn.html('<span class="dashicons dashicons-edit"></span>');
                            }
                            $('.enrollment-list-enhanced').removeClass('edit-mode');
                            $('.enrollment-edit-checkbox-col').remove();
                            $('.remove-enrollment-btn').remove();
                        }
                    }
                });
            };

            // Enrollment search functionality
            $(document).on('input', '.enrollment-search-input', function() {
                var query = $(this).val().toLowerCase();
                $('.enrollment-list-enhanced .enrollment-card').each(function() {
                    var name = $(this).find('.enrollment-card-title').text().toLowerCase();
                    if (name.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                // Show/hide placeholder if no results
                var visible = $('.enrollment-list-enhanced .enrollment-card:visible').length;
                var $placeholder = $('.program-enrollment-list-placeholder');
                if (visible === 0) {
                    if ($placeholder.length === 0) {
                        $('.enrollment-list-enhanced').after('<div class="program-enrollment-list-placeholder">No enrollments found for this search.</div>');
                    } else {
                        $placeholder.show().text('No enrollments found for this search.');
                    }
                } else {
                    $placeholder.hide();
                }
            });

            // Add course detail modal template if not present
            if ($('#course-detail-modal').length === 0) {
                $('body').append(`
                    <div id="course-detail-modal" class="modal course-detail-modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <div class="course-detail-modal-sections">
                                <div class="course-detail-main-section"></div>
                            </div>
                        </div>
                    </div>
                `);
            }
            // Open course detail modal on course card click
            $(document).on('click', '.course-card', function() {
                var courseName = $(this).find('.course-card-title').text();
                var programId = $('#program-view-container').data('program-id');
                var courseId = $('#course-detail-modal .course-detail-tab-content').data('course-id');
                $(this).closest('.courses-list-enhanced').find('.course-card').each(function() {
                    if ($(this).find('.course-card-title').text() === courseName) {
                        courseId = $(this).data('course-id');
                    }
                });
                if (!courseId) {
                    courseId = $(this).data('course-id');
                }
                if (!courseId) {
                    courseId = $(this).attr('data-course-id');
                }
                if (!courseId) {
                    alert('Could not determine course ID.');
                    return;
                }
                // Fetch course details and enrollments (now as a single tabbed interface)
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_course_detail_tabs',
                        nonce: administration_plugin.nonce,
                        course_id: courseId,
                        program_id: programId
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.html) {
                            $('#course-detail-modal .course-detail-main-section').html(response.data.html);
                            $('#course-detail-modal').addClass('show');
                        } else {
                            alert(response.data || 'Failed to load course details.');
                        }
                    },
                    error: function() {
                        alert('Failed to load course details.');
                    }
                });
            });
            // Close course detail modal
            $(document).on('click', '#course-detail-modal .close', function() {
                $('#course-detail-modal').removeClass('show');
            });

            // Tab switching for course detail modal
            $(document).on('click', '#course-detail-modal .tab-button', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var tab = $btn.data('tab');
                $btn.addClass('active').siblings('.tab-button').removeClass('active');
                var $tabContent = $btn.closest('.course-detail-modal').find('.course-detail-tab-content');
                $tabContent.find('.tab-pane').removeClass('active');
                $tabContent.find('#' + tab).addClass('active');
            });

            // --- BEGIN: Multi-Enrollment for Courses ---
            // Shared selectedPeople array for course add enrollment modal
            var selectedPeople = [];

            $(document).on('click', '.add-course-enrollment-btn', function(e) {
                e.preventDefault();
                var courseId = $('#course-detail-modal .course-detail-tab-content').data('course-id');
                var programId = $('#program-view-container').data('program-id');
                // Modal for multi-select add
                if ($('#add-course-enrollment-modal').length === 0) {
                    $('body').append(`
                        <div id="add-course-enrollment-modal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2>Add Course Enrollment</h2>
                                <form id="add-course-enrollment-form">
                                    <div class="form-field">
                                        <label for="course-enrollment-person-search">Person</label>
                                        <input type="text" id="course-enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                                        <div id="course-enrollment-person-list" style="margin-top:8px; max-height:180px; overflow-y:auto; border:1px solid #e3e7ee; border-radius:6px; background:#fff;"></div>
                                        <div id="course-enrollment-selected-count" style="margin-top:6px; font-size:0.95em; color:#2271b1;"></div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="button button-primary">Add Enrollment</button>
                                        <button type="button" class="button" id="cancel-add-course-enrollment">Cancel</button>
                                    </div>
                                </form>
                                <div id="add-course-enrollment-message"></div>
                            </div>
                        </div>
                    `);
                }
                var allPeople = [];
                selectedPeople = [];
                function renderPeopleList(query) {
                    var filtered = allPeople.filter(function(person) {
                        var fullName = person.FirstName + ' ' + person.LastName;
                        return !query || fullName.toLowerCase().includes(query.toLowerCase());
                    });
                    var html = '';
                    filtered.forEach(function(person) {
                        var fullName = person.FirstName + ' ' + person.LastName;
                        var checked = selectedPeople.includes(person.PersonID) ? 'checked' : '';
                        html += `<div class='enrollment-person-list-row' style='display:flex;align-items:center;padding:4px 8px;width:100%;'>
                            <div style='width:10%;display:flex;align-items:center;justify-content:center;'>
                                <input type='checkbox' class='course-enrollment-person-checkbox' value='${person.PersonID}' style='vertical-align:middle;' ${checked}>
                            </div>
                            <span style='width:90%;vertical-align:middle;display:inline-block;'>${fullName}</span>
                        </div>`;
                    });
                    $('#course-enrollment-person-list').html(html);
                    $('#course-enrollment-selected-count').text(selectedPeople.length + ' selected');
                    if (filtered.length === 0) {
                        console.log('[Add Course Enrollment Modal] No people found for selection.');
                    }
                }
                function loadPeopleList(query) {
                    $.ajax({
                        url: administration_plugin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_people_enrolled_in_program',
                            nonce: administration_plugin.nonce,
                            program_id: programId
                        },
                        success: function(response) {
                            if (response.success && Array.isArray(response.data)) {
                                allPeople = response.data;
                                renderPeopleList(query || '');
                            } else {
                                console.log('[Add Course Enrollment Modal] Failed to load people list or empty.');
                            }
                        },
                        error: function() {
                            console.log('[Add Course Enrollment Modal] AJAX error loading people list.');
                        }
                    });
                }
                loadPeopleList('');
                $(document).off('input', '#course-enrollment-person-search').on('input', '#course-enrollment-person-search', function() {
                    renderPeopleList($(this).val());
                });
                $(document).off('change', '.course-enrollment-person-checkbox').on('change', '.course-enrollment-person-checkbox', function() {
                    var personId = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!selectedPeople.includes(personId)) selectedPeople.push(personId);
                    } else {
                        selectedPeople = selectedPeople.filter(function(id) { return id !== personId; });
                    }
                    $('#course-enrollment-selected-count').text(selectedPeople.length + ' selected');
                    console.log('[Add Course Enrollment Modal] selectedPeople:', selectedPeople);
                });
                $('#add-course-enrollment-modal').addClass('show');
                $('#add-course-enrollment-form')[0].reset();
                $('#add-course-enrollment-message').html('');
            });
            // Add Enrollment form submission (multi)
            $(document).off('submit', '#add-course-enrollment-form').on('submit', '#add-course-enrollment-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-course-enrollment-message');
                var courseId = $('#course-detail-modal .course-detail-tab-content').data('course-id');
                // Debug log selectedPeople
                console.log('[Add Course Enrollment Modal] Submitting selectedPeople:', typeof selectedPeople !== 'undefined' ? selectedPeople : 'undefined');
                if (!selectedPeople || !selectedPeople.length) {
                    $message.html('<span class="error-message">Please select at least one person.</span>');
                    return;
                }
                $message.html('<span class="loading">Adding enrollment...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'add_course_enrollment',
                        nonce: administration_plugin.nonce,
                        CourseID: courseId,
                        'PersonIDs[]': selectedPeople
                    },
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">' + (response.data && response.data.summary ? response.data.summary : 'Enrollment(s) added successfully!') + '</span>');
                            if (response.data && response.data.debug) {
                                console.log('DEBUG:', response.data.debug);
                            }
                            setTimeout(function() {
                                $('#add-course-enrollment-modal').removeClass('show');
                                $form[0].reset();
                                $message.html('');
                                selectedPeople = [];
                                $('.course-enrollment-person-checkbox').prop('checked', false);
                                // Reload enrollments list for this course
                                var programId = $('#program-view-container').data('program-id');
                                var courseId = $('#course-detail-modal').find('[data-course-id]').first().data('course-id');
                                $.ajax({
                                    url: administration_plugin.ajax_url,
                                    type: 'POST',
                                    data: {
                                        action: 'get_course_detail_tabs',
                                        nonce: administration_plugin.nonce,
                                        course_id: courseId,
                                        program_id: programId
                                    },
                                    success: function(response) {
                                        if (response.success && response.data && response.data.html) {
                                            $('#course-detail-modal .course-detail-main-section').html(response.data.html);
                                        }
                                    }
                                });
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add enrollment.') + '</span>');
                            if (response.data && response.data.debug) {
                                console.log('DEBUG:', response.data.debug);
                            }
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add enrollment. Please try again.</span>');
                    }
                });
            });
            // --- END: Multi-Enrollment for Courses ---

            // Remove Enrollment (Edit Mode) for courses
            $(document).on('click', '.edit-course-enrollment-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $enrollmentsList = $('.course-detail-enrollments-list');
                var $toolbar = $(this).closest('.course-detail-enrollments-actions');
                var $editBtn = $(this);
                // Always ensure the edit button has the correct class and no inline style
                $editBtn.removeClass('button-danger').addClass('button').css('margin-left', '8px');
                if ($enrollmentsList.hasClass('edit-mode')) {
                    // Exit edit mode
                    $enrollmentsList.removeClass('edit-mode');
                    $enrollmentsList.find('.course-enrollment-edit-checkbox-col').remove();
                    $toolbar.find('.remove-course-enrollment-btn').remove();
                    $editBtn.html('<span class="dashicons dashicons-edit"></span>');
                    $editBtn.removeClass('button-danger').addClass('button');
                } else {
                    // Enter edit mode
                    $enrollmentsList.addClass('edit-mode');
                    $enrollmentsList.find('.course-detail-enrollment-card').prepend('<div class="course-enrollment-edit-checkbox-col"><input type="checkbox" class="course-enrollment-edit-checkbox"></div>');
                    // Remove any existing Remove Selected button in the toolbar
                    $toolbar.find('.remove-course-enrollment-btn').remove();
                    // Inject Remove Selected button next to Edit in the toolbar
                    var $removeBtn = $('<button class="button button-danger remove-course-enrollment-btn" style="margin-left:8px;">Remove Selected</button>');
                    $editBtn.after($removeBtn);
                    $editBtn.html('<span class="dashicons dashicons-no-alt"></span>'); // X icon
                    $editBtn.removeClass('button').addClass('button-danger');
                }
            });
            // Remove selected enrollments (courses)
            $(document).on('click', '.remove-course-enrollment-btn', function() {
                var selected = [];
                $('.course-enrollment-edit-checkbox:checked').each(function() {
                    var $card = $(this).closest('.course-detail-enrollment-card');
                    selected.push($card.data('person-id'));
                });
                if (selected.length === 0) {
                    alert('Please select at least one enrollment to remove.');
                    return;
                }
                var courseId = $('#course-detail-modal .course-detail-tab-content').data('course-id');
                if (!confirm('Are you sure you want to remove the selected enrollments?')) return;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_course_enrollments',
                        nonce: administration_plugin.nonce,
                        CourseID: courseId,
                        'PersonIDs[]': selected
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.data && response.data.summary) {
                                alert(response.data.summary);
                            }
                            // Reload enrollments list for this course
                            var programId = $('#program-view-container').data('program-id');
                            var courseId = $('#course-detail-modal').find('[data-course-id]').first().data('course-id');
                            $.ajax({
                                url: administration_plugin.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'get_course_detail_tabs',
                                    nonce: administration_plugin.nonce,
                                    course_id: courseId,
                                    program_id: programId
                                },
                                success: function(response) {
                                    if (response.success && response.data && response.data.html) {
                                        $('#course-detail-modal .course-detail-main-section').html(response.data.html);
                                    }
                                }
                            });
                        } else {
                            alert(response.data || 'Failed to remove enrollments.');
                        }
                    },
                    error: function() {
                        alert('Failed to remove enrollments.');
                    }
                });
            });

            // Add search functionality for course detail enrollments
            $(document).on('input', '.course-detail-enrollments-search', function() {
                var query = $(this).val().toLowerCase();
                $('.course-detail-enrollments-list .course-detail-enrollment-card').each(function() {
                    var name = $(this).find('.course-detail-enrollment-title').text().toLowerCase();
                    if (name.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Add dynamic search for .program-view-edu-enrollment-search to filter .enrollment-list-enhanced .enrollment-card by name, just like the .enrollment-search-input handler.
            $(document).on('input', '.program-view-edu-enrollment-search', function() {
                var query = $(this).val().toLowerCase();
                $('.enrollment-list-enhanced .enrollment-card').each(function() {
                    var name = $(this).find('.enrollment-card-title').text().toLowerCase();
                    if (name.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                // Show/hide placeholder if no results
                var visible = $('.enrollment-list-enhanced .enrollment-card:visible').length;
                var $placeholder = $('.program-enrollment-list-placeholder');
                if (visible === 0) {
                    if ($placeholder.length === 0) {
                        $('.enrollment-list-enhanced').after('<div class="program-enrollment-list-placeholder">No enrollments found for this search.</div>');
                    } else {
                        $placeholder.show().text('No enrollments found for this search.');
                    }
                } else {
                    $placeholder.hide();
                }
            });

            // Add Role Modal logic
            $(document).on('click', '.program-view-edu-add-role-btn', function() {
                $('#add-staff-role-modal').addClass('show');
                $('#add-staff-role-form')[0].reset();
                $('#add-staff-role-message').html('');
            });
            $(document).on('click', '#add-staff-role-modal .close, #cancel-add-staff-role', function() {
                $('#add-staff-role-modal').removeClass('show');
            });
            $(document).on('submit', '#add-staff-role-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $msg = $('#add-staff-role-message');
                var title = $('#staff-role-title').val().trim();
                var desc = $('#staff-role-description').val().trim();
                if (!title) {
                    $msg.html('<span class="error-message">Role Title is required.</span>');
                    return;
                }
                // Generate StaffRoleID
                var staffRoleId = 'ROLE' + Math.floor(10000 + Math.random() * 90000);
                $msg.html('<span class="loading">Adding role...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'add_staff_role',
                        nonce: administration_plugin.nonce,
                        StaffRoleID: staffRoleId,
                        RoleTitle: title,
                        StaffRoleDescription: desc
                    },
                    success: function(response) {
                        if (response.success) {
                            $msg.html('<span class="success-message">Role added successfully!</span>');
                            setTimeout(function() {
                                $('#add-staff-role-modal').removeClass('show');
                                $form[0].reset();
                                $msg.html('');
                                // Optionally reload staff roles list here
                            }, 800);
                        } else {
                            $msg.html('<span class="error-message">' + (response.data || 'Failed to add role.') + '</span>');
                        }
                    },
                    error: function() {
                        $msg.html('<span class="error-message">Failed to add role. Please try again.</span>');
                    }
                });
            });

            // Add Staff Modal logic
            $(document).on('click', '.program-view-edu-add-staff-btn', function() {
                $('#add-staff-modal').addClass('show');
                $('#add-staff-form')[0].reset();
                $('#add-staff-message').html('');
                $('#staff-person-select').html('');
            });
            $(document).on('click', '#add-staff-modal .close, #cancel-add-staff', function() {
                $('#add-staff-modal').removeClass('show');
            });
            $(document).on('input', '#staff-person-search', function() {
                var query = $(this).val();
                var $select = $('#staff-person-select');
                $select.html('<option>Loading...</option>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_people_list',
                        nonce: administration_plugin.nonce,
                        search: query
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Parse HTML and extract person rows
                            var $html = $('<div>' + response.data + '</div>');
                            var options = '';
                            $html.find('.person-row').each(function() {
                                var id = $(this).data('person-id');
                                var name = $(this).find('.person-name').text();
                                var email = $(this).find('.person-email').text();
                                options += '<option value="' + id + '">' + name + (email ? ' (' + email + ')' : '') + '</option>';
                            });
                            $select.html(options || '<option disabled>No people found.</option>');
                        } else {
                            $select.html('<option disabled>No people found.</option>');
                        }
                    },
                    error: function() {
                        $select.html('<option disabled>Failed to load people.</option>');
                    }
                });
            });
            $(document).on('submit', '#add-staff-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $msg = $('#add-staff-message');
                var personId = $('#staff-person-select').val();
                var staffRoleId = $('#staff-role-select').val();
                var programId = $('#program-view-container').data('program-id');
                if (!personId || !staffRoleId) {
                    $msg.html('<span class="error-message">Please select a person and a role.</span>');
                    return;
                }
                $msg.html('<span class="loading">Adding staff member...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'add_staff_member',
                        nonce: administration_plugin.nonce,
                        PersonID: personId,
                        StaffRolesID: staffRoleId,
                        ProgramID: programId
                    },
                    success: function(response) {
                        if (response.success) {
                            $msg.html('<span class="success-message">Staff member added successfully!</span>');
                            setTimeout(function() {
                                $('#add-staff-modal').removeClass('show');
                                $form[0].reset();
                                $msg.html('');
                                // Reload staff list for the current program
                                ProgramView.reloadStaffList(programId);
                            }, 800);
                        } else {
                            $msg.html('<span class="error-message">' + (response.data || 'Failed to add staff member.') + '</span>');
                        }
                    },
                    error: function() {
                        $msg.html('<span class="error-message">Failed to add staff member. Please try again.</span>');
                    }
                });
            });

            // Staff details split view logic
            $(document).on('click', '.program-view-edu-staff-card', function() {
                var personId = $(this).data('person-id');
                var $panel = $('.program-view-edu-staff-details-panel');
                $panel.html('<div class="loading">Loading staff details...</div>').show();
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_person_details',
                        nonce: administration_plugin.nonce,
                        person_id: personId
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            var person = response.data;
                            var detailsHtml = `
                                <button class="program-view-edu-staff-details-close" title="Close">&times;</button>
                                <h3 class="person-details-title">${person.FirstName} ${person.LastName}</h3>
                                <div class="person-details-content">
                                    <div class="person-detail-row">
                                        <span class="person-detail-label">ID:</span>
                                        <span class="person-detail-value">${person.PersonID}</span>
                                    </div>
                                    <div class="person-detail-row">
                                        <span class="person-detail-label">Email:</span>
                                        <span class="person-detail-value">${person.Email || 'N/A'}</span>
                                    </div>
                                    <div class="person-detail-row">
                                        <span class="person-detail-label">Phone:</span>
                                        <span class="person-detail-value">${person.Phone || 'N/A'}</span>
                                    </div>
                                </div>
                            `;
                            $panel.html(detailsHtml).show();
                        } else {
                            $panel.html('<div class="error-message">Failed to load staff details.</div>');
                        }
                    },
                    error: function() {
                        $panel.html('<div class="error-message">Failed to load staff details.</div>');
                    }
                });
            });
            $(document).on('click', '.program-view-edu-staff-details-close', function() {
                $('.program-view-edu-staff-details-panel').hide().empty();
            });

            // Course General Tab: Instructors Edit/Save Logic
            $(document).on('click', '#edit-instructors-btn', function() {
                // Hide display, show selects and save/cancel
                $('#primary-instructor-display, #backup1-display, #backup2-display').hide();
                $('.course-general-edit').show();
                $('#edit-instructors-btn').hide();
                $('#save-instructors-btn, #cancel-instructors-btn').show();
                // Populate dropdowns with staff
                var $modal = $('#course-detail-modal');
                var programId = $('#program-view-container').data('program-id');
                var courseId = $('.course-detail-tab-content').data('course-id');
                function populateStaffSelect($select, selectedId) {
                    $.ajax({
                        url: administration_plugin.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_program_staff_list',
                            nonce: administration_plugin.nonce,
                            program_id: programId
                        },
                        success: function(response) {
                            if (response.success && Array.isArray(response.data)) {
                                var options = '<option value="">Select...</option>';
                                response.data.forEach(function(staff) {
                                    var fullName = staff.FirstName + ' ' + staff.LastName;
                                    options += `<option value="${staff.PersonID}"${staff.PersonID == selectedId ? ' selected' : ''}>${fullName}</option>`;
                                });
                                $select.html(options);
                            }
                        }
                    });
                }
                populateStaffSelect($('#primary-instructor-select'), $('#primary-instructor-display').data('person-id'));
                populateStaffSelect($('#backup1-select'), $('#backup1-display').data('person-id'));
                populateStaffSelect($('#backup2-select'), $('#backup2-display').data('person-id'));
            });
            $(document).on('click', '#cancel-instructors-btn', function() {
                // Hide selects, show display, reset buttons
                $('.course-general-edit').hide();
                $('#primary-instructor-display, #backup1-display, #backup2-display').show();
                $('#edit-instructors-btn').show();
                $('#save-instructors-btn, #cancel-instructors-btn').hide();
                $('#instructors-message').html('');
            });
            $(document).on('click', '#save-instructors-btn', function() {
                var $msg = $('#instructors-message');
                var courseId = $('.course-detail-tab-content').data('course-id');
                var primary = $('#primary-instructor-select').val();
                var backup1 = $('#backup1-select').val();
                var backup2 = $('#backup2-select').val();
                if (!primary) {
                    $msg.html('<span class="error-message">Primary Instructor is required.</span>');
                    return;
                }
                $msg.html('<span class="loading">Saving...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_course_instructors',
                        nonce: administration_plugin.nonce,
                        CourseID: courseId,
                        PrimaryInstructorID: primary,
                        BackUpTeacher1ID: backup1,
                        BackUpTeacher2ID: backup2
                    },
                    success: function(response) {
                        if (response.success) {
                            $msg.html('<span class="success-message">Saved!</span>');
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            $msg.html('<span class="error-message">' + (response.data || 'Failed to save.') + '</span>');
                        }
                    },
                    error: function() {
                        $msg.html('<span class="error-message">Failed to save. Please try again.</span>');
                    }
                });
            });

            // Helper to reload only the staff list for the current program
            ProgramView.reloadStaffList = function(programId) {
                var $container = $('.program-view-edu-staff-content-list');
                if (!$container.length) return;
                $container.html('<div class="loading">Loading staff...</div>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_program_staff_list',
                        nonce: administration_plugin.nonce,
                        program_id: programId
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            var html = '';
                            if (response.data.length) {
                                html += '<div class="program-view-edu-staff-grid">';
                                response.data.forEach(function(staff) {
                                    html += `<div class="program-view-edu-staff-card" data-person-id="${staff.PersonID}">
                                        <div class="program-view-edu-staff-info">
                                            <h4 class="program-view-edu-staff-name">${staff.FirstName} ${staff.LastName}</h4>
                                            <span class="program-view-edu-staff-role">${staff.RoleTitle || ''}</span>
                                        </div>
                                        <div class="program-view-edu-staff-actions">
                                            <button type="button" class="program-view-edu-staff-edit-btn" data-staff-id="${staff.StaffID || ''}"><span class="dashicons dashicons-edit"></span></button>
                                            <button type="button" class="program-view-edu-staff-remove-btn" data-staff-id="${staff.StaffID || ''}"><span class="dashicons dashicons-trash"></span></button>
                                        </div>
                                    </div>`;
                                });
                                html += '</div>';
                            } else {
                                html = '<div class="program-view-edu-staff-empty"><span class="dashicons dashicons-groups"></span><p>No staff members assigned to this program yet.</p></div>';
                            }
                            $container.html(html);
                        } else {
                            $container.html('<div class="error-message">Failed to load staff.</div>');
                        }
                    },
                    error: function() {
                        $container.html('<div class="error-message">Failed to load staff.</div>');
                    }
                });
            };

            // 1. Add Edit Enrollment button and logic
            $(document).on('click', '.program-view-edu-edit-enrollment-btn', function() {
                var $enrollmentList = $('.enrollment-list-enhanced');
                var $editBtn = $(this);
                if ($enrollmentList.hasClass('edit-mode')) {
                    // Exit edit mode
                    $enrollmentList.removeClass('edit-mode');
                    $enrollmentList.find('.enrollment-edit-checkbox-col').remove();
                    $('.remove-enrollment-btn').remove();
                    $editBtn.html('<span class="dashicons dashicons-edit"></span>');
                } else {
                    // Enter edit mode
                    $enrollmentList.addClass('edit-mode');
                    $enrollmentList.find('.enrollment-card').prepend('<div class="enrollment-edit-checkbox-col"><input type="checkbox" class="enrollment-edit-checkbox"></div>');
                    $enrollmentList.before('<button class="button button-danger remove-enrollment-btn" style="margin-bottom:12px;">Remove Selected</button>');
                    $editBtn.html('<span class="dashicons dashicons-no-alt"></span>'); // X icon
                }
            });

            // 2. Remove selected enrollments
            $(document).on('click', '.remove-enrollment-btn', function() {
                var selected = [];
                $('.enrollment-edit-checkbox:checked').each(function() {
                    var $card = $(this).closest('.enrollment-card');
                    selected.push($card.data('person-id'));
                });
                if (selected.length === 0) {
                    alert('Please select at least one enrollment to remove.');
                    return;
                }
                var programId = $('#program-view-container').data('program-id');
                if (!confirm('Are you sure you want to remove the selected enrollments?')) return;
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'remove_edu_enrollments',
                        nonce: administration_plugin.nonce,
                        program_id: programId,
                        'PersonIDs[]': selected
                    },
                    success: function(response) {
                        if (response.success) {
                            ProgramView.reloadEnrollmentList(programId);
                        } else {
                            alert(response.data || 'Failed to remove enrollments.');
                        }
                    },
                    error: function() {
                        alert('Failed to remove enrollments.');
                    }
                });
            });

            // 3. Add Edit button to enrollment header after page load
            $(document).on('ready programViewLoaded', function() {
                var $header = $('.program-view-edu-enrollment-header');
                if ($header.length && $header.find('.program-view-edu-edit-enrollment-btn').length === 0) {
                    $header.append('<button class="button program-view-edu-edit-enrollment-btn" style="margin-left:12px;">Edit</button>');
                }
            });

            // Also inject the Edit button after AJAX reloads enrollment content
            $(document).on('ajaxComplete', function(e, xhr, settings) {
                if (settings && settings.data && settings.data.includes('get_program_full_view')) {
                    var $header = $('.program-view-edu-enrollment-header');
                    if ($header.length && $header.find('.program-view-edu-edit-enrollment-btn').length === 0) {
                        $header.append('<button class="button program-view-edu-edit-enrollment-btn" style="margin-left:12px;">Edit</button>');
                    }
                }
            });

            // Remove any old Edit button in the enrollment toolbar (e.g., next to the search input)
            function removeOldEditButton() {
                $('.program-view-edu-enrollment-toolbar .program-view-edu-edit-enrollment-btn').remove();
            }
            // Inject the Edit button as a sibling to the Plus button
            function injectEditButton() {
                var $toolbar = $('.program-view-edu-enrollment-toolbar');
                if ($toolbar.length) {
                    // Remove any Edit button that is not a direct child of the toolbar (fixes template or legacy issues)
                    $('.program-view-edu-edit-enrollment-btn').each(function() {
                        if (!$(this).parent().is($toolbar)) {
                            $(this).remove();
                        }
                    });
                    var $plusBtn = $toolbar.find('.program-view-edu-add-enrollment-btn');
                    // Only inject if not already present as a sibling to the plus button
                    if ($plusBtn.length && $plusBtn.siblings('.program-view-edu-edit-enrollment-btn').length === 0) {
                        var $editBtn = $('<button type="button" class="program-view-edu-toolbar-btn program-view-edu-edit-enrollment-btn" title="Edit Enrollments" style="margin-left:8px;"><span class="dashicons dashicons-edit"></span></button>');
                        $plusBtn.after($editBtn);
                    }
                }
            }
            // Call on ready and after AJAX reloads
            $(document).on('ready programViewLoaded', injectEditButton);
            $(document).on('ajaxComplete', function(e, xhr, settings) {
                if (settings && settings.data && settings.data.includes('get_program_full_view')) {
                    injectEditButton();
                }
            });

            // --- BEGIN: Inject Remove Enrollment (Edit) Button for Courses ---
            function injectCourseEnrollmentEditButton() {
                $('.course-detail-enrollments-actions').each(function() {
                    var $actions = $(this);
                    if ($actions.find('.edit-course-enrollment-btn').length === 0) {
                        $actions.append('<button class="edit-course-enrollment-btn button" style="margin-left:8px;"><span class="dashicons dashicons-edit"></span></button>');
                    }
                });
            }
            // Call this after course enrollments are loaded or reloaded
            $(document).on('ready courseDetailLoaded', injectCourseEnrollmentEditButton);
            $(document).on('ajaxComplete', function(e, xhr, settings) {
                if (settings && settings.data && settings.data.includes('get_course_detail_tabs')) {
                    injectCourseEnrollmentEditButton();
                }
            });
            // --- END: Inject Remove Enrollment (Edit) Button for Courses ---
        }
    };
    $(document).ready(function() {
        ProgramView.init();
    });
})(jQuery); 