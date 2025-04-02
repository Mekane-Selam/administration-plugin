<div class="administration-container">
    <div class="administration-header">
        <h2>Administration - Prod</h2>
        <div class="administration-search">
            <input type="text" placeholder="Search Home">
        </div>
        <div class="administration-user">
            <span class="user-icon">J</span>
        </div>
    </div>
    
    <div class="administration-content">
        <div class="administration-sidebar">
            <ul>
                <li class="active"><a href="#"><i class="icon-home"></i> Home</a></li>
                <li><a href="#"><i class="icon-people"></i> People</a></li>
                <li><a href="#"><i class="icon-info"></i> Info</a></li>
                <li><a href="#"><i class="icon-apps"></i> Apps</a></li>
            </ul>
        </div>
        
        <div class="administration-main">
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
            
            <div class="administration-section">
                <div class="section-header">
                    <h3>Members</h3>
                    <div class="section-actions">
                        <button class="add-button">Add</button>
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
            
            <div class="administration-section">
                <div class="section-header">
                    <h3>Roles</h3>
                    <div class="section-actions">
                        <button class="add-button">Add</button>
                    </div>
                </div>
                <div class="section-content">
                    <div class="empty-state">
                        <p>No items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>