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

// Permissions Management Access Logic
if ( ! class_exists('Permissions_Util') ) {
    require_once dirname(__DIR__, 3) . '/includes/class-permissions-util.php';
}
$current_user_id = get_current_user_id();
$can_access_permissions = Permissions_Util::user_has_permission($current_user_id, 'System Administration');
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
        <!-- Permissions Management Card (now inside grid) -->
        <?php if ($can_access_permissions): ?>
        <div class="card administration-permissions">
            <div class="card-header" style="padding-left: 24px;">
                <h2><?php _e('Permissions Management', 'administration-plugin'); ?></h2>
            </div>
            <div class="card-body" style="padding-left: 24px; padding-right: 24px;">
                <div class="permissions-split-panel">
                    <div class="permissions-user-list-panel">
                        <label for="permissions-user-search" style="font-weight:600;">Search User</label>
                        <input type="text" id="permissions-user-search" class="permissions-user-search" placeholder="Type to search users..." autocomplete="off" style="width:100%;margin-bottom:12px;">
                        <div class="permissions-user-list" style="max-height:300px;overflow-y:auto;"></div>
                    </div>
                    <div class="permissions-details-panel">
                        <div class="permissions-details-placeholder" style="color:#888;text-align:center;margin-top:40px;">
                            <span class="dashicons dashicons-admin-users" style="font-size:2em;"></span>
                            <p>Select a user to view and manage their permissions.</p>
                        </div>
                        <div class="permissions-details-content" style="display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
        jQuery(function($) {
            // Permissions user search and split panel logic
            var $search = $('#permissions-user-search');
            var $list = $('.permissions-user-list');
            var $detailsPanel = $('.permissions-details-panel');
            var $placeholder = $detailsPanel.find('.permissions-details-placeholder');
            var $detailsContent = $detailsPanel.find('.permissions-details-content');
            var lastSearch = '';
            var searchTimeout;
            function renderUserList(users) {
                $list.empty();
                if (!Array.isArray(users) || !users.length) {
                    $list.append('<div style="color:#888;padding:12px;">No users found.</div>');
                    return;
                }
                users.forEach(function(user) {
                    $list.append('<div class="permissions-user-list-item" data-person-id="'+user.PersonID+'">'+
                        $('<div>').text(user.FirstName + ' ' + user.LastName).html()+'</div>');
                });
            }
            function searchUsers(query) {
                $list.html('<div style="color:#888;padding:12px;">Searching...</div>');
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'search_people',
                        nonce: administration_plugin.nonce,
                        q: query
                    },
                    success: function(response) {
                        if (response.success && Array.isArray(response.data) && response.data.length) {
                            renderUserList(response.data);
                        } else {
                            renderUserList([]);
                        }
                    },
                    error: function() {
                        renderUserList([]);
                    }
                });
            }
            $search.on('input', function() {
                var val = $search.val().trim();
                if (val === lastSearch) return;
                lastSearch = val;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (val.length < 2) {
                        $list.empty();
                        return;
                    }
                    searchUsers(val);
                }, 200);
            });
            $list.on('click', '.permissions-user-list-item', function() {
                var $item = $(this);
                $list.find('.permissions-user-list-item').removeClass('selected');
                $item.addClass('selected');
                var personId = $item.data('person-id');
                $placeholder.hide();
                $detailsContent.show().html('<div class="loading">Loading permissions...</div>');
                // Fetch permissions/roles for this user
                $.ajax({
                    url: administration_plugin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_person_permissions',
                        nonce: administration_plugin.nonce,
                        person_id: personId
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            var html = '<h3 style="margin-top:0;">'+response.data.name+'</h3>';
                            if (response.data.roles && response.data.roles.length) {
                                html += '<div class="permissions-roles-list">';
                                response.data.roles.forEach(function(role) {
                                    html += '<div class="permissions-role-item">';
                                    html += '<strong>'+role.RoleTitle+'</strong>';
                                    if (role.ProgramName) html += ' <span style="color:#888;">('+role.ProgramName+')</span>';
                                    html += '<div style="margin-top:4px;">Permissions: <span style="color:#2271b1;">'+(role.Permissions ? role.Permissions.join(', ') : '—')+'</span></div>';
                                    html += '</div>';
                                });
                                html += '</div>';
                            } else {
                                html += '<div style="color:#888;">No roles assigned.</div>';
                            }
                            $detailsContent.html(html);
                        } else {
                            $detailsContent.html('<div style="color:#888;">No permissions info found.</div>');
                        }
                    },
                    error: function() {
                        $detailsContent.html('<div style="color:#888;">Failed to load permissions info.</div>');
                    }
                });
            });
        });
        </script>
        <?php endif; ?>
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

