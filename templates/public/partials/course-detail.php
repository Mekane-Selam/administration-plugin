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
</style> 