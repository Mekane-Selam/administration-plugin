<?php
/**
 * Main dashboard content partial
 */
?>
<div class="administration-dashboard-content">
    <div class="dashboard-grid">
        <!-- Programs Overview -->
        <div class="dashboard-widget" id="programs-overview">
            <div class="widget-header">
                <h2><?php _e('Programs Overview', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="programs-list">
                    <!-- Programs will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading programs...', 'administration-plugin'); ?></div>
                </div>
            </div>
        </div>

        <!-- Parish Overview -->
        <div class="dashboard-widget" id="parish-overview">
            <div class="widget-header">
                <h2><?php _e('Parish Overview', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="parish-list">
                    <!-- Parish will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading parish...', 'administration-plugin'); ?></div>
                </div>
            </div>
        </div>

        <!-- Calendar Overview -->
        <div class="dashboard-widget" id="calendar-overview">
            <div class="widget-header">
                <h2><?php _e('Calendar Overview', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="calendar-list">
                    <!-- Calendar will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading calendar...', 'administration-plugin'); ?></div>
                </div>
            </div>
        </div>

        <!-- HR Overview -->
        <div class="dashboard-widget" id="hr-overview">
            <div class="widget-header">
                <h2><?php _e('HR Overview', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="hr-list">
                    <!-- HR information will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading HR information...', 'administration-plugin'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div> 