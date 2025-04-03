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
                    <a href="#"><i class="icon-home"></i> <span>Home</span></a>
                </li>
                <li data-page="programs">
                    <a href="#"><i class="icon-programs"></i> <span>Programs</span></a>
                </li>
                <li data-page="members">
                    <a href="#"><i class="icon-members"></i> <span>Members</span></a>
                </li>
            </ul>
        </div>
        
        <div class="administration-main">
            <div class="page-content" id="home-page">
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
            
            <div class="page-content" id="programs-page" style="display: none;">
                <div class="administration-section">
                    <h3>Programs Page</h3>
                    <p>Programs content will go here</p>
                </div>
            </div>
            
            <div class="page-content" id="members-page" style="display: none;">
                <div class="administration-section">
                    <h3>Members Page</h3>
                    <p>Members content will go here</p>
                </div>
            </div>
        </div>
    </div>
</div>