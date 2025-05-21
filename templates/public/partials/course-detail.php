<div class="course-detail-tabs">
  <button class="tab-button active" data-tab="enrollments">Enrollments</button>
  <button class="tab-button" data-tab="assignments">Assignments</button>
  <button class="tab-button" data-tab="curriculum">Curriculum</button>
  <button class="tab-button" data-tab="grades">Grades</button>
  <button class="tab-button" data-tab="attendance">Attendance</button>
</div>
<div class="course-detail-tab-content" data-course-id="<?php echo isset($course->CourseID) ? esc_attr($course->CourseID) : ''; ?>">
  <div class="tab-pane active" id="enrollments">
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