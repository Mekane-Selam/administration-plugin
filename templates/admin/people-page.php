<?php
/**
 * People admin page template
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="administration-people">
        <div class="notice notice-info">
            <p><?php _e('Admin settings page', 'administration-plugin'); ?></p>
        </div>
    </div>
</div>

<!-- Person Modal Template -->
<div id="person-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Person Details</h2>
        <form id="person-form">
            <div class="form-field">
                <label for="person-first-name">First Name</label>
                <input type="text" id="person-first-name" name="person-first-name" required>
            </div>
            <div class="form-field">
                <label for="person-last-name">Last Name</label>
                <input type="text" id="person-last-name" name="person-last-name" required>
            </div>
            <div class="form-field">
                <label for="person-email">Email</label>
                <input type="email" id="person-email" name="person-email">
            </div>
            <div class="form-field">
                <label for="person-phone">Phone</label>
                <input type="tel" id="person-phone" name="person-phone">
            </div>
            <div class="form-field">
                <label for="person-address">Address</label>
                <input type="text" id="person-address" name="person-address">
            </div>
            <div class="form-field">
                <label for="person-city">City</label>
                <input type="text" id="person-city" name="person-city">
            </div>
            <div class="form-field">
                <label for="person-state">State</label>
                <input type="text" id="person-state" name="person-state">
            </div>
            <div class="form-field">
                <label for="person-zip">ZIP</label>
                <input type="text" id="person-zip" name="person-zip">
            </div>
            <div class="form-field">
                <label for="person-roles">Roles</label>
                <select id="person-roles" name="person-roles" multiple>
                    <!-- Roles will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Person</button>
                <button type="button" class="button" id="cancel-person">Cancel</button>
            </div>
        </form>
    </div>
</div> 