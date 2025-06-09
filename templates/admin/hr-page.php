<?php
/**
 * HR admin page template
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="administration-hr">
        <div class="notice notice-info">
            <p><?php _e('Admin settings page', 'administration-plugin'); ?></p>
        </div>
    </div>
</div>

<!-- Job Modal Template -->
<div id="job-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Job Details</h2>
        <form id="job-form">
            <div class="form-field">
                <label for="job-title">Title</label>
                <input type="text" id="job-title" name="job-title" required>
            </div>
            <div class="form-field">
                <label for="job-department">Department</label>
                <input type="text" id="job-department" name="job-department" required>
            </div>
            <div class="form-field">
                <label for="job-location">Location</label>
                <input type="text" id="job-location" name="job-location">
            </div>
            <div class="form-field">
                <label for="job-type">Job Type</label>
                <select id="job-type" name="job-type" required>
                    <option value="full-time">Full Time</option>
                    <option value="part-time">Part Time</option>
                    <option value="contract">Contract</option>
                    <option value="temporary">Temporary</option>
                </select>
            </div>
            <div class="form-field">
                <label for="job-description">Description</label>
                <textarea id="job-description" name="job-description" required></textarea>
            </div>
            <div class="form-field">
                <label for="job-requirements">Requirements</label>
                <textarea id="job-requirements" name="job-requirements"></textarea>
            </div>
            <div class="form-field">
                <label for="job-responsibilities">Responsibilities</label>
                <textarea id="job-responsibilities" name="job-responsibilities"></textarea>
            </div>
            <div class="form-field">
                <label for="job-salary">Salary Range</label>
                <input type="text" id="job-salary" name="job-salary">
            </div>
            <div class="form-field">
                <label for="job-closing-date">Closing Date</label>
                <input type="date" id="job-closing-date" name="job-closing-date">
            </div>
            <div class="form-field">
                <label for="job-status">Status</label>
                <select id="job-status" name="job-status" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Job</button>
                <button type="button" class="button" id="cancel-job">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Application Modal Template -->
<div id="application-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Application Details</h2>
        <form id="application-form">
            <div class="form-field">
                <label for="application-job">Job</label>
                <select id="application-job" name="application-job" required>
                    <!-- Jobs will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-field">
                <label for="application-status">Status</label>
                <select id="application-status" name="application-status" required>
                    <option value="new">New</option>
                    <option value="reviewing">Reviewing</option>
                    <option value="interviewing">Interviewing</option>
                    <option value="offered">Offered</option>
                    <option value="hired">Hired</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="form-field">
                <label for="application-notes">Notes</label>
                <textarea id="application-notes" name="application-notes"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Application</button>
                <button type="button" class="button" id="cancel-application">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php
// Permissions management section (only for WP admin or System Administration role)
if ( ! class_exists('Permissions_Util') ) {
    require_once dirname(__DIR__, 1) . '/class-permissions-util.php';
}
$current_user_id = get_current_user_id();
$can_access_permissions = Permissions_Util::user_has_permission($current_user_id, 'System Administration');
if ($can_access_permissions): ?>
    <script>console.log('Permissions UI: User CAN access the permissions area.');</script>
<?php else: ?>
    <script>console.log('Permissions UI: User CANNOT access the permissions area.');</script>
<?php endif; ?>
<?php if ($can_access_permissions): ?>
    <div class="administration-permissions" style="margin-top: 2em; padding: 1em; border: 1px solid #ccc; background: #f9f9f9;">
        <h2><?php _e('Permissions Management', 'administration-plugin'); ?></h2>
        <p><?php _e('Manage user and role permissions below.', 'administration-plugin'); ?></p>
        <h3><?php _e('Current Staff Roles', 'administration-plugin'); ?></h3>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('User', 'administration-plugin'); ?></th>
                    <th><?php _e('Role', 'administration-plugin'); ?></th>
                    <th><?php _e('Program', 'administration-plugin'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                global $wpdb;
                $staff = $wpdb->get_results("SELECT s.PersonID, p.FirstName, p.LastName, r.RoleTitle, s.ProgramID FROM {$wpdb->prefix}hr_staff s LEFT JOIN {$wpdb->prefix}core_person p ON s.PersonID = p.PersonID LEFT JOIN {$wpdb->prefix}hr_roles r ON s.StaffRolesID = r.StaffRoleID");
                if ($staff) {
                    foreach ($staff as $row) {
                        echo '<tr>';
                        echo '<td>' . esc_html($row->FirstName . ' ' . $row->LastName) . '</td>';
                        echo '<td>' . esc_html($row->RoleTitle) . '</td>';
                        echo '<td>' . esc_html($row->ProgramID ? $row->ProgramID : __('Global', 'administration-plugin')) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3">' . __('No staff roles assigned.', 'administration-plugin') . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
        <h3 style="margin-top:2em;"><?php _e('Assign New Role', 'administration-plugin'); ?></h3>
        <form id="assign-role-form" method="post" style="display: flex; gap: 1em; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label for="assign-user"><?php _e('User', 'administration-plugin'); ?></label><br>
                <select id="assign-user" name="assign-user">
                    <?php
                    $users = $wpdb->get_results("SELECT PersonID, FirstName, LastName FROM {$wpdb->prefix}core_person");
                    foreach ($users as $user) {
                        echo '<option value="' . esc_attr($user->PersonID) . '">' . esc_html($user->FirstName . ' ' . $user->LastName) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="assign-role"><?php _e('Role', 'administration-plugin'); ?></label><br>
                <select id="assign-role" name="assign-role">
                    <?php
                    $roles = $wpdb->get_results("SELECT StaffRoleID, RoleTitle FROM {$wpdb->prefix}hr_roles");
                    foreach ($roles as $role) {
                        echo '<option value="' . esc_attr($role->StaffRoleID) . '">' . esc_html($role->RoleTitle) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="assign-program"><?php _e('Program (optional)', 'administration-plugin'); ?></label><br>
                <select id="assign-program" name="assign-program">
                    <option value=""><?php _e('Global', 'administration-plugin'); ?></option>
                    <?php
                    $programs = $wpdb->get_results("SELECT ProgramID, ProgramName FROM {$wpdb->prefix}core_programs");
                    foreach ($programs as $program) {
                        echo '<option value="' . esc_attr($program->ProgramID) . '">' . esc_html($program->ProgramName) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <button type="submit" class="button button-primary"><?php _e('Assign Role', 'administration-plugin'); ?></button>
            </div>
        </form>
    </div>
<?php endif; ?> 