<div class="administration-container">
    <div class="administration-header">
        <div class="menu-toggle">
            <span class="hamburger"></span>
        </div>
        <h2>Administration</h2>
        <div class="administration-search">
            <input type="text" placeholder="Search Home">
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
            <div id="home-page" class="page-content active">
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
                                <?php if (!empty($programs)) : ?>
                                    <?php foreach ($programs as $program) : ?>
                                        <div class="program-card">
                                            <h4><?php echo esc_html($program->ProgramName); ?></h4>
                                            <p><?php echo esc_html($program->ProgramDescription); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="empty-state">
                                        <p>No recent programs to display.</p>
                                    </div>
                                <?php endif; ?>
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
                                <?php 
                                $users = get_users(array(
                                    'role__in' => array('administrator', 'editor', 'author', 'contributor'),
                                    'number' => 5 // Limit to 5 most recent members
                                ));
                                if (!empty($users)) : ?>
                                    <div class="members-list">
                                        <?php foreach ($users as $user) : ?>
                                            <div class="member-item">
                                                <div class="member-avatar">
                                                    <?php echo get_avatar($user->ID, 50); ?>
                                                </div>
                                                <div class="member-info">
                                                    <h4><?php echo esc_html($user->display_name); ?></h4>
                                                    <p class="member-role"><?php echo esc_html(implode(', ', $user->roles)); ?></p>
                                                    <p class="member-email"><?php echo esc_html($user->user_email); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <div class="empty-state">
                                        <p>No recent member activity to display.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programs Page -->
            <div id="programs-page" class="page-content">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Programs</h3>
                        <div class="section-actions">
                            <button class="add-button">Add Program</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <div class="empty-state">
                            <p>Programs management interface coming soon.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Page -->
            <div id="members-page" class="page-content">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Members</h3>
                        <div class="section-actions">
                            <button class="add-button">Add Member</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <?php 
                        $users = get_users(array('role__in' => array('administrator', 'editor', 'author', 'contributor')));
                        if (!empty($users)) : ?>
                            <div class="members-list">
                                <?php foreach ($users as $user) : ?>
                                    <div class="member-item">
                                        <div class="member-avatar">
                                            <?php echo get_avatar($user->ID, 50); ?>
                                        </div>
                                        <div class="member-info">
                                            <h4><?php echo esc_html($user->display_name); ?></h4>
                                            <p class="member-role"><?php echo esc_html(implode(', ', $user->roles)); ?></p>
                                            <p class="member-email"><?php echo esc_html($user->user_email); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="empty-state">
                                <p>No members found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>