<div class="dashboard-section parish-content two-column-layout">
    <div class="left-column">
        <div class="people-filters">
            <input type="text" id="people-content-filter-input" class="people-filter-input" placeholder="Filter people..." />
        </div>
        <div class="people-actions-row people-widget-actions">
            <button id="add-person-content-btn" class="button button-primary" title="Add Person">Add</button>
            <button id="sync-users-content-btn" class="button" title="Sync Users"><span class="dashicons dashicons-update"></span> Sync</button>
            <div class="sort-dropdown-wrapper">
                <button id="sort-people-btn" class="button">Sort by <span class="dashicons dashicons-arrow-down"></span></button>
                <div class="sort-dropdown" style="display:none;">
                    <ul>
                        <li><a href="#">First Name</a></li>
                        <li><a href="#">Last Name</a></li>
                        <li><a href="#">Email</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="people-list-content">
            <p>People list/details will appear here.</p>
        </div>
    </div>
    <div class="right-column">
        <div id="person-details-panel" class="person-details-panel" style="display:none;">
            <div class="person-details-section person-details-general">
                <div class="person-details-section-header">
                    <h3>General</h3>
                    <button class="button person-details-edit-btn" data-section="general"><span class="dashicons dashicons-edit"></span> Edit</button>
                </div>
                <div class="person-details-section-content" id="person-details-general-content"></div>
            </div>
            <div class="person-details-section person-details-family">
                <div class="person-details-section-header">
                    <h3>Family</h3>
                    <button class="button person-details-edit-btn" data-section="family"><span class="dashicons dashicons-edit"></span> Edit</button>
                </div>
                <div class="person-details-section-content" id="person-details-family-content"></div>
            </div>
            <div class="person-details-section person-details-roles">
                <div class="person-details-section-header">
                    <h3>Roles</h3>
                    <button class="button person-details-edit-btn" data-section="roles"><span class="dashicons dashicons-edit"></span> Edit</button>
                </div>
                <div class="person-details-section-content" id="person-details-roles-content"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Person Modal -->
<div id="add-person-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Add Person</h2>
        <form id="add-person-form">
            <div class="form-field">
                <label for="person-first-name">First Name</label>
                <input type="text" id="person-first-name" name="first_name" required>
            </div>
            <div class="form-field">
                <label for="person-last-name">Last Name</label>
                <input type="text" id="person-last-name" name="last_name" required>
            </div>
            <div class="form-field">
                <label for="person-email">Email</label>
                <input type="email" id="person-email" name="email" required>
            </div>
            <div class="form-field">
                <label for="person-phone">Phone</label>
                <input type="tel" id="person-phone" name="phone">
            </div>
            <div class="form-field">
                <label for="person-address-line1">Address Line 1</label>
                <input type="text" id="person-address-line1" name="address_line1">
            </div>
            <div class="form-field">
                <label for="person-address-line2">Address Line 2</label>
                <input type="text" id="person-address-line2" name="address_line2">
            </div>
            <div class="form-field">
                <label for="person-city">City</label>
                <input type="text" id="person-city" name="city">
            </div>
            <div class="form-field">
                <label for="person-state">State</label>
                <input type="text" id="person-state" name="state">
            </div>
            <div class="form-field">
                <label for="person-zip">ZIP</label>
                <input type="text" id="person-zip" name="zip">
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Add Person</button>
                <button type="button" class="button" id="cancel-add-person">Cancel</button>
            </div>
        </form>
        <div id="add-person-message"></div>
    </div>
</div> 