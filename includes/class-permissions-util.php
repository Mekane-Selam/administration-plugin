<?php
/**
 * Utility class for permission checks.
 */
class Permissions_Util {
    /**
     * Check if a user has a given permission, considering WordPress admin, global roles, and program-specific roles.
     *
     * @param int $user_id WordPress user ID
     * @param string $permission_key The permission to check (e.g., 'manage_permissions')
     * @param string|null $program_id Optional program context
     * @return bool
     */
    public static function user_has_permission($user_id, $permission_key, $program_id = null) {
        // 1. WordPress admin always has permission
        if (current_user_can('manage_options')) {
            return true;
        }
        global $wpdb;
        // 2. Get PersonID from user ID
        $person = $wpdb->get_row($wpdb->prepare(
            "SELECT PersonID FROM {$wpdb->prefix}core_person WHERE UserID = %d",
            $user_id
        ));
        if (!$person) {
            return false;
        }
        $person_id = $person->PersonID;
        // 3. Get all staff roles for this person (global and program-specific)
        $hr_staff_table = $wpdb->prefix . 'hr_staff';
        $hr_roles_table = $wpdb->prefix . 'hr_roles';
        // Get all roles for this person, both global (ProgramID IS NULL) and for the given program
        $query = "SELECT s.StaffRolesID, s.ProgramID, r.RoleTitle FROM $hr_staff_table s LEFT JOIN $hr_roles_table r ON s.StaffRolesID = r.StaffRoleID WHERE s.PersonID = %s";
        $roles = $wpdb->get_results($wpdb->prepare($query, $person_id));
        if (!$roles) {
            return false;
        }
        // 4. Check for System Administration role (global)
        foreach ($roles as $role) {
            if ($role->RoleTitle === 'System Administration' && ($role->ProgramID === null || $role->ProgramID === '')) {
                // System Admin role is global, grant all permissions
                return true;
            }
        }
        // 5. Check for the specific permission (future: map roles to permissions)
        // For now, just check if the user has the right role for the program
        foreach ($roles as $role) {
            // If program-specific, match program; if global, always allow
            if ($role->RoleTitle === $permission_key && ($role->ProgramID === null || $role->ProgramID === '' || $role->ProgramID === $program_id)) {
                return true;
            }
        }
        return false;
    }
} 