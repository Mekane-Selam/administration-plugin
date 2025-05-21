// Program View JS Module
(function($) {
    'use strict';
    window.ProgramView = {
        show: function(programId) {
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
                var $card = $form.closest('.program-view-edu-overview');
                var data = $form.serializeArray();
                var programId = $('#program-view-container').data('program-id');
                data.push({name: 'action', value: 'edit_program'});
                data.push({name: 'nonce', value: administration_plugin.nonce});
                data.push({name: 'program_id', value: programId});
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
                var $card = $(this).closest('.program-view-edu-courses');
                $card.find('.add-course-form').show();
                $card.find('.program-courses-list-placeholder').hide();
            });
            // Add Course Cancel
            $(document).on('click', '.add-course-cancel-btn', function(e) {
                e.preventDefault();
                var $form = $(this).closest('.add-course-form');
                $form.hide();
                $form.siblings('.program-courses-list-placeholder').show();
            });
            // Add Course Save
            $(document).on('submit', '.add-course-form', function(e) {
                e.preventDefault();
                // TODO: AJAX to add course, then reload list
                alert('Course add functionality coming soon!');
                $(this).hide();
                $(this).siblings('.program-courses-list-placeholder').show();
            });
            // Add Enrollment button
            $(document).on('click', '.program-view-edu-add-enrollment-btn', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.program-view-edu-enrollment');
                $card.find('.add-enrollment-form').show();
                $card.find('.program-enrollment-list-placeholder').hide();
            });
            // Add Enrollment Cancel
            $(document).on('click', '.add-enrollment-cancel-btn', function(e) {
                e.preventDefault();
                var $form = $(this).closest('.add-enrollment-form');
                $form.hide();
                $form.siblings('.program-enrollment-list-placeholder').show();
            });
            // Add Enrollment Save
            $(document).on('submit', '.add-enrollment-form', function(e) {
                e.preventDefault();
                // TODO: AJAX to add enrollment, then reload list
                alert('Enrollment add functionality coming soon!');
                $(this).hide();
                $(this).siblings('.program-enrollment-list-placeholder').show();
            });
        }
    };
    $(document).ready(function() {
        ProgramView.init();
    });
})(jQuery); 