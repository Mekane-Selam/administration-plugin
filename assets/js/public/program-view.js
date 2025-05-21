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
                                <label for="enrollment-person">Person</label>
                                <input type="text" id="enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                                <select id="enrollment-person" name="PersonID" required size="6" style="margin-top:8px;"></select>
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

            // Add Enrollment button
            $(document).on('click', '.program-view-edu-add-enrollment-btn', function(e) {
                e.preventDefault();
                var programId = $('#program-view-container').data('program-id');
                // Remove course dropdown and add search/filter for person
                $('#add-enrollment-form .form-field').remove();
                $('#add-enrollment-form').prepend(`
                    <div class="form-field">
                        <label for="enrollment-person">Person</label>
                        <input type="text" id="enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                        <select id="enrollment-person" name="PersonID" required size="6" style="margin-top:8px;"></select>
                    </div>
                `);
                // Load people for select
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
                                var options = '';
                                response.data.forEach(function(person) {
                                    var fullName = person.FirstName + ' ' + person.LastName;
                                    if (!query || fullName.toLowerCase().includes(query.toLowerCase())) {
                                        options += `<option value="${person.PersonID}">${fullName}</option>`;
                                    }
                                });
                                $('#enrollment-person').html(options);
                            }
                        }
                    });
                }
                loadPeopleList('');
                $('#enrollment-person-search').off('input').on('input', function() {
                    loadPeopleList($(this).val());
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

            // Add Enrollment form submission
            $(document).on('submit', '#add-enrollment-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-enrollment-message');
                var programId = $('#program-view-container').data('program-id');
                var personId = $('#enrollment-person').val();
                var data = {
                    action: 'add_edu_enrollment',
                    nonce: administration_plugin.nonce,
                    program_id: programId,
                    PersonID: personId
                };
                if (!data.PersonID) {
                    $message.html('<span class="error-message">Person is required.</span>');
                    return;
                }
                $message.html('<span class="loading">Adding enrollment...</span>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">Enrollment added successfully!</span>');
                            setTimeout(function() {
                                $('#add-enrollment-modal').removeClass('show');
                                $form[0].reset();
                                $message.html('');
                                // Reload only the enrollment list
                                ProgramView.reloadEnrollmentList(programId);
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add enrollment.') + '</span>');
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
                var courseId = null;
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

            // Add Course Enrollment button in course detail modal
            $(document).on('click', '#course-detail-modal .add-course-enrollment-btn', function(e) {
                e.preventDefault();
                var programId = $('#program-view-container').data('program-id');
                // Remove any previous form fields
                if ($('#add-course-enrollment-modal').length === 0) {
                    $('body').append(`
                        <div id="add-course-enrollment-modal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2>Add Course Enrollment</h2>
                                <form id="add-course-enrollment-form">
                                    <div class="form-field">
                                        <label for="course-enrollment-person">Person</label>
                                        <input type="text" id="course-enrollment-person-search" placeholder="Search people..." autocomplete="off" />
                                        <select id="course-enrollment-person" name="PersonID" required size="6" style="margin-top:8px;"></select>
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
                // Load people for select (only those enrolled in the parent program)
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
                                var options = '';
                                response.data.forEach(function(person) {
                                    var fullName = person.FirstName + ' ' + person.LastName;
                                    if (!query || fullName.toLowerCase().includes(query.toLowerCase())) {
                                        options += `<option value="${person.PersonID}">${fullName}</option>`;
                                    }
                                });
                                $('#course-enrollment-person').html(options);
                            }
                        }
                    });
                }
                loadPeopleList('');
                $('#course-enrollment-person-search').off('input').on('input', function() {
                    loadPeopleList($(this).val());
                });
                $('#add-course-enrollment-modal').addClass('show');
                $('#add-course-enrollment-form')[0].reset();
                $('#add-course-enrollment-message').html('');
            });
            // Close add course enrollment modal
            $(document).on('click', '#add-course-enrollment-modal .close, #cancel-add-course-enrollment', function() {
                $('#add-course-enrollment-modal').removeClass('show');
            });
            // Add Course Enrollment form submission
            $(document).on('submit', '#add-course-enrollment-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-course-enrollment-message');
                var courseId = $('#course-detail-modal .course-card').data('course-id') || $('#course-detail-modal').find('[data-course-id]').first().data('course-id');
                var personId = $('#course-enrollment-person').val();
                var today = new Date();
                var enrollmentDate = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                // Generate unique CourseEnrollmentID (client-side, for now)
                var courseEnrollmentId = 'CORENROL' + Math.floor(10000 + Math.random() * 90000);
                if (!personId) {
                    $message.html('<span class="error-message">Person is required.</span>');
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
                        PersonID: personId,
                        ActiveFlag: 1,
                        EnrollmentDate: enrollmentDate,
                        CourseEnrollmentID: courseEnrollmentId
                    },
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">Enrollment added successfully!</span>');
                            setTimeout(function() {
                                $('#add-course-enrollment-modal').removeClass('show');
                                $form[0].reset();
                                $message.html('');
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
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add enrollment. Please try again.</span>');
                    }
                });
            });
        }
    };
    $(document).ready(function() {
        ProgramView.init();
    });
})(jQuery); 