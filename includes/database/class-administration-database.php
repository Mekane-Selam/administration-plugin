<?php
/**
 * Database setup and management.
 */
class Administration_Database {

    /**
     * Setup database tables.
     */
    public static function setup_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Check if we have permission to create tables
        if (!current_user_can('activate_plugins')) {
            throw new Exception('Insufficient permissions to create database tables');
        }

        // Define table names
        $person_table = $wpdb->prefix . 'core_person';
        $person_relationships_table = $wpdb->prefix . 'core_person_relationships';
        $roles_table = $wpdb->prefix . 'core_roles';
        $person_roles_table = $wpdb->prefix . 'core_person_roles';
        $programs_table = $wpdb->prefix . 'core_programs';
        $program_enrollments_table = $wpdb->prefix . 'core_program_enrollments';
        $program_sessions_table = $wpdb->prefix . 'core_program_sessions';
        $program_checkins_table = $wpdb->prefix . 'core_program_checkins';
        $events_table = $wpdb->prefix . 'core_events';
        
        // Volunteer Operations tables
        $volunteers_table = $wpdb->prefix . 'volunteerops_volunteers';
        $volunteer_availability_table = $wpdb->prefix . 'volunteerops_volunteeravailability';
        $task_definitions_table = $wpdb->prefix . 'volunteerops_taskdefinitions';
        $task_groups_table = $wpdb->prefix . 'volunteerops_taskgroups';
        $task_group_tasks_table = $wpdb->prefix . 'volunteerops_taskgrouptasks';
        $shift_templates_table = $wpdb->prefix . 'volunteerops_shifttemplates';
        $shift_template_task_groups_table = $wpdb->prefix . 'volunteerops_shifttemplatetaskgroups';
        $shift_occurrences_table = $wpdb->prefix . 'volunteerops_shiftoccurrences';
        $shift_tasks_table = $wpdb->prefix . 'volunteerops_shifttasks';

        // HR tables
        $job_postings_table = $wpdb->prefix . 'hr_jobpostings';
        $applications_table = $wpdb->prefix . 'hr_applications';
        $external_applicants_table = $wpdb->prefix . 'hr_externalapplicants';
        $interview_schedules_table = $wpdb->prefix . 'hr_interviewschedules';
        $interviewers_table = $wpdb->prefix . 'hr_interviewers';
        $job_skills_table = $wpdb->prefix . 'hr_jobskills';
        $application_skills_table = $wpdb->prefix . 'hr_applicationskills';
        $job_workflows_table = $wpdb->prefix . 'hr_jobworkflows';
        $offers_table = $wpdb->prefix . 'hr_offers';

        // List of all tables
        $tables = [
            $person_table,
            $person_relationships_table,
            $roles_table,
            $person_roles_table,
            $programs_table,
            $program_enrollments_table,
            $program_sessions_table,
            $program_checkins_table,
            $events_table,
            $volunteers_table,
            $volunteer_availability_table,
            $task_definitions_table,
            $task_groups_table,
            $task_group_tasks_table,
            $shift_templates_table,
            $shift_template_task_groups_table,
            $shift_occurrences_table,
            $shift_tasks_table,
            $job_postings_table,
            $applications_table,
            $external_applicants_table,
            $interview_schedules_table,
            $interviewers_table,
            $job_skills_table,
            $application_skills_table,
            $job_workflows_table,
            $offers_table
        ];

