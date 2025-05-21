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