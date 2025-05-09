<?php
/**
 * Volunteer Operations admin page template
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="administration-volunteer-ops">
        <div class="notice notice-info">
            <p><?php _e('Admin settings page', 'administration-plugin'); ?></p>
        </div>
    </div>
</div>

<!-- Volunteer Modal Template -->
<div id="volunteer-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Volunteer Details</h2>
        <form id="volunteer-form">
            <div class="form-field">
                <label for="volunteer-person">Person</label>
                <select id="volunteer-person" name="volunteer-person" required>
                    <!-- People will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-field">
                <label for="volunteer-skills">Skills</label>
                <textarea id="volunteer-skills" name="volunteer-skills"></textarea>
            </div>
            <div class="form-field">
                <label for="volunteer-availability">Availability Notes</label>
                <textarea id="volunteer-availability" name="volunteer-availability"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Volunteer</button>
                <button type="button" class="button" id="cancel-volunteer">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Shift Modal Template -->
<div id="shift-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Shift Details</h2>
        <form id="shift-form">
            <div class="form-field">
                <label for="shift-date">Date</label>
                <input type="date" id="shift-date" name="shift-date" required>
            </div>
            <div class="form-field">
                <label for="shift-start-time">Start Time</label>
                <input type="time" id="shift-start-time" name="shift-start-time" required>
            </div>
            <div class="form-field">
                <label for="shift-end-time">End Time</label>
                <input type="time" id="shift-end-time" name="shift-end-time" required>
            </div>
            <div class="form-field">
                <label for="shift-program">Program</label>
                <select id="shift-program" name="shift-program" required>
                    <!-- Programs will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-field">
                <label for="shift-volunteer">Volunteer</label>
                <select id="shift-volunteer" name="shift-volunteer" required>
                    <!-- Volunteers will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-field">
                <label for="shift-tasks">Tasks</label>
                <select id="shift-tasks" name="shift-tasks" multiple>
                    <!-- Tasks will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Shift</button>
                <button type="button" class="button" id="cancel-shift">Cancel</button>
            </div>
        </form>
    </div>
</div> 