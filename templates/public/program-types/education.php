<div class="program-view-edu-layout">
  <div class="program-view-edu-top-row">
    <div class="program-view-edu-overview card">
      <button class="program-view-edu-edit-btn" title="Edit Overview"><span class="dashicons dashicons-edit"></span></button>
      <h3 class="program-view-edu-title">Overview</h3>
      <div class="program-view-edu-overview-content program-data">
        <div class="overview-display-mode">
          <div><strong>Name:</strong> <span class="overview-field" data-field="ProgramName"></span></div>
          <div><strong>Type:</strong> <span class="overview-field" data-field="ProgramType"></span></div>
          <div><strong>Description:</strong> <span class="overview-field" data-field="ProgramDescription"></span></div>
          <div><strong>Start Date:</strong> <span class="overview-field" data-field="StartDate"></span></div>
          <div><strong>End Date:</strong> <span class="overview-field" data-field="EndDate"></span></div>
          <div><strong>Status:</strong> <span class="overview-field" data-field="ActiveFlag"></span></div>
        </div>
        <form class="overview-edit-mode" style="display:none;">
          <div><strong>Name:</strong> <input type="text" name="ProgramName" class="overview-edit-input" /></div>
          <div><strong>Type:</strong> <input type="text" name="ProgramType" class="overview-edit-input" /></div>
          <div><strong>Description:</strong> <textarea name="ProgramDescription" class="overview-edit-input"></textarea></div>
          <div><strong>Start Date:</strong> <input type="date" name="StartDate" class="overview-edit-input" /></div>
          <div><strong>End Date:</strong> <input type="date" name="EndDate" class="overview-edit-input" /></div>
          <div><strong>Status:</strong> <select name="ActiveFlag" class="overview-edit-input"><option value="1">Active</option><option value="0">Inactive</option></select></div>
          <div class="overview-edit-actions">
            <button type="submit" class="button button-primary overview-save-btn">Save</button>
            <button type="button" class="button overview-cancel-btn">Cancel</button>
          </div>
        </form>
      </div>
    </div>
    <div class="program-view-edu-courses card">
      <button class="program-view-edu-add-course-btn" title="Add Course"><span class="dashicons dashicons-plus"></span></button>
      <h3 class="program-view-edu-title">Courses</h3>
      <div class="program-view-edu-courses-list">
        <form class="add-course-form" style="display:none;">
          <input type="text" name="CourseName" placeholder="Course Name" required />
          <input type="text" name="Level" placeholder="Level" />
          <button type="submit" class="button button-primary">Add</button>
          <button type="button" class="button add-course-cancel-btn">Cancel</button>
        </form>
        <?php
        // Fetch courses for this program
        if (!function_exists('administration_plugin_get_courses_for_program')) {
            function administration_plugin_get_courses_for_program($program_id) {
                global $wpdb;
                $table = $wpdb->prefix . 'progtype_edu_courses';
                return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE ProgramID = %s ORDER BY CourseName", $program_id));
            }
        }
        $courses = array();
        if (isset($program) && $program && isset($program->ProgramID)) {
            $courses = administration_plugin_get_courses_for_program($program->ProgramID);
        }
        ?>
        <?php if (!empty($courses)) : ?>
          <div class="courses-list-enhanced">
            <?php foreach ($courses as $course) : ?>
              <div class="course-card" data-course-id="<?php echo esc_attr($course->CourseID); ?>">
                <div class="course-card-icon"><span class="dashicons dashicons-welcome-learn-more"></span></div>
                <div class="course-card-details">
                  <div class="course-card-title"><?php echo esc_html($course->CourseName); ?></div>
                  <?php if (!empty($course->Level)) : ?>
                    <div class="course-card-meta">Level: <span><?php echo esc_html($course->Level); ?></span></div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else : ?>
          <div class="program-courses-list-placeholder">No courses found for this program.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="program-view-edu-enrollment card">
    <button class="program-view-edu-add-enrollment-btn" title="Add Enrollment"><span class="dashicons dashicons-plus"></span></button>
    <h3 class="program-view-edu-title">Enrollment</h3>
    <div class="program-view-edu-enrollment-content">
      <div class="enrollment-toolbar">
        <div class="enrollment-search-bar">
          <input type="text" class="enrollment-search-input" placeholder="Search enrollments by name..." autocomplete="off" />
        </div>
        <button class="program-view-edu-add-enrollment-btn" title="Add Enrollment">
          <span class="dashicons dashicons-plus"></span>
          Add Enrollment
        </button>
      </div>
      <form class="add-enrollment-form" style="display:none;">
        <input type="text" name="PersonID" placeholder="Person ID" required />
        <input type="text" name="CourseID" placeholder="Course ID" required />
        <button type="submit" class="button button-primary">Add</button>
        <button type="button" class="button add-enrollment-cancel-btn">Cancel</button>
      </form>
      <?php
      // Fetch enrollments for this program and join with people
      if (!function_exists('administration_plugin_get_enrollments_for_program')) {
          function administration_plugin_get_enrollments_for_program($program_id) {
              global $wpdb;
              $enroll_table = $wpdb->prefix . 'progtype_edu_enrollment';
              $person_table = $wpdb->prefix . 'core_person';
              return $wpdb->get_results($wpdb->prepare(
                  "SELECT e.*, p.FirstName, p.LastName FROM $enroll_table e LEFT JOIN $person_table p ON e.PersonID = p.PersonID WHERE e.ProgramID = %s ORDER BY e.EnrollmentDate DESC",
                  $program_id
              ));
          }
      }
      $enrollments = array();
      if (isset($program) && $program && isset($program->ProgramID)) {
          $enrollments = administration_plugin_get_enrollments_for_program($program->ProgramID);
      }
      ?>
      <?php if (!empty($enrollments)) : ?>
        <div class="enrollment-list-enhanced">
          <?php foreach ($enrollments as $enrollment) : ?>
            <div class="enrollment-card">
              <div class="enrollment-card-icon"><span class="dashicons dashicons-id"></span></div>
              <div class="enrollment-card-details">
                <div class="enrollment-card-title"><?php echo esc_html(trim($enrollment->FirstName . ' ' . $enrollment->LastName)); ?></div>
                <div class="enrollment-card-meta">Enrolled: <span><?php echo esc_html(date('M d, Y', strtotime($enrollment->EnrollmentDate))); ?></span></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="program-enrollment-list-placeholder">No enrollments found for this program.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Add Staff Container -->
