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
        // Job Postings
        $('#add-job-posting').on('click', function() {
            $('#job-posting-modal').show();
        });

        $('#job-posting-form').on('submit', function(e) {
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

        // Modal Handlers
        $('.close').on('click', function() {
            $(this).closest('.modal').hide();
        });

        $('#schedule-interview').on('click', function() {
            $('#interview-modal').show();
        });

        $('#make-offer').on('click', function() {
            $('#offer-modal').show();
        });

        // Interview Form Handler
        $('#interview-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'schedule_interview',
                    nonce: administrationData.nonce,
                    ...data
                },
                success: function(response) {
                    if (response.success) {
                        loadInterviews();
                        $('#interview-modal').hide();
                        this.reset();
                    } else {
                        alert('Error scheduling interview: ' + response.data.message);
                    }
                }.bind(this)
            });
        });

        // Offer Form Handler
        $('#offer-form').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'make_offer',
                    nonce: administrationData.nonce,
                    ...data
                },
                success: function(response) {
                    if (response.success) {
                        loadOffers();
                        $('#offer-modal').hide();
                        this.reset();
                    } else {
                        alert('Error making offer: ' + response.data.message);
                    }
                }.bind(this)
            });
        });

        // Load initial data when HR page is active
        $(document).on('pageChanged', function(e, page) {
            if (page === 'hr') {
                loadJobPostings();
                loadApplications();
                loadInterviews();
                loadOffers();
            }
        });
    }

    // Initialize HR Module when document is ready
    initHRModule();
});