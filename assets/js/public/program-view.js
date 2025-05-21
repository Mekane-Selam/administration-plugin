// Program View JS Module
(function($) {
    'use strict';
    window.ProgramView = {
        currentProgramId: null,
        
        show: function(programId) {
            this.currentProgramId = programId;
            var $container = $('#program-view-container');
            $container.html('<div class="loading">Loading program...</div>').show();
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
            this.currentProgramId = null;
            $('#program-view-container').hide().empty();
            $('.administration-public-dashboard').show();
        },
        init: function() {
            // Bind Go Back button
            $(document).on('click', '.program-view-back-btn', function(e) {
                e.preventDefault();
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
                var data = $form.serializeArray();
                data.push({name: 'action', value: 'edit_program'});
                data.push({name: 'nonce', value: administration_plugin.nonce});
                data.push({name: 'program_id', value: ProgramView.currentProgramId});
                
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            ProgramView.show(ProgramView.currentProgramId);
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
                var modalHtml = `
                    <div id="add-course-modal" class="modal show">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Add Course</h2>
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
                `;
                $('body').append(modalHtml);
            });
            // Add Course Cancel
            $(document).on('click', '#cancel-add-course, #add-course-modal .close', function(e) {
                e.preventDefault();
                $('#add-course-modal').remove();
            });
            // Add Course Save
            $(document).on('submit', '#add-course-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-course-message');
                var data = $form.serializeArray();
                data.push({name: 'action', value: 'add_edu_course'});
                data.push({name: 'nonce', value: administration_plugin.nonce});
                data.push({name: 'program_id', value: ProgramView.currentProgramId});

                $message.html('<span class="loading">Adding course...</span>');

                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">Course added successfully!</span>');
                            setTimeout(function() {
                                $('#add-course-modal').remove();
                                // Reload program view to show new course
                                ProgramView.show(ProgramView.currentProgramId);
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add course.') + '</span>');
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add course.</span>');
                    }
                });
            });
            // Add Enrollment button
            $(document).on('click', '.program-view-edu-add-enrollment-btn', function(e) {
                e.preventDefault();
                var modalHtml = `
                    <div id="add-enrollment-modal" class="modal show">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Add Enrollment</h2>
                            <form id="add-enrollment-form">
                                <div class="form-field">
                                    <label for="enrollment-person">Person</label>
                                    <select id="enrollment-person" name="PersonID" required>
                                        <option value="">Select Person</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label for="enrollment-course">Course</label>
                                    <select id="enrollment-course" name="CourseID" required>
                                        <option value="">Select Course</option>
                                    </select>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">Add Enrollment</button>
                                    <button type="button" class="button" id="cancel-add-enrollment">Cancel</button>
                                </div>
                            </form>
                            <div id="add-enrollment-message"></div>
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);

                // Load people for select
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_people_for_owner_select',
                        nonce: administration_plugin.nonce
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            var options = '<option value="">Select Person</option>';
                            response.data.forEach(function(person) {
                                options += `<option value="${person.PersonID}">${person.FirstName} ${person.LastName}</option>`;
                            });
                            $('#enrollment-person').html(options);
                        }
                    }
                });

                // Load courses for select
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_program_courses',
                        nonce: administration_plugin.nonce,
                        program_id: ProgramView.currentProgramId
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data)) {
                            var options = '<option value="">Select Course</option>';
                            response.data.forEach(function(course) {
                                options += `<option value="${course.CourseID}">${course.CourseName}</option>`;
                            });
                            $('#enrollment-course').html(options);
                        }
                    }
                });
            });
            // Add Enrollment Cancel
            $(document).on('click', '#cancel-add-enrollment, #add-enrollment-modal .close', function(e) {
                e.preventDefault();
                $('#add-enrollment-modal').remove();
            });
            // Add Enrollment Save
            $(document).on('submit', '#add-enrollment-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $message = $('#add-enrollment-message');
                var data = $form.serializeArray();
                data.push({name: 'action', value: 'add_edu_enrollment'});
                data.push({name: 'nonce', value: administration_plugin.nonce});
                data.push({name: 'program_id', value: ProgramView.currentProgramId});

                $message.html('<span class="loading">Adding enrollment...</span>');

                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            $message.html('<span class="success-message">Enrollment added successfully!</span>');
                            setTimeout(function() {
                                $('#add-enrollment-modal').remove();
                                // Reload program view to show new enrollment
                                ProgramView.show(ProgramView.currentProgramId);
                            }, 800);
                        } else {
                            $message.html('<span class="error-message">' + (response.data || 'Failed to add enrollment.') + '</span>');
                        }
                    },
                    error: function() {
                        $message.html('<span class="error-message">Failed to add enrollment.</span>');
                    }
                });
            });
        }
    };
    $(document).ready(function() {
        ProgramView.init();
    });
})(jQuery); 