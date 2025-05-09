<?php
/**
 * Public dashboard header partial
 */
?>
<header class="administration-public-header">
    <div class="header-content">
        <h1><?php _e('Administration Dashboard', 'administration-plugin'); ?></h1>
        <button class="menu-toggle" id="dashboard-menu-toggle">
            <span class="dashicons dashicons-menu"></span>
        </button>
    </div>
    
    <nav class="dashboard-menu" id="dashboard-menu">
        <ul>
            <li>
                <a href="#" class="menu-item" data-page="main">
                    <span class="dashicons dashicons-dashboard"></span>
                    <?php _e('Dashboard', 'administration-plugin'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item" data-page="programs">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    <?php _e('Programs', 'administration-plugin'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item" data-page="people">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e('People', 'administration-plugin'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item" data-page="volunteer-ops">
                    <span class="dashicons dashicons-businessperson"></span>
                    <?php _e('Volunteer Operations', 'administration-plugin'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item" data-page="hr">
                    <span class="dashicons dashicons-id"></span>
                    <?php _e('HR', 'administration-plugin'); ?>
                </a>
            </li>
        </ul>
    </nav>
</header> 