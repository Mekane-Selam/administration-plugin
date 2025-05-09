<?php
// Fetch programs from the database
if (!function_exists('administration_plugin_get_programs')) {
    function administration_plugin_get_programs() {
        global $wpdb;
        $table = $wpdb->prefix . 'core_programs';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY ProgramID DESC");
    }
}
$programs = administration_plugin_get_programs();
?>
<div class="widget-list programs-list">
    <?php if ($programs && count($programs) > 0): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Program Name</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($programs as $program): ?>
                    <tr>
                        <td><?php echo esc_html($program->ProgramName); ?></td>
                        <td><?php echo esc_html($program->Description); ?></td>
                        <td><?php echo esc_html($program->StartDate); ?></td>
                        <td><?php echo esc_html($program->EndDate); ?></td>
                        <td><?php echo esc_html(ucfirst($program->Status)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No programs found.</p>
    <?php endif; ?>
</div> 