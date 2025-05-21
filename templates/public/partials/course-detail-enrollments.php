<div class="course-detail-enrollments">
  <div class="course-detail-enrollments-toolbar">
    <button class="add-course-enrollment-btn" title="Add Enrollment"><span class="dashicons dashicons-plus"></span> Add Enrollment</button>
  </div>
  <?php if (!empty($enrollments)) : ?>
    <div class="course-detail-enrollments-list">
      <?php foreach ($enrollments as $enrollment) : ?>
        <div class="course-detail-enrollment-card" data-person-id="<?php echo esc_attr($enrollment->PersonID); ?>">
          <div class="course-detail-enrollment-icon"><span class="dashicons dashicons-id"></span></div>
          <div class="course-detail-enrollment-details">
            <div class="course-detail-enrollment-title"><?php echo esc_html(trim($enrollment->FirstName . ' ' . $enrollment->LastName)); ?></div>
            <div class="course-detail-enrollment-meta">Enrolled: <span><?php echo esc_html(date('M d, Y', strtotime($enrollment->EnrollmentDate))); ?></span></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <div class="program-enrollment-list-placeholder">No enrollments found for this course.</div>
  <?php endif; ?>
</div> 