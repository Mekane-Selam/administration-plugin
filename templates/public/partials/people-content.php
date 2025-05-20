<div class="dashboard-section parish-content two-column-layout">
    <div class="left-column">
        <div class="people-filters">
            <input type="text" id="people-content-filter-input" class="people-filter-input" placeholder="Filter people..." />
        </div>
        <div class="people-actions-row">
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
        <!-- Additional filter/sort UI can go here -->
    </div>
    <div class="right-column">
        <div class="people-list-content">
            <p>People list/details will appear here.</p>
        </div>
    </div>
</div> 