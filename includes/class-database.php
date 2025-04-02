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
            $events_table
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
     * Get person by user ID
     */
    public static function get_person_by_user_id($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_person';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE UserID = %d", $user_id));
    }

    /**
     * Get all persons
     */
    public static function get_all_persons() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_person';
        return $wpdb->get_results("SELECT * FROM $table_name");
    }

    /**
     * Get all programs
     */
    public static function get_all_programs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_programs';
        return $wpdb->get_results("SELECT * FROM $table_name");
    }

    /**
     * Get all roles
     */
    public static function get_all_roles() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_roles';
        return $wpdb->get_results("SELECT * FROM $table_name");
    }

    /**
     * Get persons by program ID
     */
    public static function get_persons_by_program($program_id) {
        global $wpdb;
        $person_table = $wpdb->prefix . 'core_person';
        $enrollments_table = $wpdb->prefix . 'core_program_enrollments';
        
        $query = "SELECT p.* FROM $person_table p
                 JOIN $enrollments_table e ON p.PersonID = e.PersonID
                 WHERE e.ProgramID = %d AND e.ActiveFlag = 1";
                 
        return $wpdb->get_results($wpdb->prepare($query, $program_id));
    }

    /**
     * Insert or update person
     */
    public static function save_person($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_person';
        
        if (isset($data['PersonID']) && $data['PersonID']) {
            $wpdb->update(
                $table_name,
                $data,
                ['PersonID' => $data['PersonID']]
            );
            return $data['PersonID'];
        } else {
            $wpdb->insert($table_name, $data);
            return $wpdb->insert_id;
        }
    }

    /**
     * Insert or update program
     */
    public static function save_program($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_programs';
        
        if (isset($data['ProgramID']) && $data['ProgramID']) {
            $wpdb->update(
                $table_name,
                $data,
                ['ProgramID' => $data['ProgramID']]
            );
            return $data['ProgramID'];
        } else {
            $wpdb->insert($table_name, $data);
            return $wpdb->insert_id;
        }
    }

    /**
     * Get program sessions
     */
    public static function get_program_sessions($program_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_program_sessions';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE ProgramID = %d ORDER BY SessionDate, StartTime",
            $program_id
        ));
    }

    /**
     * Save program session
     */
    public static function save_program_session($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_program_sessions';
        
        if (isset($data['ProgramSessionID']) && $data['ProgramSessionID']) {
            $wpdb->update(
                $table_name,
                $data,
                ['ProgramSessionID' => $data['ProgramSessionID']]
            );
            return $data['ProgramSessionID'];
        } else {
            $wpdb->insert($table_name, $data);
            return $wpdb->insert_id;
        }
    }

    /**
     * Save check-in record
     */
    public static function save_checkin($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_program_checkins';
        
        if (isset($data['CheckInID']) && $data['CheckInID']) {
            $wpdb->update(
                $table_name,
                $data,
                ['CheckInID' => $data['CheckInID']]
            );
            return $data['CheckInID'];
        } else {
            $wpdb->insert($table_name, $data);
            return $wpdb->insert_id;
        }
    }

    /**
     * Get check-in records for a program session
     */
    public static function get_session_checkins($program_session_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'core_program_checkins';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE ProgramSessionID = %d",
            $program_session_id
        ));
    }
}
