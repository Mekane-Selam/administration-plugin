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
<div class="programs-content">
    <?php if ($programs && count($programs) > 0): ?>
        <div class="programs-grid">
            <?php foreach ($programs as $program): ?>
                <div class="program-card" data-program-id="<?php echo esc_attr($program->ProgramID); ?>">
                    <h3><?php echo esc_html($program->ProgramName); ?></h3>
                    <p class="program-type"><?php echo esc_html($program->ProgramType); ?></p>
                    <p class="program-dates"><?php echo esc_html($program->StartDate); ?> - <?php echo esc_html($program->EndDate); ?></p>
                    <p class="program-status"><?php echo $program->ActiveFlag ? 'Active' : 'Inactive'; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No programs found.</p>
    <?php endif; ?>
</div>

<!-- Program Details Modal -->
<div id="program-details-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Program Details</h2>
        <div id="program-details-content"></div>
    </div>
</div> 