<?php
/**
 * HR admin page template
 */
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="administration-hr">
        <div class="hr-header">
            <button class="button button-primary" id="add-job">Post New Job</button>
            <button class="button" id="manage-applications">Manage Applications</button>
            <button class="button" id="manage-interviews">Manage Interviews</button>
        </div>

        <div class="hr-content">
            <div class="jobs-list">
                <h2>Job Postings</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Posted Date</th>
                            <th>Closing Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Jobs will be loaded here via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="applications-list">
                <h2>Recent Applications</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Submission Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Applications will be loaded here via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Job Modal Template -->
<div id="job-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Job Details</h2>
        <form id="job-form">
            <div class="form-field">
                <label for="job-title">Title</label>
                <input type="text" id="job-title" name="job-title" required>
            </div>
            <div class="form-field">
                <label for="job-department">Department</label>
                <input type="text" id="job-department" name="job-department" required>
            </div>
            <div class="form-field">
                <label for="job-location">Location</label>
                <input type="text" id="job-location" name="job-location">
            </div>
            <div class="form-field">
                <label for="job-type">Job Type</label>
                <select id="job-type" name="job-type" required>
                    <option value="full-time">Full Time</option>
                    <option value="part-time">Part Time</option>
                    <option value="contract">Contract</option>
                    <option value="temporary">Temporary</option>
                </select>
            </div>
            <div class="form-field">
                <label for="job-description">Description</label>
                <textarea id="job-description" name="job-description" required></textarea>
            </div>
            <div class="form-field">
                <label for="job-requirements">Requirements</label>
                <textarea id="job-requirements" name="job-requirements"></textarea>
            </div>
            <div class="form-field">
                <label for="job-responsibilities">Responsibilities</label>
                <textarea id="job-responsibilities" name="job-responsibilities"></textarea>
            </div>
            <div class="form-field">
                <label for="job-salary">Salary Range</label>
                <input type="text" id="job-salary" name="job-salary">
            </div>
            <div class="form-field">
                <label for="job-closing-date">Closing Date</label>
                <input type="date" id="job-closing-date" name="job-closing-date">
            </div>
            <div class="form-field">
                <label for="job-status">Status</label>
                <select id="job-status" name="job-status" required>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Job</button>
                <button type="button" class="button" id="cancel-job">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Application Modal Template -->
<div id="application-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Application Details</h2>
        <form id="application-form">
            <div class="form-field">
                <label for="application-job">Job</label>
                <select id="application-job" name="application-job" required>
                    <!-- Jobs will be loaded here via JavaScript -->
                </select>
            </div>
            <div class="form-field">
                <label for="application-status">Status</label>
                <select id="application-status" name="application-status" required>
                    <option value="new">New</option>
                    <option value="reviewing">Reviewing</option>
                    <option value="interviewing">Interviewing</option>
                    <option value="offered">Offered</option>
                    <option value="hired">Hired</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="form-field">
                <label for="application-notes">Notes</label>
                <textarea id="application-notes" name="application-notes"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Application</button>
                <button type="button" class="button" id="cancel-application">Cancel</button>
            </div>
        </form>
    </div>
</div> 