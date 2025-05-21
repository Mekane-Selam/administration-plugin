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
    <div class="program-view-edu-enrollment-header">
      <h3 class="program-view-edu-title">Enrollment</h3>
      <div class="program-view-edu-enrollment-toolbar">
        <div class="program-view-edu-enrollment-search-container">
          <input type="text" class="program-view-edu-enrollment-search" placeholder="Search enrollments by name..." autocomplete="off" />
        </div>
        <button type="button" class="program-view-edu-add-enrollment-btn" title="Add Enrollment">
          <span class="dashicons dashicons-plus"></span>
        </button>
      </div>
    </div>
    <div class="program-view-edu-enrollment-content">
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
<div class="program-view-edu-staff">
    <div class="program-view-edu-staff-header">
        <h3 class="program-view-edu-title">Staff</h3>
        <button type="button" class="program-view-edu-add-staff-btn">
            <span class="dashicons dashicons-plus-alt"></span>
            Add Staff
        </button>
    </div>
    <div class="program-view-edu-staff-content">
        <?php
        global $wpdb;
        
        // Join the staff, staff roles, and person tables
        $staff_query = $wpdb->prepare(
            "SELECT s.*, sr.RoleTitle, p.FirstName, p.LastName 
            FROM {$wpdb->prefix}progtype_edu_staff s
            LEFT JOIN {$wpdb->prefix}progtype_edu_staffroles sr ON s.StaffRoledID = sr.StaffRoleID
            LEFT JOIN {$wpdb->prefix}core_person p ON s.PersonID = p.PersonID
            WHERE s.ProgramID = %d
            ORDER BY sr.RoleTitle, p.LastName, p.FirstName",
            $program->ProgramID
        );
        
        $staff_members = $wpdb->get_results($staff_query);
        
        if ($staff_members) {
            echo '<div class="program-view-edu-staff-grid">';
            foreach ($staff_members as $staff) {
                ?>
                <div class="program-view-edu-staff-card">
                    <div class="program-view-edu-staff-info">
                        <h4 class="program-view-edu-staff-name">
                            <?php echo esc_html($staff->FirstName . ' ' . $staff->LastName); ?>
                        </h4>
                        <span class="program-view-edu-staff-role">
                            <?php echo esc_html($staff->RoleTitle); ?>
                        </span>
                    </div>
                    <div class="program-view-edu-staff-actions">
                        <button type="button" class="program-view-edu-staff-edit-btn" data-staff-id="<?php echo esc_attr($staff->StaffID); ?>">
                            <span class="dashicons dashicons-edit"></span>
                        </button>
                        <button type="button" class="program-view-edu-staff-remove-btn" data-staff-id="<?php echo esc_attr($staff->StaffID); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<div class="program-view-edu-staff-empty">';
            echo '<span class="dashicons dashicons-groups"></span>';
            echo '<p>No staff members assigned to this program yet.</p>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<style>
.program-view-edu-enrollment {
    margin-top: 0;
    margin-bottom: 0;
    max-width: 100%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    position: relative;
}

.program-view-edu-enrollment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e3e7ee;
}

.program-view-edu-enrollment-header .program-view-edu-title {
    margin: 0;
    font-size: 1.25rem;
    color: #1d2327;
}

.program-view-edu-enrollment-toolbar {
    display: flex;
    align-items: center;
    gap: 16px;
}

.program-view-edu-enrollment-search-container {
    width: 300px;
}

.program-view-edu-enrollment-search {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e3e7ee;
    border-radius: 8px;
    font-size: 0.95rem;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.program-view-edu-enrollment-search:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 2px rgba(34,113,177,0.10);
    outline: none;
}

.program-view-edu-enrollment-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.program-view-edu-add-enrollment-btn {
    background: linear-gradient(135deg, #2271b1 0%, #3498db 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(34,113,177,0.10);
    transition: background 0.2s, transform 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    width: 36px;
    height: 36px;
}

.program-view-edu-add-enrollment-btn:hover {
    background: linear-gradient(135deg, #135e96 0%, #2271b1 100%);
    transform: translateY(-2px);
}

.program-view-edu-add-enrollment-btn .dashicons {
    font-size: 1.2rem;
    width: 20px;
    height: 20px;
}

.program-view-edu-staff {
    background: #fff;
    border: 1px solid #e3e7ee;
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.program-view-edu-staff-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e3e7ee;
}

.program-view-edu-staff-header .program-view-edu-title {
    margin: 0;
    font-size: 1.25rem;
    color: #1d2327;
}

.program-view-edu-add-staff-btn {
    background: linear-gradient(135deg, #2271b1 0%, #3498db 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.95rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(34,113,177,0.10);
    transition: background 0.2s, transform 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
}

.program-view-edu-add-staff-btn:hover {
    background: linear-gradient(135deg, #135e96 0%, #2271b1 100%);
    transform: translateY(-2px);
}

.program-view-edu-staff-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.program-view-edu-staff-card {
    background: #f8fafc;
    border: 1px solid #e3e7ee;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: transform 0.2s, box-shadow 0.2s;
}

.program-view-edu-staff-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.program-view-edu-staff-info {
    flex: 1;
}

.program-view-edu-staff-name {
    margin: 0 0 4px 0;
    font-size: 1.1rem;
    color: #1d2327;
}

.program-view-edu-staff-role {
    display: block;
    font-size: 0.9rem;
    color: #646970;
}

.program-view-edu-staff-actions {
    display: flex;
    gap: 8px;
}

.program-view-edu-staff-edit-btn,
.program-view-edu-staff-remove-btn {
    background: none;
    border: none;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.program-view-edu-staff-edit-btn {
    color: #2271b1;
}

.program-view-edu-staff-remove-btn {
    color: #d63638;
}

.program-view-edu-staff-edit-btn:hover {
    background-color: rgba(34,113,177,0.1);
}

.program-view-edu-staff-remove-btn:hover {
    background-color: rgba(214,54,56,0.1);
}

.program-view-edu-staff-empty {
    text-align: center;
    padding: 40px 20px;
    background: #f8fafc;
    border-radius: 8px;
    color: #646970;
}

.program-view-edu-staff-empty .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    margin-bottom: 16px;
}

.program-view-edu-staff-empty p {
    margin: 0;
    font-size: 1.1rem;
}
</style> 