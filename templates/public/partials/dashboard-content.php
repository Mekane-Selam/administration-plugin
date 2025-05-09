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

        <!-- People Overview -->
        <div class="dashboard-widget" id="people-overview">
            <div class="widget-header">
                <h2><?php _e('People Overview', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="people-list">
                    <!-- People will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading people...', 'administration-plugin'); ?></div>
                </div>
            </div>
        </div>

        <!-- Volunteer Operations -->
        <div class="dashboard-widget" id="volunteer-ops-overview">
            <div class="widget-header">
                <h2><?php _e('Volunteer Operations', 'administration-plugin'); ?></h2>
            </div>
            <div class="widget-content">
                <div class="volunteer-ops-list">
                    <!-- Volunteer operations will be loaded via JavaScript -->
                    <div class="loading"><?php _e('Loading volunteer operations...', 'administration-plugin'); ?></div>
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