        // Check if tables already exist
        $tables_exist = true;
        foreach ($tables as $table) {
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                $tables_exist = false;
                break;
            }
        }

        // If all tables exist, return early
        if ($tables_exist) {
            return;
        }

        // If we get here, at least one table is missing, so create any missing tables
        $sql = [];

        // Person table
        $sql[] = "CREATE TABLE IF NOT EXISTS $person_table (
            PersonID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            UserID BIGINT(20) UNSIGNED NULL,
            FirstName VARCHAR(50) NOT NULL,
            LastName VARCHAR(50) NOT NULL,
            Title VARCHAR(50),
            Gender VARCHAR(20),
            Email VARCHAR(100),
            Phone VARCHAR(20),
            AddressLine1 VARCHAR(100),
            AddressLine2 VARCHAR(100),
            City VARCHAR(50),
            State VARCHAR(50),
            Zip VARCHAR(20),
            Birthday DATE,
            MissingInfoFlag TINYINT(1) DEFAULT 0,
            PRIMARY KEY (PersonID),
            CONSTRAINT fk_person_user FOREIGN KEY (UserID) REFERENCES {$wpdb->users}(ID) ON DELETE SET NULL
        ) $charset_collate;";

        // Person Relationships table
        $sql[] = "CREATE TABLE IF NOT EXISTS $person_relationships_table (
            RelationshipID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            PersonID BIGINT(20) UNSIGNED NOT NULL,
            RelatedPersonID BIGINT(20) UNSIGNED NOT NULL,
            RelationshipType VARCHAR(50) NOT NULL,
            PRIMARY KEY (RelationshipID),
            CONSTRAINT fk_relationship_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE,
            CONSTRAINT fk_relationship_related_person FOREIGN KEY (RelatedPersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE
        ) $charset_collate;";

        // Roles table
        $sql[] = "CREATE TABLE IF NOT EXISTS $roles_table (
            RoleID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            RoleName VARCHAR(50) NOT NULL,
            PRIMARY KEY (RoleID)
        ) $charset_collate;";

        // Person Roles table
        $sql[] = "CREATE TABLE IF NOT EXISTS $person_roles_table (
            PersonRoleID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            PersonID BIGINT(20) UNSIGNED NOT NULL,
            RoleID BIGINT(20) UNSIGNED NOT NULL,
            ProgramID BIGINT(20) UNSIGNED NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            PRIMARY KEY (PersonRoleID),
            CONSTRAINT fk_person_role_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE,
            CONSTRAINT fk_person_role_role FOREIGN KEY (RoleID) REFERENCES $roles_table(RoleID) ON DELETE CASCADE
        ) $charset_collate;";

        // Programs table
        $sql[] = "CREATE TABLE IF NOT EXISTS $programs_table (
            ProgramID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ProgramName VARCHAR(100) NOT NULL,
            ProgramType VARCHAR(50) NOT NULL,
            ProgramDescription TEXT,
            ActiveFlag TINYINT(1) DEFAULT 1,
            StartDate DATE,
            EndDate DATE,
            ProgramOwner BIGINT(20) UNSIGNED NULL,
            PRIMARY KEY (ProgramID),
            CONSTRAINT fk_program_owner FOREIGN KEY (ProgramOwner) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // Program Enrollments table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_enrollments_table (
            EnrollmentID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            PersonID BIGINT(20) UNSIGNED NOT NULL,
            ProgramID BIGINT(20) UNSIGNED NOT NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            EnrollmentDate DATE,
            CompletionDate DATE,
            PRIMARY KEY (EnrollmentID),
            CONSTRAINT fk_enrollment_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE,
            CONSTRAINT fk_enrollment_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE CASCADE
        ) $charset_collate;";

        // Program Sessions table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_sessions_table (
            ProgramSessionID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ProgramID BIGINT(20) UNSIGNED NOT NULL,
            SessionDate DATE,
            StartTime TIME,
            EndTime TIME,
            Location VARCHAR(100),
            Notes TEXT,
            IsSelectedSession TINYINT(1) DEFAULT 0,
            PRIMARY KEY (ProgramSessionID),
            CONSTRAINT fk_session_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE CASCADE
        ) $charset_collate;";

        // Program Checkins table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_checkins_table (
            CheckInID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            PersonID BIGINT(20) UNSIGNED NOT NULL,
            ProgramID BIGINT(20) UNSIGNED NOT NULL,
            ProgramSessionID BIGINT(20) UNSIGNED NULL,
            CheckInDate DATE,
            CheckInTime TIME,
            CheckOutDate DATE,
            CheckOutTime TIME,
            PRIMARY KEY (CheckInID),
            CONSTRAINT fk_checkin_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE,
            CONSTRAINT fk_checkin_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE CASCADE,
            CONSTRAINT fk_checkin_session FOREIGN KEY (ProgramSessionID) REFERENCES $program_sessions_table(ProgramSessionID) ON DELETE SET NULL
        ) $charset_collate;";

        // Events table
        $sql[] = "CREATE TABLE IF NOT EXISTS $events_table (
            EventID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ProgramID BIGINT(20) UNSIGNED NULL,
            EventName VARCHAR(100) NOT NULL,
            EventType VARCHAR(50),
            StartDateTime DATETIME,
            EndDateTime DATETIME,
            Location VARCHAR(100),
            PRIMARY KEY (EventID),
            CONSTRAINT fk_event_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL
        ) $charset_collate;";

        // Volunteers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $volunteers_table (
            VolunteerID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            PersonID BIGINT(20) UNSIGNED NOT NULL,
            Skills TEXT,
            AvailabilityNotes TEXT,
            PRIMARY KEY (VolunteerID),
            CONSTRAINT fk_volunteer_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE CASCADE
        ) $charset_collate;";

        // Volunteer Availability Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $volunteer_availability_table (
            AvailabilityID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            VolunteerID BIGINT(20) UNSIGNED NOT NULL,
            StartDateTime DATETIME NOT NULL,
            EndDateTime DATETIME NOT NULL,
            AvailabilityType VARCHAR(50) NOT NULL,
            LastUpdated DATETIME NOT NULL,
            Notes TEXT,
            PRIMARY KEY (AvailabilityID),
            CONSTRAINT fk_availability_volunteer FOREIGN KEY (VolunteerID) REFERENCES $volunteers_table(VolunteerID) ON DELETE CASCADE
        ) $charset_collate;";

        // Task Definitions Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_definitions_table (
            TaskID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            TaskName VARCHAR(100) NOT NULL,
            TaskDescription TEXT,
            DefaultDuration INT UNSIGNED,
            PRIMARY KEY (TaskID)
        ) $charset_collate;";

        // Task Groups Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_groups_table (
            TaskGroupID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            TaskGroupName VARCHAR(100) NOT NULL,
            Description TEXT,
            PRIMARY KEY (TaskGroupID)
        ) $charset_collate;";

        // Task Group Tasks Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_group_tasks_table (
            TaskGroupTaskID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            TaskGroupID BIGINT(20) UNSIGNED NOT NULL,
            TaskID BIGINT(20) UNSIGNED NOT NULL,
            SortOrder INT UNSIGNED NOT NULL,
            PRIMARY KEY (TaskGroupTaskID),
            CONSTRAINT fk_taskgroup_taskgroup FOREIGN KEY (TaskGroupID) REFERENCES $task_groups_table(TaskGroupID) ON DELETE CASCADE,
            CONSTRAINT fk_taskgroup_task FOREIGN KEY (TaskID) REFERENCES $task_definitions_table(TaskID) ON DELETE CASCADE
        ) $charset_collate;";

        // Shift Templates Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_templates_table (
            ShiftTemplateID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ShiftTemplateName VARCHAR(100) NOT NULL,
            ProgramID BIGINT(20) UNSIGNED NOT NULL,
            DefaultStartTime TIME NOT NULL,
            DefaultEndTime TIME NOT NULL,
            PRIMARY KEY (ShiftTemplateID),
            CONSTRAINT fk_shifttemplate_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE CASCADE
        ) $charset_collate;";

        // Shift Template Task Groups Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_template_task_groups_table (
            ShiftTemplateTaskGroupID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ShiftTemplateID BIGINT(20) UNSIGNED NOT NULL,
            TaskGroupID BIGINT(20) UNSIGNED NOT NULL,
            SortOrder INT UNSIGNED NOT NULL,
            PRIMARY KEY (ShiftTemplateTaskGroupID),
            CONSTRAINT fk_shifttemplate_taskgroup_template FOREIGN KEY (ShiftTemplateID) REFERENCES $shift_templates_table(ShiftTemplateID) ON DELETE CASCADE,
            CONSTRAINT fk_shifttemplate_taskgroup_group FOREIGN KEY (TaskGroupID) REFERENCES $task_groups_table(TaskGroupID) ON DELETE CASCADE
        ) $charset_collate;";

        // Shift Occurrences Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_occurrences_table (
            ShiftOccurrenceID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ProgramID BIGINT(20) UNSIGNED NOT NULL,
            VolunteerID BIGINT(20) UNSIGNED NOT NULL,
            ScheduledDate DATE NOT NULL,
            StartTime TIME NOT NULL,
            EndTime TIME NOT NULL,
            ShiftTemplateID BIGINT(20) UNSIGNED NOT NULL,
            Notes TEXT,
            PRIMARY KEY (ShiftOccurrenceID),
            CONSTRAINT fk_shiftoccurrence_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE CASCADE,
            CONSTRAINT fk_shiftoccurrence_volunteer FOREIGN KEY (VolunteerID) REFERENCES $volunteers_table(VolunteerID) ON DELETE CASCADE,
            CONSTRAINT fk_shiftoccurrence_template FOREIGN KEY (ShiftTemplateID) REFERENCES $shift_templates_table(ShiftTemplateID) ON DELETE CASCADE
        ) $charset_collate;";

        // Shift Tasks Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_tasks_table (
            ShiftTaskID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ShiftOccurrenceID BIGINT(20) UNSIGNED NOT NULL,
            TaskID BIGINT(20) UNSIGNED NOT NULL,
            IsModified TINYINT(1) DEFAULT 0,
            Status VARCHAR(50) NOT NULL,
            Notes TEXT,
            PRIMARY KEY (ShiftTaskID),
            CONSTRAINT fk_shifttask_occurrence FOREIGN KEY (ShiftOccurrenceID) REFERENCES $shift_occurrences_table(ShiftOccurrenceID) ON DELETE CASCADE,
            CONSTRAINT fk_shifttask_task FOREIGN KEY (TaskID) REFERENCES $task_definitions_table(TaskID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Job Postings Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_postings_table (
            JobPostingID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ProgramID BIGINT(20) UNSIGNED NULL,
            Title VARCHAR(100) NOT NULL,
            Description TEXT,
            Requirements TEXT,
            Responsibilities TEXT,
            JobType VARCHAR(50) NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Draft',
            Location VARCHAR(100),
            SalaryRange VARCHAR(100),
            PostedDate DATETIME,
            ClosingDate DATETIME,
            DepartmentName VARCHAR(100),
            ReportsTo BIGINT(20) UNSIGNED NULL,
            CreatedBy BIGINT(20) UNSIGNED NOT NULL,
            LastModifiedDate DATETIME NOT NULL,
            IsInternal TINYINT(1) DEFAULT 0,
            PRIMARY KEY (JobPostingID),
            CONSTRAINT fk_jobposting_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL,
            CONSTRAINT fk_jobposting_reports_to FOREIGN KEY (ReportsTo) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_jobposting_created_by FOREIGN KEY (CreatedBy) REFERENCES $person_table(PersonID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Applications Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $applications_table (
            ApplicationID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            JobPostingID BIGINT(20) UNSIGNED NOT NULL,
            PersonID BIGINT(20) UNSIGNED NULL,
            ExternalApplicantID BIGINT(20) UNSIGNED NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'New',
            SubmissionDate DATETIME NOT NULL,
            LastModifiedDate DATETIME NOT NULL,
            Notes TEXT,
            ResumeURL VARCHAR(255),
            CoverLetterURL VARCHAR(255),
            ReferralSource VARCHAR(100),
            PRIMARY KEY (ApplicationID),
            CONSTRAINT fk_application_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE CASCADE,
            CONSTRAINT fk_application_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR External Applicants Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $external_applicants_table (
            ExternalApplicantID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            FirstName VARCHAR(50) NOT NULL,
            LastName VARCHAR(50) NOT NULL,
            Email VARCHAR(100) NOT NULL,
            Phone VARCHAR(20),
            AddressLine1 VARCHAR(100),
            AddressLine2 VARCHAR(100),
            City VARCHAR(50),
            State VARCHAR(50),
            Zip VARCHAR(20),
            CreatedDate DATETIME NOT NULL,
            LastModifiedDate DATETIME NOT NULL,
            ConvertedToPersonID BIGINT(20) UNSIGNED NULL,
            PRIMARY KEY (ExternalApplicantID),
            CONSTRAINT fk_externalapplicant_person FOREIGN KEY (ConvertedToPersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Interview Schedules Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $interview_schedules_table (
            InterviewID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ApplicationID BIGINT(20) UNSIGNED NOT NULL,
            InterviewRound INT UNSIGNED NOT NULL,
            ScheduledDateTime DATETIME NOT NULL,
            Location VARCHAR(100),
            InterviewType VARCHAR(50) NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Scheduled',
            Notes TEXT,
            PRIMARY KEY (InterviewID),
            CONSTRAINT fk_interview_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Interviewers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $interviewers_table (
            InterviewerAssignmentID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            InterviewID BIGINT(20) UNSIGNED NOT NULL,
            InterviewerID BIGINT(20) UNSIGNED NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Invited',
            Feedback TEXT,
            Rating INT,
            FeedbackSubmitted TINYINT(1) DEFAULT 0,
            PRIMARY KEY (InterviewerAssignmentID),
            CONSTRAINT fk_interviewer_interview FOREIGN KEY (InterviewID) REFERENCES $interview_schedules_table(InterviewID) ON DELETE CASCADE,
            CONSTRAINT fk_interviewer_person FOREIGN KEY (InterviewerID) REFERENCES $person_table(PersonID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Job Skills Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_skills_table (
            JobSkillID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            JobPostingID BIGINT(20) UNSIGNED NOT NULL,
            SkillName VARCHAR(100) NOT NULL,
            Required TINYINT(1) DEFAULT 0,
            PRIMARY KEY (JobSkillID),
            CONSTRAINT fk_jobskill_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Application Skills Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $application_skills_table (
            ApplicationSkillID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ApplicationID BIGINT(20) UNSIGNED NOT NULL,
            SkillName VARCHAR(100) NOT NULL,
            YearsExperience INT UNSIGNED,
            ProficiencyLevel VARCHAR(50),
            PRIMARY KEY (ApplicationSkillID),
            CONSTRAINT fk_applicationskill_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE CASCADE
        ) $charset_collate;";

        // HR Job Workflows Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_workflows_table (
            WorkflowID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            JobPostingID BIGINT(20) UNSIGNED NOT NULL,
            StepNumber INT UNSIGNED NOT NULL,
            StepName VARCHAR(100) NOT NULL,
            RequiredRole BIGINT(20) UNSIGNED NULL,
            IsComplete TINYINT(1) DEFAULT 0,
            CompletedBy BIGINT(20) UNSIGNED NULL,
            CompletedDate DATETIME,
            PRIMARY KEY (WorkflowID),
            CONSTRAINT fk_workflow_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE CASCADE,
            CONSTRAINT fk_workflow_role FOREIGN KEY (RequiredRole) REFERENCES $roles_table(RoleID) ON DELETE SET NULL,
            CONSTRAINT fk_workflow_completed_by FOREIGN KEY (CompletedBy) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Offers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $offers_table (
            OfferID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ApplicationID BIGINT(20) UNSIGNED NOT NULL,
            OfferDate DATETIME NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Draft',
            StartDate DATE,
            SalaryOffered DECIMAL(10,2),
            Position VARCHAR(100),
            Department VARCHAR(100),
            ApprovalStatus VARCHAR(50) DEFAULT 'Pending',
            ApprovedBy BIGINT(20) UNSIGNED NULL,
            ApprovalDate DATETIME,
            Notes TEXT,
            PRIMARY KEY (OfferID),
            CONSTRAINT fk_offer_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE CASCADE,
            CONSTRAINT fk_offer_approved_by FOREIGN KEY (ApprovedBy) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        try {
            // Execute each SQL statement
            foreach ($sql as $query) {
                $wpdb->query($query);
            }

            // Verify tables exist
            foreach ($tables as $table) {
                if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                    throw new Exception("Failed to create table: $table");
                }
            }

        } catch (Exception $e) {
            error_log('Database Setup Error: ' . $e->getMessage());
            throw new Exception('Failed to set up database tables. Please check the error log for details.');
        }
    }
} 