<div class="program-view-edu-staff card">
    <h3 class="program-view-edu-title">Program Staff</h3>
    <div class="program-view-edu-staff-content">
        <?php
        // Get program staff members with correct table joins
        global $wpdb;
        $staff_members = $wpdb->get_results($wpdb->prepare(
            "SELECT p.FirstName, p.LastName, sr.RoleTitle, sr.RoleDescription
            FROM {$wpdb->prefix}progtype_edu_staff s
            JOIN {$wpdb->prefix}progtype_edu_staffroles sr ON s.RoleID = sr.RoleID
            JOIN {$wpdb->prefix}core_person p ON s.PersonID = p.PersonID
            WHERE s.ProgramID = %s
            ORDER BY sr.RoleTitle, p.LastName, p.FirstName",
            $program->ProgramID
        ));
        
        if ($staff_members) {
            foreach ($staff_members as $staff) {
                ?>
                <div class="staff-member-card">
                    <div class="staff-member-icon">
                        <span class="dashicons dashicons-businessperson"></span>
                    </div>
                    <div class="staff-member-details">
                        <div class="staff-member-name"><?php echo esc_html($staff->FirstName . ' ' . $staff->LastName); ?></div>
                        <div class="staff-member-role">
                            <span class="role-title"><?php echo esc_html($staff->RoleTitle); ?></span>
                            <?php if (!empty($staff->RoleDescription)) : ?>
                                <span class="role-description"><?php echo esc_html($staff->RoleDescription); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="program-enrollment-list-placeholder">No staff members assigned.</div>';
        }
        ?>
    </div>
</div>

<style>
.program-view-edu-enrollment-content {
    width: 100%;
}

.enrollment-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.enrollment-search-bar {
    flex: 0 1 300px;
}

.enrollment-search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e3e7ee;
    border-radius: 8px;
    font-size: 0.95rem;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.enrollment-search-input:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 2px rgba(34,113,177,0.10);
    outline: none;
}

.program-view-edu-staff {
    margin-top: 24px;
    background: linear-gradient(135deg, #f8fafc 0%, #e3e7ee 100%);
    border: 1px solid #e3e7ee;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(34,113,177,0.06);
    padding: 22px 20px 18px 20px;
    position: relative;
}

.program-view-edu-staff-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.staff-member-card {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e3e7ee;
    border-radius: 10px;
    padding: 16px 20px;
    transition: box-shadow 0.2s, border-color 0.2s, transform 0.2s;
}

.staff-member-card:hover {
    box-shadow: 0 4px 16px rgba(34,113,177,0.13);
    border-color: #b6d4ef;
    transform: translateY(-1px);
}

.staff-member-icon {
    font-size: 1.7rem;
    color: #2271b1;
    margin-right: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: #eaf4fb;
    border-radius: 50%;
}

.staff-member-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.staff-member-name {
    font-size: 1.08rem;
    font-weight: 600;
    color: #1d2327;
}

.staff-member-role {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.role-title {
    font-size: 0.97rem;
    color: #2271b1;
    font-weight: 500;
}

.role-description {
    font-size: 0.9rem;
    color: #666;
    font-style: italic;
}
</style> 