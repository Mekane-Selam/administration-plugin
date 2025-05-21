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
    <p>General course information will go here.</p>
  </div>
  <div class="tab-pane" id="enrollments">
    <div class="course-detail-enrollments-toolbar">
        <input type="text" class="course-detail-enrollments-search" placeholder="Search enrollments...">
        <button class="add-course-enrollment-btn">
            <span class="dashicons dashicons-plus"></span>
            Add Enrollment
        </button>
    </div>
    <?php include plugin_dir_path(__FILE__) . 'course-detail-enrollments.php'; ?>
  </div>
  <div class="tab-pane" id="assignments">
    <p>Assignments content will go here.</p>
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

.course-detail-enrollments-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 10px 0;
    border-bottom: 1px solid #e3e7ee;
    margin-bottom: 16px;
    border-radius: 8px 8px 0 0;
}

.course-detail-enrollments-search {
    flex: 0 1 300px;
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