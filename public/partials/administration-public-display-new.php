<?php
/**
 * Administration plugin public display
 */
?>

<div class="administration-container">
    <div class="administration-header">
        <div class="menu-toggle">
            <span class="hamburger"></span>
        </div>
        <h2>Administration</h2>
        <div class="administration-search">
            <input type="text" placeholder="Search">
        </div>
        <div class="administration-user">
            <span class="user-icon">J</span>
        </div>
    </div>
    
    <div class="administration-content">
        <div class="administration-sidebar">
            <ul class="sidebar-menu">
                <li class="active" data-page="home">
                    <a href="#"><i class="dashicons dashicons-admin-home"></i> <span>Home</span></a>
                </li>
                <li data-page="programs">
                    <a href="#"><i class="dashicons dashicons-calendar"></i> <span>Programs</span></a>
                </li>
                <li data-page="members">
                    <a href="#"><i class="dashicons dashicons-groups"></i> <span>Members</span></a>
                </li>
            </ul>
        </div>
        
        <div class="administration-main">
            <!-- Home Page -->
            <div id="home-page" class="page-content">
                <div class="home-grid">
                    <!-- Programs Overview Box -->
                    <div class="home-section">
                        <div class="section-header">
                            <h3>Programs Overview</h3>
                            <div class="section-actions">
                                <button class="add-button">Quick Add</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="programs-overview">
                                <div class="empty-state">
                                    <p>No recent programs to display.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Members Overview Box -->
                    <div class="home-section">
                        <div class="section-header">
                            <h3>Members Overview</h3>
                            <div class="section-actions">
                                <button class="add-button">Quick Add</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="members-overview">
                                <div class="empty-state">
                                    <p>No recent member activity to display.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programs Page -->
            <div id="programs-page" class="page-content" style="display: none;">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Programs Management</h3>
                        <div class="section-actions">
                            <button class="add-button">Add Program</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <div class="programs-management">
                            <div class="empty-state">
                                <p>No programs added yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Page -->
            <div id="members-page" class="page-content" style="display: none;">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Members Management</h3>
                        <div class="section-actions">
                            <button class="add-button">Add Member</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <div class="members-management">
                            <div class="members-list">
                                <?php
                                // Your existing members list code here
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 