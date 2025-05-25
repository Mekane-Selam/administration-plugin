<?php
// Modular job posting full view
?>
<div class="job-posting-full-view" data-job-posting-id="<?php echo esc_attr($job->JobPostingID); ?>">
    <button class="button button-primary back-to-dashboard-btn" id="back-to-dashboard-btn">&larr; Back to Dashboard</button>
    <div class="person-details-card" style="margin-top: 24px;">
        <h2 style="margin-bottom: 18px; color: #2271b1; font-size: 1.5rem; font-weight: 700; padding-left: 18px;">Job Posting: <?php echo esc_html($job->Title); ?></h2>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
            <h3 style="color: #2271b1; font-size: 1.15rem; font-weight: 600; margin: 0; padding-left: 18px;">General Info</h3>
            <button class="button button-secondary person-details-edit-btn" id="edit-job-posting-btn" data-job-posting-id="<?php echo esc_attr($job->JobPostingID); ?>" style="margin-right: 18px; min-width: 140px;">Edit Job Posting</button>
        </div>
        <div class="job-posting-details-two-col">
            <div class="job-posting-details-left">
                <div class="job-posting-details-left-inner">
                    <div class="person-detail-row job-detail-status" style="grid-column: 1 / span 2;">
                        <span class="person-detail-label">Status</span><span class="person-detail-value"><?php echo esc_html($job->Status); ?></span>
                    </div>
                    <div class="person-detail-row job-detail-balance" style="grid-column: 1 / span 2; align-items: center; margin-bottom: 6px;">
                        <span class="person-detail-label">Drive Folder</span>
                        <?php if (!empty($job->DriveFolderID)): ?>
                        <a href="https://drive.google.com/drive/folders/<?php echo esc_attr($job->DriveFolderID); ?>" target="_blank" class="job-drive-link" title="Open Google Drive Folder">
                            <span class="job-drive-icon" aria-hidden="true" style="display:inline-flex;align-items:center;">
                                <!-- Google Drive SVG icon -->
                                <svg width="22" height="22" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align:middle;"><g><polygon fill="#FFC107" points="21.6,27.7 13.1,42 34.9,42 43.4,27.7"/><polygon fill="#1976D2" points="6.6,27.7 13.1,42 21.6,27.7 15.1,14.3"/><polygon fill="#388E3C" points="32.9,14.3 26.4,27.7 34.9,42 43.4,27.7"/><polygon fill="#FFA000" points="21.6,27.7 26.4,27.7 32.9,14.3 15.1,14.3"/></g></svg>
                            </span>
                            <span class="job-drive-label">Open Folder</span>
                        </a>
                        <?php else: ?>
                        <span class="person-detail-value" style="color: #888;">—</span>
                        <?php endif; ?>
                    </div>
                    <div class="job-posting-details-left-col">
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Department</span><span class="person-detail-value"><?php echo esc_html($job->DepartmentName); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Job Type</span><span class="person-detail-value"><?php echo esc_html($job->JobType); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Location</span><span class="person-detail-value"><?php echo esc_html($job->Location); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Internal?</span><span class="person-detail-value"><?php echo $job->IsInternal ? 'Yes' : 'No'; ?></span></div>
                        <div class="person-detail-row job-detail-balance">
                            <span class="person-detail-label">Drive Folder</span>
                            <?php if (!empty($job->DriveFolderID)): ?>
                            <a href="https://drive.google.com/drive/folders/<?php echo esc_attr($job->DriveFolderID); ?>" target="_blank" class="job-drive-link" title="Open Google Drive Folder">
                                <span class="job-drive-icon" aria-hidden="true">
                                    <svg width="22" height="22" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><g><polygon fill="#FFC107" points="21.6,27.7 13.1,42 34.9,42 43.4,27.7"/><polygon fill="#1976D2" points="6.6,27.7 13.1,42 21.6,27.7 15.1,14.3"/><polygon fill="#388E3C" points="32.9,14.3 26.4,27.7 34.9,42 43.4,27.7"/><polygon fill="#FFA000" points="21.6,27.7 26.4,27.7 32.9,14.3 15.1,14.3"/></g></svg>
                                </span>
                                <span class="job-drive-label">Open Folder</span>
                            </a>
                            <?php else: ?>
                            <span class="person-detail-value" style="color: #888;">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="job-posting-details-left-col">
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Posted Date</span><span class="person-detail-value"><?php echo esc_html($job->PostedDate); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Closing Date</span><span class="person-detail-value"><?php echo esc_html($job->ClosingDate); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Program</span><span class="person-detail-value" data-program-id="<?php echo esc_attr($job->ProgramID); ?>"><?php echo $program ? esc_html($program->ProgramName) : esc_html($job->ProgramID); ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Program Type</span><span class="person-detail-value"><?php echo $program ? esc_html($program->ProgramType) : ''; ?></span></div>
                        <div class="person-detail-row job-detail-balance"><span class="person-detail-label">Reports To</span><span class="person-detail-value" data-person-id="<?php echo esc_attr($job->ReportsTo); ?>"><?php echo $reports_to ? esc_html($reports_to) : esc_html($job->ReportsTo); ?></span></div>
                    </div>
                </div>
            </div>
            <div class="job-posting-details-right">
                <div class="person-detail-row job-detail-long"><span class="person-detail-label">Description</span><span class="person-detail-value job-detail-long-value"><?php echo esc_html($job->Description); ?></span></div>
                <div class="person-detail-row job-detail-long"><span class="person-detail-label">Requirements</span><span class="person-detail-value job-detail-long-value"><?php echo esc_html($job->Requirements); ?></span></div>
                <div class="person-detail-row job-detail-long"><span class="person-detail-label">Responsibilities</span><span class="person-detail-value job-detail-long-value"><?php echo esc_html($job->Responsibilities); ?></span></div>
            </div>
        </div>
    </div>
    <div class="job-posting-sections" style="margin-top: 36px;">
        <div class="person-details-card">
            <h3 style="color: #2271b1; font-size: 1.15rem; font-weight: 600; margin-bottom: 12px;">Track Applications & Interviews</h3>
            <div class="job-tracking-section">(Tracking features coming soon...)</div>
        </div>
    </div>
</div> 