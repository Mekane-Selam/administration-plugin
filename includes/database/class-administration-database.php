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

        // Program Types Tables
        $progtype_edu_enrollment_table = $wpdb->prefix . 'progtype_edu_enrollment';
        $progtype_edu_courses_table = $wpdb->prefix . 'progtype_edu_courses';
        $progtype_edu_staff_table = $wpdb->prefix . 'progtype_edu_staff';
        $progtype_edu_staffroles_table = $wpdb->prefix . 'progtype_edu_staffroles';


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
            $offers_table,
            $progtype_edu_enrollment_table,
            $progtype_edu_courses_table,
            $progtype_edu_staff_table,
            $progtype_edu_staffroles_table
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
            PersonID VARCHAR(25) NOT NULL,
            UserID VARCHAR(25) NULL,
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
            PRIMARY KEY (PersonID)
        ) $charset_collate;";

        // Person Relationships table
        $sql[] = "CREATE TABLE IF NOT EXISTS $person_relationships_table (
            RelationshipID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            RelatedPersonID VARCHAR(25) NULL,
            RelationshipType VARCHAR(50) NOT NULL,
            PRIMARY KEY (RelationshipID),
            CONSTRAINT fk_relationship_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_relationship_related_person FOREIGN KEY (RelatedPersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // Roles table
        $sql[] = "CREATE TABLE IF NOT EXISTS $roles_table (
            RoleID VARCHAR(25) NOT NULL,
            RoleName VARCHAR(50) NOT NULL,
            PRIMARY KEY (RoleID)
        ) $charset_collate;";

        // Person Roles table
        $sql[] = "CREATE TABLE IF NOT EXISTS $person_roles_table (
            PersonRoleID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            RoleID VARCHAR(25) NULL,
            ProgramID VARCHAR(25) NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            PRIMARY KEY (PersonRoleID),
            CONSTRAINT fk_person_role_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_person_role_role FOREIGN KEY (RoleID) REFERENCES $roles_table(RoleID) ON DELETE SET NULL
        ) $charset_collate;";

        // Programs table
        $sql[] = "CREATE TABLE IF NOT EXISTS $programs_table (
            ProgramID VARCHAR(25) NOT NULL,
            ProgramName VARCHAR(100) NOT NULL,
            ProgramType VARCHAR(50) NOT NULL,
            ProgramDescription TEXT,
            ActiveFlag TINYINT(1) DEFAULT 1,
            StartDate DATE,
            EndDate DATE,
            ProgramOwner VARCHAR(25) NULL,
            PRIMARY KEY (ProgramID),
            CONSTRAINT fk_program_owner FOREIGN KEY (ProgramOwner) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // Program Enrollments table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_enrollments_table (
            EnrollmentID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            ProgramID VARCHAR(25) NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            EnrollmentDate DATE,
            CompletionDate DATE,
            PRIMARY KEY (EnrollmentID),
            CONSTRAINT fk_enrollment_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_enrollment_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL
        ) $charset_collate;";

        // Program Sessions table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_sessions_table (
            ProgramSessionID VARCHAR(25) NOT NULL,
            ProgramID VARCHAR(25) NULL,
            SessionDate DATE,
            StartTime TIME,
            EndTime TIME,
            Location VARCHAR(100),
            Notes TEXT,
            IsSelectedSession TINYINT(1) DEFAULT 0,
            PRIMARY KEY (ProgramSessionID),
            CONSTRAINT fk_session_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL
        ) $charset_collate;";

        // Program Checkins table
        $sql[] = "CREATE TABLE IF NOT EXISTS $program_checkins_table (
            CheckInID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            ProgramID VARCHAR(25) NULL,
            ProgramSessionID VARCHAR(25) NULL,
            CheckInDate DATE,
            CheckInTime TIME,
            CheckOutDate DATE,
            CheckOutTime TIME,
            PRIMARY KEY (CheckInID),
            CONSTRAINT fk_checkin_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_checkin_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL,
            CONSTRAINT fk_checkin_session FOREIGN KEY (ProgramSessionID) REFERENCES $program_sessions_table(ProgramSessionID) ON DELETE SET NULL
        ) $charset_collate;";

        // Events table
        $sql[] = "CREATE TABLE IF NOT EXISTS $events_table (
            EventID VARCHAR(25) NOT NULL,
            ProgramID VARCHAR(25) NULL,
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
            VolunteerID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            Skills TEXT,
            AvailabilityNotes TEXT,
            PRIMARY KEY (VolunteerID),
            CONSTRAINT fk_volunteer_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // Volunteer Availability Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $volunteer_availability_table (
            AvailabilityID VARCHAR(25) NOT NULL,
            VolunteerID VARCHAR(25) NULL,
            StartDateTime DATETIME NOT NULL,
            EndDateTime DATETIME NOT NULL,
            AvailabilityType VARCHAR(50) NOT NULL,
            LastUpdated DATETIME NOT NULL,
            Notes TEXT,
            PRIMARY KEY (AvailabilityID),
            CONSTRAINT fk_availability_volunteer FOREIGN KEY (VolunteerID) REFERENCES $volunteers_table(VolunteerID) ON DELETE SET NULL
        ) $charset_collate;";

        // Task Definitions Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_definitions_table (
            TaskID VARCHAR(25) NOT NULL,
            TaskName VARCHAR(100) NOT NULL,
            TaskDescription TEXT,
            DefaultDuration INT UNSIGNED,
            PRIMARY KEY (TaskID)
        ) $charset_collate;";

        // Task Groups Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_groups_table (
            TaskGroupID VARCHAR(25) NOT NULL,
            TaskGroupName VARCHAR(100) NOT NULL,
            Description TEXT,
            PRIMARY KEY (TaskGroupID)
        ) $charset_collate;";

        // Task Group Tasks Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $task_group_tasks_table (
            TaskGroupTaskID VARCHAR(25) NOT NULL,
            TaskGroupID VARCHAR(25) NULL,
            TaskID VARCHAR(25) NULL,
            SortOrder INT UNSIGNED NOT NULL,
            PRIMARY KEY (TaskGroupTaskID),
            CONSTRAINT fk_taskgroup_taskgroup FOREIGN KEY (TaskGroupID) REFERENCES $task_groups_table(TaskGroupID) ON DELETE SET NULL,
            CONSTRAINT fk_taskgroup_task FOREIGN KEY (TaskID) REFERENCES $task_definitions_table(TaskID) ON DELETE SET NULL
        ) $charset_collate;";

        // Shift Templates Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_templates_table (
            ShiftTemplateID VARCHAR(25) NOT NULL,
            ShiftTemplateName VARCHAR(100) NOT NULL,
            ProgramID VARCHAR(25) NULL,
            DefaultStartTime TIME NOT NULL,
            DefaultEndTime TIME NOT NULL,
            PRIMARY KEY (ShiftTemplateID),
            CONSTRAINT fk_shifttemplate_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL
        ) $charset_collate;";

        // Shift Template Task Groups Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_template_task_groups_table (
            ShiftTemplateTaskGroupID VARCHAR(25) NOT NULL,
            ShiftTemplateID VARCHAR(25) NULL,
            TaskGroupID VARCHAR(25) NULL,
            SortOrder INT UNSIGNED NOT NULL,
            PRIMARY KEY (ShiftTemplateTaskGroupID),
            CONSTRAINT fk_shifttemplate_taskgroup_template FOREIGN KEY (ShiftTemplateID) REFERENCES $shift_templates_table(ShiftTemplateID) ON DELETE SET NULL,
            CONSTRAINT fk_shifttemplate_taskgroup_group FOREIGN KEY (TaskGroupID) REFERENCES $task_groups_table(TaskGroupID) ON DELETE SET NULL
        ) $charset_collate;";

        // Shift Occurrences Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_occurrences_table (
            ShiftOccurrenceID VARCHAR(25) NOT NULL,
            ProgramID VARCHAR(25) NULL,
            VolunteerID VARCHAR(25) NULL,
            ScheduledDate DATE NOT NULL,
            StartTime TIME NOT NULL,
            EndTime TIME NOT NULL,
            ShiftTemplateID VARCHAR(25) NULL,
            Notes TEXT,
            PRIMARY KEY (ShiftOccurrenceID),
            CONSTRAINT fk_shiftoccurrence_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL,
            CONSTRAINT fk_shiftoccurrence_volunteer FOREIGN KEY (VolunteerID) REFERENCES $volunteers_table(VolunteerID) ON DELETE SET NULL,
            CONSTRAINT fk_shiftoccurrence_template FOREIGN KEY (ShiftTemplateID) REFERENCES $shift_templates_table(ShiftTemplateID) ON DELETE SET NULL
        ) $charset_collate;";

        // Shift Tasks Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $shift_tasks_table (
            ShiftTaskID VARCHAR(25) NOT NULL,
            ShiftOccurrenceID VARCHAR(25) NULL,
            TaskID VARCHAR(25) NULL,
            IsModified TINYINT(1) DEFAULT 0,
            Status VARCHAR(50) NOT NULL,
            Notes TEXT,
            PRIMARY KEY (ShiftTaskID),
            CONSTRAINT fk_shifttask_occurrence FOREIGN KEY (ShiftOccurrenceID) REFERENCES $shift_occurrences_table(ShiftOccurrenceID) ON DELETE SET NULL,
            CONSTRAINT fk_shifttask_task FOREIGN KEY (TaskID) REFERENCES $task_definitions_table(TaskID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Job Postings Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_postings_table (
            JobPostingID VARCHAR(25) NOT NULL,
            ProgramID VARCHAR(25) NULL,
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
            ReportsTo VARCHAR(25) NULL,
            CreatedBy VARCHAR(25) NULL,
            LastModifiedDate DATETIME NOT NULL,
            IsInternal TINYINT(1) DEFAULT 0,
            PRIMARY KEY (JobPostingID),
            CONSTRAINT fk_jobposting_program FOREIGN KEY (ProgramID) REFERENCES $programs_table(ProgramID) ON DELETE SET NULL,
            CONSTRAINT fk_jobposting_reports_to FOREIGN KEY (ReportsTo) REFERENCES $person_table(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_jobposting_created_by FOREIGN KEY (CreatedBy) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Applications Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $applications_table (
            ApplicationID VARCHAR(25) NOT NULL,
            JobPostingID VARCHAR(25) NULL,
            PersonID VARCHAR(25) NULL,
            ExternalApplicantID VARCHAR(25) NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'New',
            SubmissionDate DATETIME NOT NULL,
            LastModifiedDate DATETIME NOT NULL,
            Notes TEXT,
            ResumeURL VARCHAR(255),
            CoverLetterURL VARCHAR(255),
            ReferralSource VARCHAR(100),
            PRIMARY KEY (ApplicationID),
            CONSTRAINT fk_application_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE SET NULL,
            CONSTRAINT fk_application_person FOREIGN KEY (PersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR External Applicants Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $external_applicants_table (
            ExternalApplicantID VARCHAR(25) NOT NULL,
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
            ConvertedToPersonID VARCHAR(25) NULL,
            PRIMARY KEY (ExternalApplicantID),
            CONSTRAINT fk_externalapplicant_person FOREIGN KEY (ConvertedToPersonID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Interview Schedules Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $interview_schedules_table (
            InterviewID VARCHAR(25) NOT NULL,
            ApplicationID VARCHAR(25) NULL,
            InterviewRound INT UNSIGNED NOT NULL,
            ScheduledDateTime DATETIME NOT NULL,
            Location VARCHAR(100),
            InterviewType VARCHAR(50) NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Scheduled',
            Notes TEXT,
            PRIMARY KEY (InterviewID),
            CONSTRAINT fk_interview_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Interviewers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $interviewers_table (
            InterviewerAssignmentID VARCHAR(25) NOT NULL,
            InterviewID VARCHAR(25) NULL,
            InterviewerID VARCHAR(25) NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Invited',
            Feedback TEXT,
            Rating INT,
            FeedbackSubmitted TINYINT(1) DEFAULT 0,
            PRIMARY KEY (InterviewerAssignmentID),
            CONSTRAINT fk_interviewer_interview FOREIGN KEY (InterviewID) REFERENCES $interview_schedules_table(InterviewID) ON DELETE SET NULL,
            CONSTRAINT fk_interviewer_person FOREIGN KEY (InterviewerID) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Job Skills Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_skills_table (
            JobSkillID VARCHAR(25) NOT NULL,
            JobPostingID VARCHAR(25) NULL,
            SkillName VARCHAR(100) NOT NULL,
            Required TINYINT(1) DEFAULT 0,
            PRIMARY KEY (JobSkillID),
            CONSTRAINT fk_jobskill_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Application Skills Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $application_skills_table (
            ApplicationSkillID VARCHAR(25) NOT NULL,
            ApplicationID VARCHAR(25) NULL,
            SkillName VARCHAR(100) NOT NULL,
            YearsExperience INT UNSIGNED,
            ProficiencyLevel VARCHAR(50),
            PRIMARY KEY (ApplicationSkillID),
            CONSTRAINT fk_applicationskill_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Job Workflows Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $job_workflows_table (
            WorkflowID VARCHAR(25) NOT NULL,
            JobPostingID VARCHAR(25) NULL,
            StepNumber INT UNSIGNED NOT NULL,
            StepName VARCHAR(100) NOT NULL,
            RequiredRole VARCHAR(25) NULL,
            IsComplete TINYINT(1) DEFAULT 0,
            CompletedBy VARCHAR(25) NULL,
            CompletedDate DATETIME,
            PRIMARY KEY (WorkflowID),
            CONSTRAINT fk_workflow_jobposting FOREIGN KEY (JobPostingID) REFERENCES $job_postings_table(JobPostingID) ON DELETE SET NULL,
            CONSTRAINT fk_workflow_role FOREIGN KEY (RequiredRole) REFERENCES $roles_table(RoleID) ON DELETE SET NULL,
            CONSTRAINT fk_workflow_completed_by FOREIGN KEY (CompletedBy) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // HR Offers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS $offers_table (
            OfferID VARCHAR(25) NOT NULL,
            ApplicationID VARCHAR(25) NULL,
            OfferDate DATETIME NOT NULL,
            Status VARCHAR(50) NOT NULL DEFAULT 'Draft',
            StartDate DATE,
            SalaryOffered DECIMAL(10,2),
            Position VARCHAR(100),
            Department VARCHAR(100),
            ApprovalStatus VARCHAR(50) DEFAULT 'Pending',
            ApprovedBy VARCHAR(25) NULL,
            ApprovalDate DATETIME,
            Notes TEXT,
            PRIMARY KEY (OfferID),
            CONSTRAINT fk_offer_application FOREIGN KEY (ApplicationID) REFERENCES $applications_table(ApplicationID) ON DELETE SET NULL,
            CONSTRAINT fk_offer_approved_by FOREIGN KEY (ApprovedBy) REFERENCES $person_table(PersonID) ON DELETE SET NULL
        ) $charset_collate;";

        // Education Program Type Tables
        $sql[] = "CREATE TABLE IF NOT EXISTS $progtype_edu_enrollment_table (
            ProgramEnrollmentID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            CourseID VARCHAR(25) NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            EnrollmentDate DATE,
            ProgramID VARCHAR(25) NULL,
            PRIMARY KEY (ProgramEnrollmentID),
            CONSTRAINT fk_edu_enroll_person FOREIGN KEY (PersonID) REFERENCES {$wpdb->prefix}core_person(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_edu_enroll_course FOREIGN KEY (CourseID) REFERENCES {$wpdb->prefix}progtype_edu_courses(CourseID) ON DELETE SET NULL,
            CONSTRAINT fk_edu_enroll_program FOREIGN KEY (ProgramID) REFERENCES {$wpdb->prefix}core_programs(ProgramID) ON DELETE SET NULL
        ) $charset_collate;";

        $sql[] = "CREATE TABLE IF NOT EXISTS $progtype_edu_courses_table (
            CourseID VARCHAR(25) NOT NULL,
            ProgramID VARCHAR(25) NULL,
            CourseName VARCHAR(100) NOT NULL,
            Description TEXT,
            Level VARCHAR(50),
            PrimaryInstructorID VARCHAR(25) NULL,
            StartDate DATE,
            EndDate DATE,
            PRIMARY KEY (CourseID),
            CONSTRAINT fk_edu_course_program FOREIGN KEY (ProgramID) REFERENCES {$wpdb->prefix}core_programs(ProgramID) ON DELETE SET NULL,
            CONSTRAINT fk_edu_course_instructor FOREIGN KEY (PrimaryInstructorID) REFERENCES {$wpdb->prefix}core_person(PersonID) ON DELETE SET NULL
        ) $charset_collate;";
        $sql[] = "CREATE TABLE IF NOT EXISTS $progtype_edu_courseenrollments_table (
            CourseEnrollmentID VARCHAR(25) NOT NULL,
            PersonID VARCHAR(25) NULL,
            CourseID VARCHAR(25) NULL,
            ActiveFlag TINYINT(1) DEFAULT 1,
            EnrollmentDate DATE,
            CompletionDate DATE NULL,
            PRIMARY KEY (CourseEnrollmentID),
            CONSTRAINT fk_edu_courseenroll_person FOREIGN KEY (PersonID) REFERENCES {$wpdb->prefix}core_person(PersonID) ON DELETE SET NULL,
            CONSTRAINT fk_edu_courseenroll_course FOREIGN KEY (CourseID) REFERENCES {$wpdb->prefix}progtype_edu_courses(CourseID) ON DELETE SET NULL
        ) $charset_collate;";
        $sql[] = "CREATE TABLE IF NOT EXISTS $progtype_edu_staff_table (
            PersonID VARCHAR(25) NOT NULL,
            StaffRolesID TEXT,
            PRIMARY KEY (PersonID),
            CONSTRAINT fk_edu_staff_person FOREIGN KEY (PersonID) REFERENCES {$wpdb->prefix}core_person(PersonID) ON DELETE CASCADE
        ) $charset_collate;";
        $sql[] = "CREATE TABLE IF NOT EXISTS $progtype_edu_staffroles_table (
            StaffRoleID VARCHAR(25) NOT NULL,
            StaffRoleDescription VARCHAR(100),
            PaidFlag TINYINT(1) DEFAULT 0,
            PRIMARY KEY (StaffRoleID)
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

    /**
     * Get a person by WordPress user ID
     */
    public static function get_person_by_user_id($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE UserID = %d", $user_id));
    }

    /**
     * Save a person (insert or update)
     * $person_data: array with keys UserID, FirstName, LastName, Email, [PersonID]
     * Returns PersonID on success, false on failure
     */
    public static function save_person($person_data) {
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        $fields = [
            'UserID' => isset($person_data['UserID']) ? $person_data['UserID'] : null,
            'FirstName' => isset($person_data['FirstName']) ? $person_data['FirstName'] : '',
            'LastName' => isset($person_data['LastName']) ? $person_data['LastName'] : '',
            'Email' => isset($person_data['Email']) ? $person_data['Email'] : '',
        ];
        if (!empty($person_data['PersonID'])) {
            // Update existing
            $result = $wpdb->update(
                $table,
                $fields,
                ['PersonID' => $person_data['PersonID']]
            );
            return $result !== false ? $person_data['PersonID'] : false;
        } else {
            // Insert new, generate unique PersonID (PERSxxxxx)
            do {
                $unique_code = mt_rand(10000, 99999);
                $person_id = 'PERS' . $unique_code;
                $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE PersonID = %s", $person_id));
            } while ($exists);
            $fields['PersonID'] = $person_id;
            $result = $wpdb->insert($table, $fields);
            return $result ? $person_id : false;
        }
    }

    /**
     * Run this on plugin activation to ensure all tables are created
     */
    public static function activate() {
        self::setup_tables();
    }
} 