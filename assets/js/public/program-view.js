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
                        // Optionally, populate .program-data with program info
                        if (response.data.program) {
                            var program = response.data.program;
                            $container.find('.program-data').html(
                                '<div><strong>Name:</strong> ' + program.ProgramName + '</div>' +
                                '<div><strong>Type:</strong> ' + program.ProgramType + '</div>' +
                                '<div><strong>Description:</strong> ' + program.ProgramDescription + '</div>' +
                                '<div><strong>Start Date:</strong> ' + program.StartDate + '</div>' +
                                '<div><strong>End Date:</strong> ' + program.EndDate + '</div>' +
                                '<div><strong>Status:</strong> ' + (program.ActiveFlag ? 'Active' : 'Inactive') + '</div>'
                            );
                            // Add placeholder for courses list if education type
                            if (program.ProgramType && program.ProgramType.toLowerCase() === 'education') {
                                if ($container.find('.program-courses-list-placeholder').length === 0) {
                                    $container.find('.program-type-education').append('<div class="program-courses-list-placeholder">[Courses list will appear here]</div>');
                                }
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
        }
    };
    $(document).ready(function() {
        ProgramView.init();
    });
})(jQuery); 