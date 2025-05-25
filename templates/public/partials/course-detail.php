<div class="course-detail-tabs">
  <button class="tab-button active" data-tab="general">General</button>
  <button class="tab-button" data-tab="enrollments">Enrollments</button>
  <button class="tab-button" data-tab="assignments">Assignments</button>
  <button class="tab-button" data-tab="curriculum">Curriculum</button>
  <button class="tab-button" data-tab="grades">Grades</button>
  <button class="tab-button" data-tab="attendance">Attendance</button>
</div>
<div class="course-detail-tab-content" data-course-id="<?php echo isset($course->CourseID) ? esc_attr($course->CourseID) : ''; ?>">
  <div class="tab-pane active" id="general">
    <div class="course-general-fields">
      <div class="course-general-row">
        <span class="course-general-label">Primary Instructor:</span>
        <span class="course-general-value" id="primary-instructor-display" data-person-id="<?php echo esc_attr($course->PrimaryInstructorID); ?>">
          <?php
          global $wpdb;
          $primary = '';
          if (!empty($course->PrimaryInstructorID)) {
            $person = $wpdb->get_row($wpdb->prepare("SELECT FirstName, LastName FROM {$wpdb->prefix}core_person WHERE PersonID = %s", $course->PrimaryInstructorID));
            if ($person) $primary = esc_html($person->FirstName . ' ' . $person->LastName);
          }
          echo $primary ? $primary : '<span class="course-general-empty">Not set</span>';
          ?>
        </span>
        <select class="course-general-edit" id="primary-instructor-select" style="display:none;"></select>
      </div>
      <div class="course-general-row">
        <span class="course-general-label">Back Up Teacher 1:</span>
        <span class="course-general-value" id="backup1-display" data-person-id="<?php echo esc_attr($course->BackUpTeacher1ID); ?>">
          <?php
          $backup1 = '';
          if (!empty($course->BackUpTeacher1ID)) {
            $person = $wpdb->get_row($wpdb->prepare("SELECT FirstName, LastName FROM {$wpdb->prefix}core_person WHERE PersonID = %s", $course->BackUpTeacher1ID));
            if ($person) $backup1 = esc_html($person->FirstName . ' ' . $person->LastName);
          }
          echo $backup1 ? $backup1 : '<span class="course-general-empty">Not set</span>';
          ?>
        </span>
        <select class="course-general-edit" id="backup1-select" style="display:none;"></select>
      </div>
      <div class="course-general-row">
        <span class="course-general-label">Back Up Teacher 2:</span>
        <span class="course-general-value" id="backup2-display" data-person-id="<?php echo esc_attr($course->BackUpTeacher2ID); ?>">
          <?php
          $backup2 = '';
          if (!empty($course->BackUpTeacher2ID)) {
            $person = $wpdb->get_row($wpdb->prepare("SELECT FirstName, LastName FROM {$wpdb->prefix}core_person WHERE PersonID = %s", $course->BackUpTeacher2ID));
            if ($person) $backup2 = esc_html($person->FirstName . ' ' . $person->LastName);
          }
          echo $backup2 ? $backup2 : '<span class="course-general-empty">Not set</span>';
          ?>
        </span>
        <select class="course-general-edit" id="backup2-select" style="display:none;"></select>
      </div>
      <div class="course-general-actions">
        <button type="button" class="button button-primary" id="edit-instructors-btn">Edit</button>
        <button type="button" class="button button-primary" id="save-instructors-btn" style="display:none;">Save</button>
        <button type="button" class="button" id="cancel-instructors-btn" style="display:none;">Cancel</button>
        <span id="instructors-message"></span>
      </div>
    </div>
    <style>
      .course-general-fields { max-width: 520px; margin: 0 auto 24px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(34,113,177,0.06); padding: 24px 28px 18px 28px; }
      .course-general-row { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
      .course-general-label { font-weight: 600; color: #2271b1; min-width: 160px; }
      .course-general-value { flex: 1; color: #1d2327; font-weight: 500; }
      .course-general-empty { color: #b6b6b6; font-style: italic; }
      .course-general-edit { flex: 1; min-width: 0; padding: 7px 12px; border: 1px solid #e3e7ee; border-radius: 8px; font-size: 1rem; background: #f8fafc; }
      .course-general-actions { display: flex; gap: 12px; align-items: center; margin-top: 10px; }
      #instructors-message { font-size: 0.98rem; font-weight: 500; margin-left: 10px; }
    </style>
  </div>
  <div class="tab-pane" id="enrollments">
    <div class="course-detail-enrollments-toolbar">
      <div class="course-detail-enrollments-search-container">
        <input type="text" class="course-detail-enrollments-search" placeholder="Search enrollments...">
      </div>
      <div class="course-detail-enrollments-actions">
        <button class="add-course-enrollment-btn">Add Enrollment</button>
      </div>
    </div>
    <?php include plugin_dir_path(__FILE__) . 'course-detail-enrollments.php'; ?>
  </div>
  <div class="tab-pane" id="assignments">
    <div class="course-detail-assignments-toolbar">
      <div class="course-detail-assignments-search-container">
        <input type="text" class="course-detail-assignments-search" placeholder="Search assignments...">
      </div>
      <div class="course-detail-assignments-actions">
        <button class="add-course-assignment-btn button button-primary">Add Assignment</button>
      </div>
    </div>
    <div style="height: 14px;"></div>
    <div class="course-assignments-content-split assignments-split-with-rule">
      <div class="course-assignments-list">
        <div class="course-assignments-list-grid">
          <div class="course-assignment-empty">No assignments yet.</div>
        </div>
      </div>
      <div class="course-assignment-details-panel"><div class="assignment-details-inner"></div></div>
    </div>
  </div>
  <div class="tab-pane" id="curriculum">
    <p>Curriculum content will go here.</p>
  </div>
  <div class="tab-pane" id="grades">
    <p>Grades content will go here.</p>
  </div>
  <div class="tab-pane" id="attendance">
    <p>Attendance content will go here.</p>
  </div>
</div>

<style>
.course-detail-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid #e3e7ee;
    margin-bottom: 20px;
    padding: 0 20px;
    margin: 0 10px 0 10px;
}

.tab-button {
    padding: 12px 20px;
    border: none;
    background: none;
    color: #646970;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    position: relative;
    transition: color 0.2s;
}

.tab-button:hover {
    color: #2271b1;
}

.tab-button.active {
    color: #2271b1;
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #2271b1;
}

.tab-pane {
    padding: 20px;
    display: none;
}

.tab-pane.active {
    display: block;
}

.course-detail-enrollments-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0 0 0;
    border-bottom: 1px solid #e3e7ee;
    margin-bottom: 20px;
}

.course-detail-enrollments-search-container {
    flex: 1;
    max-width: 300px;
    margin-right: 16px;
}

.course-detail-enrollments-search {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e3e7ee;
    border-radius: 8px;
    font-size: 0.95rem;
    background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.course-detail-enrollments-search:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 2px rgba(34,113,177,0.10);
    outline: none;
}

.course-detail-enrollments-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.add-course-enrollment-btn {
    background: linear-gradient(135deg, #2271b1 0%, #3498db 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 1rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(34,113,177,0.10);
    transition: background 0.2s, transform 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.add-course-enrollment-btn:hover {
    background: linear-gradient(135deg, #135e96 0%, #2271b1 100%);
    transform: translateY(-2px);
}

.add-course-enrollment-btn .dashicons {
    font-size: 1.2rem;
}

.course-detail-assignments-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 0 0 0;
  border-bottom: 1px solid #e3e7ee;
  margin-bottom: 20px;
}
.course-detail-assignments-search-container {
  flex: 1;
  max-width: 300px;
  margin-right: 16px;
}
.course-detail-assignments-search {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #e3e7ee;
  border-radius: 8px;
  font-size: 0.95rem;
  background: #f8fafc;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.course-detail-assignments-search:focus {
  border-color: #2271b1;
  box-shadow: 0 0 0 2px rgba(34,113,177,0.10);
  outline: none;
}
.course-detail-assignments-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}
.add-course-assignment-btn {
  background: linear-gradient(135deg, #2271b1 0%, #3498db 100%);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 1rem;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(34,113,177,0.10);
  transition: background 0.2s, transform 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.add-course-assignment-btn:hover {
  background: linear-gradient(135deg, #135e96 0%, #2271b1 100%);
  transform: translateY(-2px);
}
.course-assignments-content-split.assignments-split-with-rule {
  display: flex;
  gap: 0;
  min-height: 220px;
  position: relative;
}
.course-assignments-list {
  flex: 0 0 320px;
  max-width: 340px;
  min-width: 220px;
  border-right: 1.5px solid #e3e7ee;
  padding-right: 0;
  background: #fcfdff;
  z-index: 1;
}
.course-assignment-details-panel {
  flex: 1 1 0;
  min-width: 0;
  padding-left: 32px;
  background: #fff;
  z-index: 0;
  display: none;
}
.course-assignment-details-panel.active {
  display: block;
}
.course-assignments-list-grid {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.course-assignment-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 7px 14px 7px 18px;
  border-radius: 7px;
  font-size: 0.97rem;
  font-weight: 500;
  color: #1d2327;
  background: #f8fafc;
  cursor: pointer;
  transition: background 0.15s, box-shadow 0.15s;
  min-height: 36px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.course-assignment-row:hover, .course-assignment-row.selected {
  background: #eaf4fb;
  color: #2271b1;
}
.course-assignment-title {
  flex: 1 1 0;
  font-size: 0.98em;
  font-weight: 600;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.course-assignment-meta {
  display: flex;
  gap: 10px;
  font-size: 0.93em;
  color: #6a7a8c;
  min-width: 0;
}
.course-assignment-duedate, .course-assignment-maxscore {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.course-assignment-empty {
  color: #b6b6b6;
  font-style: italic;
  padding: 18px 0 0 18px;
}
.assignment-details-inner {
  max-width: 540px;
  margin: 0 auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(34,113,177,0.07);
  padding: 32px 36px 28px 36px;
  min-height: 120px;
}
</style> 