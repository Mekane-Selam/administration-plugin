<?php
// Get staff data from the database
global $wpdb;
$staff_table = $wpdb->prefix . 'hr_staff';
$person_table = $wpdb->prefix . 'core_person';
$roles_table = $wpdb->prefix . 'hr_roles';
$programs_table = $wpdb->prefix . 'core_programs';

$staff_members = $wpdb->get_results(
    "SELECT s.PersonID, p.FirstName, p.LastName, r.RoleTitle, pr.ProgramName
     FROM $staff_table s
     LEFT JOIN $person_table p ON s.PersonID = p.PersonID
     LEFT JOIN $roles_table r ON s.StaffRolesID = r.StaffRoleID
     LEFT JOIN $programs_table pr ON s.ProgramID = pr.ProgramID
     ORDER BY p.LastName ASC, p.FirstName ASC"
);
?>

<div class="wrap administration-hr-admin">
    <h1>HR Staff Directory</h1>
    <div class="card">
        <div class="card-header">
            <h2>Staff Members</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($staff_members)) : ?>
                <div class="table-responsive">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Program</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff_members as $staff) : ?>
                                <tr>
                                    <td><?php echo esc_html($staff->FirstName . ' ' . $staff->LastName); ?></td>
                                    <td><?php echo esc_html($staff->RoleTitle ?: '—'); ?></td>
                                    <td><?php echo esc_html($staff->ProgramName ?: '—'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="no-data">No staff members found.</p>
            <?php endif; ?>
        </div>
    </div>
</div> 