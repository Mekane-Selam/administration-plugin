<div class="course-detail-overview">
  <h2 class="course-detail-title">Course Overview</h2>
  <?php if (!empty($course)) : ?>
    <div class="course-detail-fields">
      <div class="course-detail-row"><span class="course-detail-label">Name:</span> <span class="course-detail-value"><?php echo esc_html($course->CourseName); ?></span></div>
      <?php if (!empty($course->Level)) : ?>
        <div class="course-detail-row"><span class="course-detail-label">Level:</span> <span class="course-detail-value"><?php echo esc_html($course->Level); ?></span></div>
      <?php endif; ?>
      <?php if (!empty($course->Description)) : ?>
        <div class="course-detail-row"><span class="course-detail-label">Description:</span> <span class="course-detail-value"><?php echo esc_html($course->Description); ?></span></div>
      <?php endif; ?>
      <?php if (!empty($course->StartDate)) : ?>
        <div class="course-detail-row"><span class="course-detail-label">Start Date:</span> <span class="course-detail-value"><?php echo esc_html($course->StartDate); ?></span></div>
      <?php endif; ?>
      <?php if (!empty($course->EndDate)) : ?>
        <div class="course-detail-row"><span class="course-detail-label">End Date:</span> <span class="course-detail-value"><?php echo esc_html($course->EndDate); ?></span></div>
      <?php endif; ?>
    </div>
  <?php else : ?>
    <div class="error-message">Course not found.</div>
  <?php endif; ?>
</div> 