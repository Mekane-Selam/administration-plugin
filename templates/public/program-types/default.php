<?php
global $program_page_data;
$program = $program_page_data;
?>
<div class="program-type-page default-program-page">
    <a href="/dashboard" class="go-back-dashboard-btn">&larr; Go back to Dashboard</a>
    <div class="program-type-card">
        <h1><?php echo esc_html($program->ProgramName); ?></h1>
        <div class="program-meta">
            <div><strong>Type:</strong> <?php echo esc_html($program->ProgramType); ?></div>
            <div><strong>Description:</strong> <?php echo esc_html($program->ProgramDescription); ?></div>
            <div><strong>Start Date:</strong> <?php echo esc_html($program->StartDate); ?></div>
            <div><strong>End Date:</strong> <?php echo esc_html($program->EndDate); ?></div>
            <div><strong>Status:</strong> <?php echo $program->ActiveFlag ? 'Active' : 'Inactive'; ?></div>
        </div>
    </div>
</div> 