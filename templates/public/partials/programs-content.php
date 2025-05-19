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
    <div class="programs-header">
        <h2><?php _e('Programs', 'administration-plugin'); ?></h2>
        <button id="add-program-btn" class="button button-primary"><?php _e('Add Program', 'administration-plugin'); ?></button>
    </div>

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

<!-- Add Program Modal -->
<div id="add-program-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-add-program-modal">&times;</span>
        <h2><?php _e('Add New Program', 'administration-plugin'); ?></h2>
        <form id="add-program-form">
            <div class="form-field">
                <label for="program-name"><?php _e('Program Name', 'administration-plugin'); ?></label>
                <input type="text" id="program-name" name="program_name" required>
            </div>
            <div class="form-field">
                <label for="program-description"><?php _e('Description', 'administration-plugin'); ?></label>
                <textarea id="program-description" name="description"></textarea>
            </div>
            <div class="form-field">
                <label for="program-start-date"><?php _e('Start Date', 'administration-plugin'); ?></label>
                <input type="date" id="program-start-date" name="start_date">
            </div>
            <div class="form-field">
                <label for="program-end-date"><?php _e('End Date', 'administration-plugin'); ?></label>
                <input type="date" id="program-end-date" name="end_date">
            </div>
            <div class="form-field">
                <label for="program-status"><?php _e('Status', 'administration-plugin'); ?></label>
                <select id="program-status" name="status">
                    <option value="active"><?php _e('Active', 'administration-plugin'); ?></option>
                    <option value="inactive"><?php _e('Inactive', 'administration-plugin'); ?></option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary"><?php _e('Save Program', 'administration-plugin'); ?></button>
                <button type="button" class="button" id="cancel-add-program"><?php _e('Cancel', 'administration-plugin'); ?></button>
            </div>
        </form>
        <div id="add-program-message"></div>
    </div>
</div>

<!-- Program Details Modal -->
<div id="program-details-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Program Details', 'administration-plugin'); ?></h2>
        <div id="program-details-content"></div>
    </div>
</div> 