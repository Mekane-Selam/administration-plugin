<?php
// Get staff data from the database
global $wpdb;
$staff_members = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}hr_staff ORDER BY name ASC");
?>

<div class="dashboard-section hr-content">
    <h2>HR Dashboard</h2>
    
    <div class="hr-grid">
        <!-- Top Row -->
        <div class="hr-row">
            <!-- Staff List Section -->
            <div class="hr-column staff-list">
                <div class="card">
                    <div class="card-header">
                        <h3>Staff Directory</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($staff_members)) : ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staff_members as $staff) : ?>
                                            <tr>
                                                <td><?php echo esc_html($staff->name); ?></td>
                                                <td><?php echo esc_html($staff->position); ?></td>
                                                <td><?php echo esc_html($staff->department); ?></td>
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

            <!-- Top Right Placeholder -->
            <div class="hr-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Section Title</h3>
                    </div>
                    <div class="card-body">
                        <p>Content coming soon...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="hr-row">
            <!-- Bottom Left Placeholder -->
            <div class="hr-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Section Title</h3>
                    </div>
                    <div class="card-body">
                        <p>Content coming soon...</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Right Placeholder -->
            <div class="hr-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Section Title</h3>
                    </div>
                    <div class="card-body">
                        <p>Content coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 