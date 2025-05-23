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
    <div class="hr-admin-grid">
        <!-- Staff Directory Card -->
        <div class="card">
            <div class="card-header" style="padding-left: 24px;">
                <h2>Staff Directory</h2>
            </div>
            <div class="card-body" style="padding-left: 24px; padding-right: 24px;">
                <?php if (!empty($staff_members)) : ?>
                    <div class="table-responsive">
                        <table class="hr-admin-staff-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Program</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($staff_members as $staff) : ?>
                                    <tr class="staff-row" data-person-id="<?php echo esc_attr($staff->PersonID); ?>">
                                        <td><?php echo esc_html($staff->FirstName . ' ' . $staff->LastName); ?></td>
                                        <td><?php echo esc_html($staff->RoleTitle ?: '—'); ?></td>
                                        <td><?php echo esc_html($staff->ProgramName ?: '—'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="hr-admin-staff-table no-data">No staff members found.</div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Placeholder Card 1 -->
        <div class="card">
            <div class="card-header" style="padding-left: 24px;">
                <h2>HR Analytics</h2>
            </div>
            <div class="card-body">
                <p>Analytics and metrics coming soon...</p>
            </div>
        </div>
        <!-- Placeholder Card 2 -->
        <div class="card">
            <div class="card-header" style="padding-left: 24px;">
                <h2>Open Positions</h2>
            </div>
            <div class="card-body">
                <p>Job postings and open positions coming soon...</p>
            </div>
        </div>
        <!-- Placeholder Card 3 -->
        <div class="card">
            <div class="card-header" style="padding-left: 24px;">
                <h2>Recent Applications</h2>
            </div>
            <div class="card-body">
                <p>Recent applications and activity coming soon...</p>
            </div>
        </div>
    </div>
</div>

<!-- Staff Details Modal -->
<div id="staff-details-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="close-staff-details-modal" tabindex="0" role="button" aria-label="Close">&times;</span>
        <h2>Staff Details</h2>
        <div class="person-details-section person-details-general">
            <div class="person-details-section-header">
                <h3>General</h3>
            </div>
            <div class="person-details-section-content" id="staff-details-general-content"></div>
        </div>
    </div>
</div> 