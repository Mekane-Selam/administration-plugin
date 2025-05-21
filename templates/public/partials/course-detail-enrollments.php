<div class="course-detail-enrollments">
  <div class="course-detail-enrollments-list">
    <?php
    // Fetch enrollments for this course
    global $wpdb;
    $enrollments = $wpdb->get_results($wpdb->prepare(
        "SELECT ce.*, p.FirstName, p.LastName 
        FROM {$wpdb->prefix}progtype_edu_course_enrollment ce
        LEFT JOIN {$wpdb->prefix}core_person p ON ce.PersonID = p.PersonID
        WHERE ce.CourseID = %s
        ORDER BY ce.EnrollmentDate DESC",
        $course->CourseID
    ));

    if ($enrollments) {
        foreach ($enrollments as $enrollment) {
            ?>
            <div class="course-detail-enrollment-card" data-person-id="<?php echo esc_attr($enrollment->PersonID); ?>">
                <div class="course-detail-enrollment-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="course-detail-enrollment-details">
                    <div class="course-detail-enrollment-title">
                        <?php echo esc_html($enrollment->FirstName . ' ' . $enrollment->LastName); ?>
                    </div>
                    <div class="course-detail-enrollment-meta">
                        Enrolled: <?php echo esc_html(date('Y-m-d', strtotime($enrollment->EnrollmentDate))); ?>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="course-detail-enrollments-empty">No enrollments found for this course.</div>';
    }
    ?>
  </div>
</div> 