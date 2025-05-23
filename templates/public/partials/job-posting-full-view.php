<?php
// Modular job posting full view
?>
<div class="job-posting-full-view">
    <button class="button back-to-dashboard-btn" id="back-to-dashboard-btn">&larr; Back to Dashboard</button>
    <div class="person-details-card" style="margin-top: 24px;">
        <h2 style="margin-bottom: 18px; color: #2271b1; font-size: 1.5rem; font-weight: 700;">Job Posting: <?php echo esc_html($job->Title); ?></h2>
        <div class="person-details-grid">
            <div class="person-detail-row"><span class="person-detail-label">Status</span><span class="person-detail-value"><?php echo esc_html($job->Status); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Department</span><span class="person-detail-value"><?php echo esc_html($job->DepartmentName); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Job Type</span><span class="person-detail-value"><?php echo esc_html($job->JobType); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Location</span><span class="person-detail-value"><?php echo esc_html($job->Location); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Salary Range</span><span class="person-detail-value"><?php echo esc_html($job->SalaryRange); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Posted Date</span><span class="person-detail-value"><?php echo esc_html($job->PostedDate); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Closing Date</span><span class="person-detail-value"><?php echo esc_html($job->ClosingDate); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Program</span><span class="person-detail-value"><?php echo $program ? esc_html($program->ProgramName) : esc_html($job->ProgramID); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Program Type</span><span class="person-detail-value"><?php echo $program ? esc_html($program->ProgramType) : ''; ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Reports To</span><span class="person-detail-value"><?php echo $reports_to ? esc_html($reports_to) : esc_html($job->ReportsTo); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Internal?</span><span class="person-detail-value"><?php echo $job->IsInternal ? 'Yes' : 'No'; ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Description</span><span class="person-detail-value"><?php echo esc_html($job->Description); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Requirements</span><span class="person-detail-value"><?php echo esc_html($job->Requirements); ?></span></div>
            <div class="person-detail-row"><span class="person-detail-label">Responsibilities</span><span class="person-detail-value"><?php echo esc_html($job->Responsibilities); ?></span></div>
        </div>
    </div>
    <div class="job-posting-sections" style="margin-top: 36px;">
        <div class="person-details-card" style="margin-bottom: 32px;">
            <h3 style="color: #2271b1; font-size: 1.15rem; font-weight: 600; margin-bottom: 12px;">Applications</h3>
            <div class="job-applications-list">(Applications list coming soon...)</div>
        </div>
        <div class="person-details-card" style="margin-bottom: 32px;">
            <h3 style="color: #2271b1; font-size: 1.15rem; font-weight: 600; margin-bottom: 12px;">Edit Job Details</h3>
            <div class="job-edit-form">(Edit form coming soon...)</div>
        </div>
        <div class="person-details-card">
            <h3 style="color: #2271b1; font-size: 1.15rem; font-weight: 600; margin-bottom: 12px;">Track Applications & Interviews</h3>
            <div class="job-tracking-section">(Tracking features coming soon...)</div>
        </div>
    </div>
</div> 