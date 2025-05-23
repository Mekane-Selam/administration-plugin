<?php
/**
 * Programs admin page template
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="administration-programs">
        <div class="notice notice-info">
            <p><?php _e('Admin settings page', 'administration-plugin'); ?></p>
        </div>
    </div>
</div>

<!-- Program Modal Template -->
<div id="program-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Program Details</h2>
        <form id="program-form">
            <div class="form-field">
                <label for="program-name">Program Name</label>
                <input type="text" id="program-name" name="program-name" required>
            </div>
            <div class="form-field">
                <label for="program-description">Description</label>
                <textarea id="program-description" name="program-description"></textarea>
            </div>
            <div class="form-field">
                <label for="program-start-date">Start Date</label>
                <input type="date" id="program-start-date" name="program-start-date">
            </div>
            <div class="form-field">
                <label for="program-end-date">End Date</label>
                <input type="date" id="program-end-date" name="program-end-date">
            </div>
            <div class="form-field">
                <label for="program-status">Status</label>
                <select id="program-status" name="program-status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Program</button>
                <button type="button" class="button" id="cancel-program">Cancel</button>
            </div>
        </form>
    </div>
</div> 