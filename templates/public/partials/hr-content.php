<?php
// Get staff data from the database
global $wpdb;
$staff_table = $wpdb->prefix . 'hr_staff';
$person_table = $wpdb->prefix . 'core_person';
$roles_table = $wpdb->prefix . 'hr_roles';
$programs_table = $wpdb->prefix . 'core_programs';

$staff_rows = $wpdb->get_results(
    "SELECT s.PersonID, p.FirstName, p.LastName, r.RoleTitle, pr.ProgramName
     FROM $staff_table s
     LEFT JOIN $person_table p ON s.PersonID = p.PersonID
     LEFT JOIN $roles_table r ON s.StaffRolesID = r.StaffRoleID
     LEFT JOIN $programs_table pr ON s.ProgramID = pr.ProgramID
     ORDER BY p.LastName ASC, p.FirstName ASC"
);

// Group staff by PersonID
$staff_members = [];
foreach ($staff_rows as $row) {
    $pid = $row->PersonID;
    if (!isset($staff_members[$pid])) {
        $staff_members[$pid] = [
            'PersonID' => $row->PersonID,
            'FirstName' => $row->FirstName,
            'LastName' => $row->LastName,
            'roles' => [],
            'programs' => [],
        ];
    }
    if ($row->RoleTitle && !in_array($row->RoleTitle, $staff_members[$pid]['roles'])) {
        $staff_members[$pid]['roles'][] = $row->RoleTitle;
    }
    if ($row->ProgramName && !in_array($row->ProgramName, $staff_members[$pid]['programs'])) {
        $staff_members[$pid]['programs'][] = $row->ProgramName;
    }
}
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
                                    <tr class="staff-row" data-person-id="<?php echo esc_attr($staff['PersonID']); ?>">
                                        <td><?php echo esc_html($staff['FirstName'] . ' ' . $staff['LastName']); ?></td>
                                        <td><?php echo count($staff['roles']) > 1 ? 'Multiple' : esc_html($staff['roles'][0] ?? '—'); ?></td>
                                        <td><?php echo count($staff['programs']) > 1 ? 'Multiple' : esc_html($staff['programs'][0] ?? '—'); ?></td>
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
        <!-- Job Postings Card (Top Right) -->
        <div class="card">
            <div class="card-header" style="padding-left: 24px;">
                <h2>Job Postings</h2>
            </div>
            <div class="card-body">
                <div class="job-postings-list-header toggle-slider-header" style="display: flex; align-items: center; gap: 12px;">
                    <button id="add-job-posting-btn" class="add-button" title="Add Job Posting">
                        <span class="dashicons dashicons-plus-alt"></span>
                    </button>
                    <label class="switch" style="margin-left: 8px;">
                        <input type="checkbox" id="toggle-all-job-postings">
                        <span class="slider round"></span>
                    </label>
                    <span id="toggle-job-postings-label" style="margin-left: 8px; font-size: 0.98em;">Show Active</span>
                </div>
                <div id="job-postings-list"></div>
            </div>
        </div>
    </div>
</div>

<!-- Staff Details Modal -->
<div id="staff-details-modal" class="modal">
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