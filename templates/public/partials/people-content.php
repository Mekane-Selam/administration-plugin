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
        <!-- Reserved for future use -->
    </div>
</div>
<!-- Add Person Modal -->
<div id="add-person-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="close-add-person-modal">&times;</span>
        <h2>Add Person</h2>
        <form id="add-person-form">
            <div class="form-field">
                <label for="person-first-name">First Name</label>
                <input type="text" id="person-first-name" name="first_name" required />
            </div>
            <div class="form-field">
                <label for="person-last-name">Last Name</label>
                <input type="text" id="person-last-name" name="last_name" required />
            </div>
            <div class="form-field">
                <label for="person-email">Email</label>
                <input type="email" id="person-email" name="email" required />
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Person</button>
                <button type="button" class="button" id="cancel-add-person">Cancel</button>
            </div>
        </form>
        <div id="add-person-message"></div>
    </div>
</div> 