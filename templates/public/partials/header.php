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
                <a href="#" class="menu-item" data-page="parish">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e('Parish', 'administration-plugin'); ?>
                </a>
            </li>
            <li>
                <a href="#" class="menu-item" data-page="calendar">
                    <span class="dashicons dashicons-calendar"></span>
                    <?php _e('Calendar', 'administration-plugin'); ?>
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