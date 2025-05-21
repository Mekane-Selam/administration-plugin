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

// Fetch people for Program Owner select
if (!function_exists('administration_plugin_get_people')) {
    function administration_plugin_get_people() {
        global $wpdb;
        $table = $wpdb->prefix . 'core_person';
        return $wpdb->get_results("SELECT PersonID, FirstName, LastName FROM $table ORDER BY LastName, FirstName");
    }
}
$people = administration_plugin_get_people();

// Define program types centrally
$program_types = array('Education', 'Health', 'Social');
?>
<div class="programs-content">
    <div class="programs-actions">
        <button class="add-program-btn">Add New Program</button>
    </div>
    <div class="programs-list">
        <!-- Programs will be loaded here -->
    </div>
</div>

<!-- Add Program Modal -->
<div id="add-program-modal" class="modal">
    <div class="modal-content">
        <span class="close" id="close-add-program-modal" tabindex="0" role="button" aria-label="Close">&times;</span>
        <h2><?php _e('Add New Program', 'administration-plugin'); ?></h2>
        <form id="add-program-form">
            <div class="form-field">
                <label for="program-name"><?php _e('Program Name', 'administration-plugin'); ?></label>
                <input type="text" id="program-name" name="program_name" required>
            </div>
            <div class="form-field">
                <label for="program-type"><?php _e('Program Type', 'administration-plugin'); ?></label>
                <select id="program-type" name="program_type" required>
                    <?php foreach ($program_types as $type): ?>
                        <option value="<?php echo strtolower($type); ?>"><?php echo esc_html($type); ?></option>
                    <?php endforeach; ?>
                </select>
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
            <div class="form-field">
                <label for="program-owner"><?php _e('Program Owner', 'administration-plugin'); ?></label>
                <select id="program-owner" name="program_owner" required>
                    <option value=""><?php _e('Select Owner', 'administration-plugin'); ?></option>
                    <?php if (!empty($people)): ?>
                        <?php foreach ($people as $person): ?>
                            <option value="<?php echo esc_attr($person->PersonID); ?>"><?php echo esc_html($person->FirstName . ' ' . $person->LastName); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled><?php _e('No people found. Please add people first.', 'administration-plugin'); ?></option>
                    <?php endif; ?>
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