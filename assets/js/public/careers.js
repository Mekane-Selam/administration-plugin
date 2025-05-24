jQuery(function($) {
  function renderJobCards(jobs) {
    var $list = $('.careers-job-list');
    $list.empty();
    if (!jobs.length) {
      $list.html('<div class="no-data">No job openings at this time.</div>');
      return;
    }
    jobs.forEach(function(job) {
      var card = `<div class="careers-job-card" data-job-id="${job.JobPostingID}">
        <div class="careers-job-title">${job.Title ? $('<div>').text(job.Title).html() : ''}</div>
        <div class="careers-job-meta">
          <span><strong>Department:</strong> ${job.DepartmentName ? $('<div>').text(job.DepartmentName).html() : '—'}</span>
          <span><strong>Type:</strong> ${job.JobType ? $('<div>').text(job.JobType).html() : '—'}</span>
          <span><strong>Location:</strong> ${job.Location ? $('<div>').text(job.Location).html() : '—'}</span>
          <span><strong>Closing:</strong> ${job.ClosingDate ? $('<div>').text(job.ClosingDate).html() : '—'}</span>
        </div>
        <div class="careers-job-desc">${job.Description ? $('<div>').text(job.Description).html() : ''}</div>
        <button class="careers-job-apply-btn">Apply</button>
      </div>`;
      $list.append(card);
    });
  }

  // Fetch jobs on load
  $.ajax({
    url: window.careers_plugin_ajax_url,
    type: 'POST',
    data: { action: 'get_careers_job_postings' },
    success: function(response) {
      if (response.success && Array.isArray(response.data)) {
        renderJobCards(response.data);
      } else {
        $('.careers-job-list').html('<div class="no-data">Failed to load job openings.</div>');
      }
    },
    error: function() {
      $('.careers-job-list').html('<div class="no-data">Failed to load job openings.</div>');
    }
  });

  // Modal HTML
  function getModalHtml(jobId, jobTitle) {
    return `
      <div class="careers-modal-overlay">
        <div class="careers-modal">
          <button class="careers-modal-close" title="Close">&times;</button>
          <div class="careers-modal-title">Apply for: <span>${jobTitle}</span></div>
          <form class="careers-apply-form" autocomplete="off">
            <input type="hidden" name="job_posting_id" value="${jobId}">
            <div class="careers-form-row">
              <label>First Name <span class="required">*</span></label>
              <input type="text" name="first_name" required maxlength="64">
            </div>
            <div class="careers-form-row">
              <label>Last Name <span class="required">*</span></label>
              <input type="text" name="last_name" required maxlength="64">
            </div>
            <div class="careers-form-row">
              <label>Email <span class="required">*</span></label>
              <input type="email" name="email" required maxlength="128">
            </div>
            <div class="careers-form-row">
              <label>Phone</label>
              <input type="text" name="phone" maxlength="32">
            </div>
            <div class="careers-form-row">
              <label>Cover Letter</label>
              <textarea name="cover_letter" rows="3" maxlength="2000"></textarea>
            </div>
            <div class="careers-form-row">
              <label>Additional Notes</label>
              <textarea name="notes" rows="2" maxlength="1000"></textarea>
            </div>
            <div class="careers-form-actions">
              <button type="submit" class="careers-modal-submit">Submit Application</button>
            </div>
            <div class="careers-form-message" style="display:none;"></div>
          </form>
        </div>
      </div>
    `;
  }

  // Show modal on Apply click
  $(document).on('click', '.careers-job-apply-btn', function(e) {
    e.preventDefault();
    var $card = $(this).closest('.careers-job-card');
    var jobId = $card.data('job-id');
    var jobTitle = $card.find('.careers-job-title').text();
    // Remove any existing modal
    $('.careers-modal-overlay').remove();
    // Append modal
    $('body').append(getModalHtml(jobId, jobTitle));
    setTimeout(function() {
      $('.careers-modal-overlay').addClass('open');
    }, 10);
  });

  // Close modal
  $(document).on('click', '.careers-modal-close, .careers-modal-overlay', function(e) {
    if ($(e.target).is('.careers-modal-close') || $(e.target).is('.careers-modal-overlay')) {
      $('.careers-modal-overlay').removeClass('open');
      setTimeout(function() { $('.careers-modal-overlay').remove(); }, 200);
    }
  });
  // Prevent modal click from closing
  $(document).on('click', '.careers-modal', function(e) { e.stopPropagation(); });

  // Handle form submit
  $(document).on('submit', '.careers-apply-form', function(e) {
    e.preventDefault();
    var $form = $(this);
    var $msg = $form.find('.careers-form-message');
    $msg.hide().removeClass('error success');
    // Validate required fields
    var valid = true;
    $form.find('[required]').each(function() {
      if (!$(this).val().trim()) {
        valid = false;
        $(this).addClass('input-error');
      } else {
        $(this).removeClass('input-error');
      }
    });
    if (!valid) {
      $msg.text('Please fill in all required fields.').addClass('error').show();
      return;
    }
    // Submit via AJAX
    var data = $form.serializeArray();
    data.push({ name: 'action', value: 'apply_for_job_posting' });
    $.ajax({
      url: window.careers_plugin_ajax_url,
      type: 'POST',
      data: data,
      success: function(response) {
        if (response.success) {
          $msg.text('Application submitted successfully!').addClass('success').show();
          $form[0].reset();
          setTimeout(function() { $('.careers-modal-overlay').removeClass('open'); setTimeout(function() { $('.careers-modal-overlay').remove(); }, 200); }, 1800);
        } else {
          $msg.text(response.data || 'Failed to submit application.').addClass('error').show();
        }
      },
      error: function() {
        $msg.text('Failed to submit application.').addClass('error').show();
      }
    });
  });
}); 