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
    
    // Add button handlers
    $('.add-button').on('click', function() {
        const section = $(this).closest('.administration-section');
        const sectionTitle = section.find('h3').text().trim();
        alert('Add ' + sectionTitle + ' feature coming soon!');
    });
    
    // Initialize the active page
    const $activePage = $('.sidebar-menu li.active');
    if ($activePage.length) {
        $activePage.find('a').trigger('click');
    } else {
        $('.sidebar-menu li:first-child a').trigger('click');
    }

    // HR Module Functionality
    function initHRModule() {
        // HR Dashboard Navigation
        $('.hr-dashboard-item').on('click', function() {
            const section = $(this).data('section');
            if (section === 'jobs') {
                $('.hr-dashboard-grid').closest('.administration-section').hide();
                $('#hr-jobs-section').show();
                
                // Load initial jobs data
                loadJobPostings();
                loadApplications();
                loadInterviews();
                loadOffers();
            }
            // Other sections (employees, timesheets) will be handled similarly
        });

        // Job Postings
        function loadJobPostings() {
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'get_job_postings',
                    nonce: administrationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayJobPostings(response.data);
                    }
                }
            });
        }

        function displayJobPostings(postings) {
            const $container = $('.job-postings-list');
            $container.empty();

            if (postings && postings.length > 0) {
                postings.forEach(posting => {
                    const $posting = $(`
                        <div class="job-posting-item" data-id="${posting.JobID}">
                            <div class="job-posting-header">
                                <h4>${posting.Title}</h4>
                                <span class="status ${posting.Status.toLowerCase()}">${posting.Status}</span>
                            </div>
                            <div class="job-posting-details">
                                <p><strong>Department:</strong> ${posting.Department}</p>
                                <p><strong>Type:</strong> ${posting.JobType}</p>
                                <p><strong>Location:</strong> ${posting.Location}</p>
                                <p><strong>Posted:</strong> ${new Date(posting.PostedDate).toLocaleDateString()}</p>
                            </div>
                            <div class="job-posting-actions">
                                <button class="edit-posting">Edit</button>
                                <button class="view-applications">View Applications</button>
                            </div>
                        </div>
                    `);
                    $container.append($posting);
                });
            } else {
                $container.append('<div class="empty-state">No job postings found. Click "Add Job Posting" to create one.</div>');
            }
        }

        $('#add-job-posting').off('click').on('click', function() {
            $('#job-posting-modal').show();
        });

        $('#job-posting-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_job_posting',
                    nonce: administrationData.nonce,
                    ...data
                },
                success: function(response) {
                    if (response.success) {
                        loadJobPostings();
                        $('#job-posting-modal').hide();
                        this.reset();
                    } else {
                        alert('Error saving job posting: ' + response.data.message);
                    }
                }.bind(this)
            });
        });

        // Applications
        function loadApplications() {
            $.ajax({
                url: ajaxurl,
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
                url: ajaxurl,
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

        // Offers
        function loadOffers() {
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'get_offers',
                    nonce: administrationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        displayOffers(response.data);
                    }
                }
            });
        }

        function displayOffers(offers) {
            const $container = $('.offers-list');
            $container.empty();

            offers.forEach(offer => {
                const $offer = $(`
                    <div class="offer-item" data-id="${offer.OfferID}">
                        <div class="offer-header">
                            <h4>${offer.CandidateName}</h4>
                            <span class="status ${offer.Status.toLowerCase()}">${offer.Status}</span>
                        </div>
                        <div class="offer-details">
                            <p>Position: ${offer.Position}</p>
                            <p>Department: ${offer.Department}</p>
                            <p>Salary: ${offer.SalaryOffered}</p>
                            <p>Start Date: ${new Date(offer.StartDate).toLocaleDateString()}</p>
                        </div>
                    </div>
                `);
                $container.append($offer);
            });
        }

        // Back button functionality
        function addBackButton() {
            const $backButton = $('<button class="back-button"><i class="dashicons dashicons-arrow-left-alt"></i> Back to Dashboard</button>');
            $('#hr-jobs-section').find('.section-header').first().prepend($backButton);
            
            $backButton.on('click', function() {
                $('#hr-jobs-section').hide();
                $('.hr-dashboard-grid').closest('.administration-section').show();
            });
        }
        addBackButton();

        // Modal close handlers
        $('.modal .close').on('click', function() {
            $(this).closest('.modal').hide();
        });

        // Close modal when clicking outside
        $('.modal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        // Load initial data when HR page is active
        $(document).on('pageChanged', function(e, page) {
            if (page === 'hr') {
                // Show dashboard, hide jobs section initially
                $('.hr-dashboard-grid').closest('.administration-section').show();
                $('#hr-jobs-section').hide();
            }
        });
    }

    // Initialize HR Module when document is ready
    initHRModule();
});