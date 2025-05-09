<?php
// List all programs in the database
if (!defined('ABSPATH')) exit;

global $wpdb;
$programs_table = $wpdb->prefix . 'core_programs';
$programs = $wpdb->get_results("SELECT ProgramID, ProgramName, ProgramDescription, StartDate, EndDate, ActiveFlag FROM $programs_table ORDER BY ProgramName ASC");
?>
<div class="widget-list programs-list">
    <?php if ($programs && count($programs) > 0): ?>
        <ul class="programs-ul">
            <?php foreach ($programs as $program): ?>
                <li class="program-item <?php echo $program->ActiveFlag ? 'active' : 'inactive'; ?>">
                    <div class="program-title">
                        <strong><?php echo esc_html($program->ProgramName); ?></strong>
                        <?php if (!$program->ActiveFlag): ?>
                            <span class="program-status inactive">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($program->ProgramDescription)): ?>
                        <div class="program-description"><?php echo esc_html($program->ProgramDescription); ?></div>
                    <?php endif; ?>
                    <div class="program-dates">
                        <?php if ($program->StartDate): ?>
                            <span class="program-date">Start: <?php echo esc_html($program->StartDate); ?></span>
                        <?php endif; ?>
                        <?php if ($program->EndDate): ?>
                            <span class="program-date">End: <?php echo esc_html($program->EndDate); ?></span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><?php _e('No programs found.', 'administration-plugin'); ?></p>
    <?php endif; ?>
</div>
<style>
.programs-ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
.program-item {
    padding: 16px 0;
    border-bottom: 1px solid #e3e7ee;
}
.program-item:last-child {
    border-bottom: none;
}
.program-title {
    font-size: 1.1rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.program-status.inactive {
    color: #d63638;
    font-size: 0.9em;
    background: #fcf0f1;
    border-radius: 4px;
    padding: 2px 8px;
    margin-left: 8px;
}
.program-description {
    color: #555;
    font-size: 0.98em;
    margin-bottom: 4px;
}
.program-dates {
    font-size: 0.92em;
    color: #888;
    margin-top: 2px;
}
.program-date {
    margin-right: 12px;
}
</style> 