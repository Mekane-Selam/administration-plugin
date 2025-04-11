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
                <li data-page="hr">
                    <a href="#"><i class="dashicons dashicons-businessperson"></i> <span>HR</span></a>
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

            <!-- HR Page -->
            <div id="hr-page" class="page-content">
                <!-- HR Dashboard -->
                <div class="administration-section">
                    <div class="section-header">
                        <h3>HR Dashboard</h3>
                    </div>
                    <div class="hr-dashboard-grid">
                        <div class="hr-dashboard-item" data-section="jobs">
                            <div class="dashboard-icon">
                                <i class="dashicons dashicons-businessman"></i>
                            </div>
                            <h4>Jobs</h4>
                            <p>Manage job postings, applications, interviews, and offers</p>
                        </div>
                        <div class="hr-dashboard-item" data-section="employees">
                            <div class="dashboard-icon">
                                <i class="dashicons dashicons-groups"></i>
                            </div>
                            <h4>Employees</h4>
                            <p>Manage employee records and information</p>
                        </div>
                        <div class="hr-dashboard-item" data-section="timesheets">
                            <div class="dashboard-icon">
                                <i class="dashicons dashicons-calendar-alt"></i>
                            </div>
                            <h4>Timesheets</h4>
                            <p>Track employee work hours and attendance</p>
                        </div>
                    </div>
                </div>

                <!-- Jobs Section -->
                <div id="hr-jobs-section" class="hr-section" style="display: none;">
                    <!-- Job Postings -->
                    <div class="administration-section">
                        <div class="section-header">
                            <h3>Job Postings</h3>
                            <div class="section-actions">
                                <button class="add-button" id="add-job-posting">Add Job Posting</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="job-postings-list">
                                <!-- Job postings will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Applications Section -->
                    <div class="administration-section">
                        <div class="section-header">
                            <h3>Applications</h3>
                            <div class="section-actions">
                                <button class="filter-button" id="filter-applications">Filter</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="applications-list">
                                <!-- Applications will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Interviews Section -->
                    <div class="administration-section">
                        <div class="section-header">
                            <h3>Interviews</h3>
                            <div class="section-actions">
                                <button class="filter-button" id="filter-interviews">Filter</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="interviews-list">
                                <!-- Interviews will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Offers Section -->
                    <div class="administration-section">
                        <div class="section-header">
                            <h3>Offers</h3>
                            <div class="section-actions">
                                <button class="filter-button" id="filter-offers">Filter</button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="offers-list">
                                <!-- Offers will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Modals -->
                    <div id="job-posting-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Add Job Posting</h2>
                            <form id="job-posting-form">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" id="title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="requirements">Requirements</label>
                                    <textarea id="requirements" name="requirements" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="responsibilities">Responsibilities</label>
                                    <textarea id="responsibilities" name="responsibilities" required></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="job-type">Job Type</label>
                                        <select id="job-type" name="jobType" required>
                                            <option value="Full-time">Full-time</option>
                                            <option value="Part-time">Part-time</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Volunteer">Volunteer</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" id="location" name="location">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="salary-range">Salary Range</label>
                                        <input type="text" id="salary-range" name="salaryRange">
                                    </div>
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <input type="text" id="department" name="departmentName">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="posted-date">Posted Date</label>
                                        <input type="date" id="posted-date" name="postedDate">
                                    </div>
                                    <div class="form-group">
                                        <label for="closing-date">Closing Date</label>
                                        <input type="date" id="closing-date" name="closingDate">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="isInternal" value="1">
                                        Internal Posting Only
                                    </label>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">Save Job Posting</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="application-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>View Application</h2>
                            <div class="application-details">
                                <!-- Application details will be loaded here -->
                            </div>
                            <div class="application-actions">
                                <button class="button" id="schedule-interview">Schedule Interview</button>
                                <button class="button" id="reject-application">Reject</button>
                                <button class="button button-primary" id="make-offer">Make Offer</button>
                            </div>
                        </div>
                    </div>

                    <div id="interview-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Schedule Interview</h2>
                            <form id="interview-form">
                                <div class="form-group">
                                    <label for="interview-round">Interview Round</label>
                                    <select id="interview-round" name="interviewRound" required>
                                        <option value="1">First Round</option>
                                        <option value="2">Second Round</option>
                                        <option value="3">Final Round</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="interview-date">Date</label>
                                    <input type="date" id="interview-date" name="scheduledDate" required>
                                </div>
                                <div class="form-group">
                                    <label for="interview-time">Time</label>
                                    <input type="time" id="interview-time" name="scheduledTime" required>
                                </div>
                                <div class="form-group">
                                    <label for="interview-type">Type</label>
                                    <select id="interview-type" name="interviewType" required>
                                        <option value="In-person">In-person</option>
                                        <option value="Phone">Phone</option>
                                        <option value="Video">Video</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="interview-location">Location</label>
                                    <input type="text" id="interview-location" name="location">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">Schedule Interview</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="offer-modal" class="modal">
                        <div class="modal-content">
                            <span class="close">&times;</span>
                            <h2>Make Offer</h2>
                            <form id="offer-form">
                                <div class="form-group">
                                    <label for="offer-position">Position</label>
                                    <input type="text" id="offer-position" name="position" required>
                                </div>
                                <div class="form-group">
                                    <label for="offer-department">Department</label>
                                    <input type="text" id="offer-department" name="department" required>
                                </div>
                                <div class="form-group">
                                    <label for="offer-salary">Salary</label>
                                    <input type="number" id="offer-salary" name="salaryOffered" required>
                                </div>
                                <div class="form-group">
                                    <label for="offer-start-date">Start Date</label>
                                    <input type="date" id="offer-start-date" name="startDate" required>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="button button-primary">Make Offer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>