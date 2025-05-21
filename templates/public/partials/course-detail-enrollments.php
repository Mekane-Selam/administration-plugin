<div class="course-detail-enrollments-actions">
  <button class="add-course-enrollment-btn">
    <span class="dashicons dashicons-plus-alt"></span>
    Add Enrollment
  </button>
</div>
<div class="course-detail-enrollments">
  <div class="course-detail-enrollments-list">
    <?php if (!empty($enrollments)): ?>
      <?php foreach ($enrollments as $enrollment): ?>
        <div class="course-detail-enrollment-card" data-person-id="<?php echo esc_attr($enrollment->PersonID); ?>">
          <div class="course-detail-enrollment-icon">
            <span class="dashicons dashicons-groups"></span>
          </div>
          <div class="course-detail-enrollment-details">
            <div class="course-detail-enrollment-title"><?php echo esc_html($enrollment->FirstName . ' ' . $enrollment->LastName); ?></div>
            <div class="course-detail-enrollment-meta">Enrolled: <?php echo esc_html($enrollment->EnrollmentDate); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No enrollments found.</p>
    <?php endif; ?>
  </div>
</div> 