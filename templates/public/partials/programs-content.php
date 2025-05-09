<div class="dashboard-section programs-content">
    <div class="programs-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <h2>Programs</h2>
        <button class="button button-primary" id="add-program-btn">Add Program</button>
    </div>
    <div id="programs-list-widget">
        <?php include ADMINISTRATION_PLUGIN_PATH . 'templates/public/partials/widgets/programs-list.php'; ?>
    </div>

    <!-- Add Program Modal -->
    <div id="add-program-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="close-add-program-modal">&times;</span>
            <h2>Add Program</h2>
            <form id="add-program-form">
                <div class="form-field">
                    <label for="program-name">Program Name</label>
                    <input type="text" id="program-name" name="program_name" required>
                </div>
                <div class="form-field">
                    <label for="program-description">Description</label>
                    <textarea id="program-description" name="description"></textarea>
                </div>
                <div class="form-field">
                    <label for="program-start-date">Start Date</label>
                    <input type="date" id="program-start-date" name="start_date">
                </div>
                <div class="form-field">
                    <label for="program-end-date">End Date</label>
                    <input type="date" id="program-end-date" name="end_date">
                </div>
                <div class="form-field">
                    <label for="program-status">Status</label>
                    <select id="program-status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="button button-primary">Save Program</button>
                    <button type="button" class="button" id="cancel-add-program">Cancel</button>
                </div>
            </form>
            <div id="add-program-message"></div>
        </div>
    </div>
</div> 