<script>
jQuery(function($) {
    // Permissions user search and split panel logic
    var $search = $('#permissions-user-search');
    var $list = $('.permissions-user-list');
    var $detailsPanel = $('.permissions-details-panel');
    var $placeholder = $detailsPanel.find('.permissions-details-placeholder');
    var $detailsContent = $detailsPanel.find('.permissions-details-content');
    var lastSearch = '';
    var searchTimeout;
    function renderUserList(users) {
        $list.empty();
        if (!users.length) {
            $list.append('<div style="color:#888;padding:12px;">No users found.</div>');
            return;
        }
        users.forEach(function(user) {
            $list.append('<div class="permissions-user-list-item" data-person-id="'+user.PersonID+'">'+
                $('<div>').text(user.FirstName + ' ' + user.LastName).html()+'</div>');
        });
    }
    function searchUsers(query) {
        $list.html('<div style="color:#888;padding:12px;">Searching...</div>');
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: {
                action: 'search_people',
                nonce: administration_plugin.nonce,
                q: query
            },
            success: function(response) {
                if (response.success && response.data.length) {
                    renderUserList(response.data);
                } else {
                    renderUserList([]);
                }
            },
            error: function() {
                renderUserList([]);
            }
        });
    }
    $search.on('input', function() {
        var val = $search.val().trim();
        if (val === lastSearch) return;
        lastSearch = val;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (val.length < 2) {
                $list.empty();
                return;
            }
            searchUsers(val);
        }, 200);
    });
    $list.on('click', '.permissions-user-list-item', function() {
        var $item = $(this);
        $list.find('.permissions-user-list-item').removeClass('selected');
        $item.addClass('selected');
        var personId = $item.data('person-id');
        $placeholder.hide();
        $detailsContent.show().html('<div class="loading">Loading permissions...</div>');
        // Fetch permissions/roles for this user
        $.ajax({
            url: administration_plugin.ajax_url,
            type: 'POST',
            data: {
                action: 'get_person_permissions',
                nonce: administration_plugin.nonce,
                person_id: personId
            },
            success: function(response) {
                if (response.success && response.data) {
                    var html = '<h3 style="margin-top:0;">'+response.data.name+'</h3>';
                    if (response.data.roles && response.data.roles.length) {
                        html += '<div class="permissions-roles-list">';
                        response.data.roles.forEach(function(role) {
                            html += '<div class="permissions-role-item">';
                            html += '<strong>'+role.RoleTitle+'</strong>';
                            if (role.ProgramName) html += ' <span style="color:#888;">('+role.ProgramName+')</span>';
                            html += '<div style="margin-top:4px;">Permissions: <span style="color:#2271b1;">'+(role.Permissions ? role.Permissions.join(', ') : '—')+'</span></div>';
                            html += '</div>';
                        });
                        html += '</div>';
                    } else {
                        html += '<div style="color:#888;">No roles assigned.</div>';
                    }
                    $detailsContent.html(html);
                } else {
                    $detailsContent.html('<div style="color:#888;">No permissions info found.</div>');
                }
            },
            error: function() {
                $detailsContent.html('<div style="color:#888;">Failed to load permissions info.</div>');
            }
        });
    });
});
</script> 