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
            <div id="home-page" class="page-content active">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Program Management</h3>
                        <div class="section-actions">
                            <button class="add-button">Add</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <?php if (!empty($programs)) : ?>
                            <?php foreach ($programs as $program) : ?>
                                <div class="program-card">
                                    <h4><?php echo esc_html($program->ProgramName); ?></h4>
                                    <p><?php echo esc_html($program->ProgramDescription); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="empty-state">
                                <p>No programs added yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
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
            
            <div id="members-page" class="page-content">
                <div class="administration-section">
                    <div class="section-header">
                        <h3>Members</h3>
                        <div class="section-actions">
                            <button class="add-button">Add Member</button>
                        </div>
                    </div>
                    <div class="section-content">
                        <?php if (!empty($persons)) : ?>
                            <div class="members-list">
                                <?php foreach ($persons as $person) : ?>
                                    <div class="member-item">
                                        <?php echo esc_html($person->FirstName . ' ' . $person->LastName); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="empty-state">
                                <p>No members added yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>