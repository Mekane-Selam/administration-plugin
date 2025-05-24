jQuery(function($) {
  function renderJobCards(jobs) {
    var $list = $('.careers-job-list');
    $list.empty();
    if (!jobs.length) {
      $list.html('<div class="no-data">No job openings at this time.</div>');
      return;
    }
    jobs.forEach(function(job) {
      var card = `<div class="careers-job-card">
        <div class="careers-job-title">${job.Title ? $('<div>').text(job.Title).html() : ''}</div>
        <div class="careers-job-meta">
          <span><strong>Department:</strong> ${job.DepartmentName ? $('<div>').text(job.DepartmentName).html() : '—'}</span>
          <span><strong>Type:</strong> ${job.JobType ? $('<div>').text(job.JobType).html() : '—'}</span>
          <span><strong>Location:</strong> ${job.Location ? $('<div>').text(job.Location).html() : '—'}</span>
          <span><strong>Closing:</strong> ${job.ClosingDate ? $('<div>').text(job.ClosingDate).html() : '—'}</span>
        </div>
        <div class="careers-job-desc">${job.Description ? $('<div>').text(job.Description).html() : ''}</div>
        <button class="careers-job-apply-btn" disabled>Apply</button>
      </div>`;
      $list.append(card);
    });
  }
  function loadJobs() {
    $('.careers-job-list').html('<div class="loading">Loading job openings...</div>');
    $.ajax({
      url: window.careers_plugin_ajax_url,
      type: 'POST',
      data: { action: 'get_careers_job_postings' },
      success: function(response) {
        if (response.success && Array.isArray(response.data)) {
          renderJobCards(response.data);
        } else {
          $('.careers-job-list').html('<div class="error-message">Failed to load job openings.</div>');
        }
      },
      error: function() {
        $('.careers-job-list').html('<div class="error-message">Failed to load job openings.</div>');
      }
    });
  }
  loadJobs();
}); 