jQuery(document).ready(function($) {
    'use strict';

    console.log('Administration plugin initialized');
    
    // Debug: Check if we can find our elements
    console.log('Sidebar menu items found:', $('.sidebar-menu li').length);
    console.log('Page content sections found:', $('.page-content').length);
    
    // Initialize sidebar state
    const $sidebar = $('.administration-sidebar');
    const $main = $('.administration-main');
    const $menuToggle = $('.menu-toggle');
    
    // Set initial state (menu expanded)
    $main.addClass('collapsed').removeClass('expanded');
    
    // Menu Toggle Functionality
    $menuToggle.on('click', function() {
        // Toggle menu button state
        $(this).toggleClass('active');
        
        // Toggle sidebar state
        $sidebar.toggleClass('collapsed');
        
        // Toggle main content state
        $main.toggleClass('menu-collapsed');
    });
    
    // Page Switching Functionality
    $(document).on('click', '.sidebar-menu li a', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $menuItem = $(this).parent('li');
        const pageId = $menuItem.data('page');
        
        console.log('Menu item clicked:', pageId);
        
        // Update active states
        $('.sidebar-menu li').removeClass('active');
        $menuItem.addClass('active');
        
        // Hide all pages first
        $('.page-content').hide();
        console.log('Hidden all pages');
        
        // Show selected page
        const $targetPage = $('#' + pageId + '-page');
        console.log('Target page found:', $targetPage.length > 0);
        
        if ($targetPage.length) {
            $targetPage.fadeIn(200);
            console.log('Showed page:', pageId);
            
            // Update search placeholder
            $('.administration-search input').attr(
                'placeholder',
                'Search ' + pageId.charAt(0).toUpperCase() + pageId.slice(1)
            );
        } else {
            console.error('Target page not found:', pageId);
        }
        
        return false;
    });
    
    // Add button handlers - but exclude HR module buttons
    $('.add-button').not('#add-job-posting').on('click', function() {
        const section = $(this).closest('.administration-section');
        const sectionTitle = section.find('h3').text().trim();
        alert('Add ' + sectionTitle + ' feature coming soon!');
    });

    // Add Job Posting Handler
    $('#add-job-posting').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#job-posting-modal').show();
    });
    
    // Form Submission Handler
    $('#job-posting-form').off('submit').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        
        // Disable submit button and show loading state
        $submitButton.prop('disabled', true).text('Saving...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: administrationData.ajax_url,
            type: 'POST',
            data: {
                action: 'save_job_posting',
                nonce: administrationData.nonce,
                title: formData.get('title'),
                description: formData.get('description'),
                requirements: formData.get('requirements'),
                department: formData.get('departmentName'),
                location: formData.get('location'),
                type: formData.get('jobType')
            },
            success: function(response) {
                if (response.success) {
                    // Hide the form modal
                    $('#job-posting-modal').hide();
                    $form[0].reset();
                    
                    // Show success message
                    alert('Job posting saved successfully!');
                    
                    // View the newly created job posting
                    viewJobPosting(response.data.id);
                } else {
                    alert('Error saving job posting: ' + (response.data?.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Error saving job posting. Please try again.');
            },
            complete: function() {
                // Re-enable submit button and restore text
                $submitButton.prop('disabled', false).text('Save Job Posting');
            }
        });
    });
    
    // Initialize the active page
    const $activePage = $('.sidebar-menu li.active');
    if ($activePage.length) {
        $activePage.find('a').trigger('click');
    } else {
        $('.sidebar-menu li:first-child a').trigger('click');
    }

    // Initialize HR Module
    initHRModule();

    // HR Module Functionality
    function initHRModule() {
        // HR Dashboard Navigation
        $('.hr-dashboard-item').off('click').on('click', function() {
            const section = $(this).data('section');
            console.log('HR dashboard item clicked:', section);
            
            switch(section) {
                case 'jobs':
                    // Hide dashboard, show jobs section
                    $('.hr-dashboard-grid').closest('.administration-section').hide();
                    $('#hr-jobs-section').show();
                    
                    // Load initial jobs data
                    loadJobPostings();
                    loadApplications();
                    loadInterviews();
                    loadOffers();
                    break;
                    
                case 'employees':
                    alert('Employee management module coming soon!');
                    break;
                    
                case 'timesheets':
                    alert('Timesheet management module coming soon!');
                    break;
            }
        });

        // Back button functionality
        function addBackButton() {
            // Remove any existing back button first
            $('#hr-jobs-section .back-button').remove();
            
            const $backButton = $('<button class="back-button"><i class="dashicons dashicons-arrow-left-alt"></i> Back to Dashboard</button>');
            $('#hr-jobs-section').find('.section-header').first().prepend($backButton);
            
            $backButton.off('click').on('click', function(e) {
                e.preventDefault();
                $('#hr-jobs-section').hide();
                $('.hr-dashboard-grid').closest('.administration-section').show();
            });
        }
        addBackButton();

        // Modal close handlers
        $('.modal .close').off('click').on('click', function() {
            $(this).closest('.modal').hide();
        });

        // Close modal when clicking outside
        $('.modal').off('click').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        // Prevent modal content clicks from bubbling to the modal backdrop
        $('.modal-content').off('click').on('click', function(e) {
            e.stopPropagation();
        });

        // Load initial data when HR page is active
        $(document).off('pageChanged.hr').on('pageChanged.hr', function(e, page) {
            if (page === 'hr') {
                // Show dashboard, hide jobs section initially
                $('.hr-dashboard-grid').closest('.administration-section').show();
                $('#hr-jobs-section').hide();
            }
        });

        // Debug logging
        console.log('HR Module initialized');
        console.log('HR dashboard items found:', $('.hr-dashboard-item').length);

        // Job Postings
        function loadJobPostings() {
            const $container = $('.job-postings-list');
            $container.html('<div class="empty-state">Loading job postings...</div>');
            
            $.ajax({
                url: administrationData.ajax_url,
                type: 'GET',
                data: {
                    action: 'get_job_postings',
                    nonce: administrationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayJobPostings(response.data);
                    } else {
                        $container.html('<div class="empty-state">Error loading job postings. Please try again.</div>');
                    }
                },
                error: function() {
                    $container.html('<div class="empty-state">Error loading job postings. Please try again.</div>');
                }
            });
        }

        function displayJobPostings(postings) {
            const $container = $('.job-postings-list');
            $container.empty();

            if (postings && postings.length > 0) {
                postings.forEach(posting => {
                    const $posting = $(`
                        <div class="job-posting-item" data-id="${posting.JobPostingID}">
                            <div class="job-posting-header">
                                <h4>${posting.Title}</h4>
                                <span class="status ${posting.Status.toLowerCase()}">${posting.Status}</span>
                            </div>
                            <div class="job-posting-details">
                                <p><strong>Department:</strong> ${posting.DepartmentName}</p>
                                <p><strong>Type:</strong> ${posting.JobType}</p>
                                <p><strong>Location:</strong> ${posting.Location}</p>
                                <p><strong>Posted:</strong> ${new Date(posting.PostedDate).toLocaleDateString()}</p>
                            </div>
                            <div class="job-posting-actions">
                                <button class="button view-posting" title="View Details">
                                    <i class="dashicons dashicons-visibility"></i> View
                                </button>
                                <button class="button edit-posting" title="Edit Posting">
                                    <i class="dashicons dashicons-edit"></i> Edit
                                </button>
                                <button class="button toggle-status" title="Toggle Status">
                                    <i class="dashicons dashicons-marker"></i> Change Status
                                </button>
                            </div>
                        </div>
                    `);
                    $container.append($posting);
                });

                // Add event handlers for the new buttons
                $('.view-posting').off('click').on('click', function(e) {
                    e.preventDefault();
                    const jobId = $(this).closest('.job-posting-item').data('id');
                    viewJobPosting(jobId);
                });

                $('.edit-posting').off('click').on('click', function(e) {
                    e.preventDefault();
                    const jobId = $(this).closest('.job-posting-item').data('id');
                    editJobPosting(jobId);
                });

                $('.toggle-status').off('click').on('click', function(e) {
                    e.preventDefault();
                    const jobId = $(this).closest('.job-posting-item').data('id');
                    toggleJobStatus(jobId);
                });
            } else {
                $container.append('<div class="empty-state">No job postings found. Click "Add Job Posting" to create one.</div>');
            }
        }

        // View Job Posting
        function viewJobPosting(jobId) {
            $.ajax({
                url: administrationData.ajax_url,
                type: 'GET',
                data: {
                    action: 'get_job_posting',
                    nonce: administrationData.nonce,
                    id: jobId
                },
                success: function(response) {
                    if (response.success) {
                        showViewModal(response.data);
                    } else {
                        alert('Error loading job posting details: ' + (response.data?.message || 'Unknown error'));
                    }
                },
                error: function() {
                    alert('Error loading job posting details. Please try again.');
                }
            });
        }

        // Edit Job Posting
        function editJobPosting(jobId) {
            $.ajax({
                url: administrationData.ajax_url,
                type: 'GET',
                data: {
                    action: 'get_job_posting',
                    nonce: administrationData.nonce,
                    id: jobId
                },
                success: function(response) {
                    if (response.success) {
                        showEditModal(response.data);
                    } else {
                        alert('Error loading job posting for editing: ' + (response.data?.message || 'Unknown error'));
                    }
                },
                error: function() {
                    alert('Error loading job posting for editing. Please try again.');
                }
            });
        }

        // Toggle Job Status
        function toggleJobStatus(jobId) {
            const statusOptions = ['Open', 'Closed', 'On Hold', 'Draft'];
            const $statusModal = $(`
                <div class="modal status-modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Change Job Posting Status</h2>
                        <form id="status-change-form">
                            <div class="form-group">
                                <label for="job-status">Status</label>
                                <select id="job-status" name="status" required>
                                    ${statusOptions.map(status => `<option value="${status}">${status}</option>`).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status-note">Note (Optional)</label>
                                <textarea id="status-note" name="statusNote" rows="3"></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="button button-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            // Add the modal to the page
            $('body').append($statusModal);
            $statusModal.show();

            // Handle form submission
            $('#status-change-form').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                $.ajax({
                    url: administrationData.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_job_status',
                        nonce: administrationData.nonce,
                        job_id: jobId,
                        status: formData.get('status'),
                        note: formData.get('statusNote')
                    },
                    success: function(response) {
                        if (response.success) {
                            $statusModal.remove();
                            loadJobPostings(); // Refresh the list
                        } else {
                            alert('Error updating status: ' + (response.data?.message || 'Unknown error'));
                        }
                    },
                    error: function() {
                        alert('Error updating status. Please try again.');
                    }
                });
            });

            // Handle modal close
            $statusModal.find('.close').on('click', function() {
                $statusModal.remove();
            });
        }

        // Show View Modal
        function showViewModal(posting) {
            const $viewModal = $(`
                <div class="modal view-modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <div class="job-posting-view">
                            <div class="view-header">
                                <h2>${posting.Title}</h2>
                                <span class="status ${posting.Status.toLowerCase()}">${posting.Status}</span>
                            </div>
                            <div class="view-content">
                                <div class="view-section">
                                    <h3>Job Details</h3>
                                    <p><strong>Department:</strong> ${posting.Department}</p>
                                    <p><strong>Type:</strong> ${posting.JobType}</p>
                                    <p><strong>Location:</strong> ${posting.Location}</p>
                                    <p><strong>Salary Range:</strong> ${posting.SalaryRange}</p>
                                    <p><strong>Posted Date:</strong> ${new Date(posting.PostedDate).toLocaleDateString()}</p>
                                    <p><strong>Closing Date:</strong> ${new Date(posting.ClosingDate).toLocaleDateString()}</p>
                                    <p><strong>Internal Only:</strong> ${posting.IsInternal ? 'Yes' : 'No'}</p>
                                </div>
                                <div class="view-section">
                                    <h3>Description</h3>
                                    <div class="content-box">${posting.Description}</div>
                                </div>
                                <div class="view-section">
                                    <h3>Requirements</h3>
                                    <div class="content-box">${posting.Requirements}</div>
                                </div>
                                <div class="view-section">
                                    <h3>Responsibilities</h3>
                                    <div class="content-box">${posting.Responsibilities}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Add the modal to the page
            $('body').append($viewModal);
            $viewModal.show();

            // Handle modal close
            $viewModal.find('.close').on('click', function() {
                $viewModal.remove();
            });
        }

        // Show Edit Modal
        function showEditModal(posting) {
            // Clone the add job posting modal and modify it for editing
            const $editModal = $('#job-posting-modal').clone()
                .attr('id', 'job-posting-edit-modal')
                .addClass('edit-mode');

            // Update the modal title
            $editModal.find('h2').text('Edit Job Posting');

            // Populate the form with existing data
            const $form = $editModal.find('form');
            $form.attr('id', 'job-posting-edit-form');
            
            // Populate form fields
            $form.find('#title').val(posting.Title);
            $form.find('#description').val(posting.Description);
            $form.find('#requirements').val(posting.Requirements);
            $form.find('#responsibilities').val(posting.Responsibilities);
            $form.find('#job-type').val(posting.JobType);
            $form.find('#location').val(posting.Location);
            $form.find('#salary-range').val(posting.SalaryRange);
            $form.find('#department').val(posting.Department);
            $form.find('#posted-date').val(posting.PostedDate.split('T')[0]);
            $form.find('#closing-date').val(posting.ClosingDate.split('T')[0]);
            $form.find('input[name="isInternal"]').prop('checked', posting.IsInternal);

            // Add the modal to the page
            $('body').append($editModal);
            $editModal.show();

            // Handle form submission
            $form.off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                $.ajax({
                    url: administrationData.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'update_job_posting',
                        nonce: administrationData.nonce,
                        job_id: posting.JobID,
                        ...Object.fromEntries(formData.entries())
                    },
                    success: function(response) {
                        if (response.success) {
                            $editModal.remove();
                            loadJobPostings(); // Refresh the list
                        } else {
                            alert('Error updating job posting: ' + (response.data?.message || 'Unknown error'));
                        }
                    },
                    error: function() {
                        alert('Error updating job posting. Please try again.');
                    }
                });
            });

            // Handle modal close
            $editModal.find('.close').on('click', function() {
                $editModal.remove();
            });
        }

        // Applications
        function loadApplications() {
            $.ajax({
                url: administrationData.ajax_url,
                type: 'GET',
                data: {
                    action: 'get_applications',
                    nonce: administrationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayApplications(response.data);
                    }
                }
            });
        }

        function displayApplications(applications) {
            const $container = $('.applications-list');
            $container.empty();

            applications.forEach(app => {
                const $app = $(`
                    <div class="application-item" data-id="${app.ApplicationID}">
                        <div class="application-header">
                            <h4>${app.FirstName} ${app.LastName}</h4>
                            <span class="status ${app.Status.toLowerCase()}">${app.Status}</span>
                        </div>
                        <div class="application-details">
                            <p>Applied for: ${app.JobTitle}</p>
                            <p>Date: ${new Date(app.SubmissionDate).toLocaleDateString()}</p>
                        </div>
                        <div class="application-actions">
                            <button class="view-application">View</button>
                        </div>
                    </div>
                `);
                $container.append($app);
            });
        }

        // Interviews
        function loadInterviews() {
            $.ajax({
                url: administrationData.ajax_url,
                type: 'GET',
                data: {
                    action: 'get_interviews',
                    nonce: administrationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayInterviews(response.data);
                    }
                }
            });
        }

        function displayInterviews(interviews) {
            const $container = $('.interviews-list');
            $container.empty();

            interviews.forEach(interview => {
                const $interview = $(`
                    <div class="interview-item" data-id="${interview.InterviewID}">
                        <div class="interview-header">
                            <h4>${interview.CandidateName}</h4>
                            <span class="status ${interview.Status.toLowerCase()}">${interview.Status}</span>
                        </div>
                        <div class="interview-details">
                            <p>Round: ${interview.InterviewRound}</p>
                            <p>Date: ${new Date(interview.ScheduledDateTime).toLocaleString()}</p>
                            <p>Type: ${interview.InterviewType}</p>
                        </div>
                    </div>
                `);
                $container.append($interview);
            });
        }
    }
});