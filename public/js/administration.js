(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize the application
        initApp();
    });

    function initApp() {
        // Fetch initial data from API
        fetchPrograms();
        fetchPersons();

        // Set up event listeners
        setupEventListeners();
    }

    function fetchPrograms() {
        $.ajax({
            url: administration_data.rest_url + 'programs',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', administration_data.nonce);
            },
            success: function(response) {
                renderPrograms(response);
            },
            error: function(error) {
                console.error('Error fetching programs:', error);
            }
        });
    }

    function fetchPersons() {
        $.ajax({
            url: administration_data.rest_url + 'persons',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', administration_data.nonce);
            },
            success: function(response) {
                renderPersons(response);
            },
            error: function(error) {
                console.error('Error fetching persons:', error);
            }
        });
    }

    function renderPrograms(programs) {
        const $programsContainer = $('.administration-section:nth-child(1) .section-content');
        
        if (programs.length === 0) {
            $programsContainer.html('<div class="empty-state"><p>No programs added yet.</p></div>');
            return;
        }
        
        let html = '';
        programs.forEach(function(program) {
            html += `
                <div class="program-card" data-id="${program.ProgramID}">
                    <h4>${program.ProgramName}</h4>
                    <p>${program.ProgramDescription || ''}</p>
                </div>
            `;
        });
        
        $programsContainer.html(html);
    }

    function renderPersons(persons) {
        const $personsContainer = $('.administration-section:nth-child(2) .section-content');
        
        if (persons.length === 0) {
            $personsContainer.html('<div class="empty-state"><p>No members added yet.</p></div>');
            return;
        }
        
        let html = '<div class="members-list">';
        persons.forEach(function(person) {
            html += `
                <div class="member-item" data-id="${person.PersonID}">
                    ${person.FirstName} ${person.LastName}
                </div>
            `;
        });
        html += '</div>';
        
        $personsContainer.html(html);
    }

    function setupEventListeners() {
        // Add program button
        $('.administration-section:nth-child(1) .add-button').on('click', function() {
            openProgramModal();
        });
        
        // Add person button
        $('.administration-section:nth-child(2) .add-button').on('click', function() {
            openPersonModal();
        });
        
        // Add role button
        $('.administration-section:nth-child(3) .add-button').on('click', function() {
            openRoleModal();
        });
        
        // Sidebar navigation
        $('.administration-sidebar li a').on('click', function(e) {
            e.preventDefault();
            $('.administration-sidebar li').removeClass('active');
            $(this).parent().addClass('active');
        });
    }

    function openProgramModal() {
        // Implement modal for adding/editing programs
        alert('Add Program feature coming soon!');
    }

    function openPersonModal() {
        // Implement modal for adding/editing persons
        alert('Add Person feature coming soon!');
    }

    function openRoleModal() {
        // Implement modal for adding/editing roles
        alert('Add Role feature coming soon!');
    }

})(jQuery);