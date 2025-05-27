/**
 * Member Profile UI logic for the administration plugin
 * Loads personal info, family/relationships, and roles for the current user
 */
jQuery(function($) {
  function renderPersonalInfo(d) {
    var html = '<div class="member-avatar-row" style="display:flex;align-items:center;gap:24px;margin-bottom:18px;">';
    html += '<div class="member-avatar-container" style="position:relative;width:84px;height:84px;">';
    html += '<img src="' + (d.AvatarURL ? Dashboard.escapeHtml(d.AvatarURL) : '/wp-content/plugins/administration-plugin/assets/img/avatar-default.png') + '" class="member-avatar-img" style="width:84px;height:84px;border-radius:50%;object-fit:cover;border:2px solid #e3e7ee;">';
    html += '<input type="file" id="member-avatar-upload" style="display:none;" accept="image/*">';
    html += '<button class="button button-xs button-secondary member-avatar-upload-btn" style="position:absolute;bottom:0;right:0;">&#9998;</button>';
    html += '</div>';
    html += '<div style="flex:1;">';
    html += '<strong>Name:</strong> ' + Dashboard.escapeHtml(d.FirstName + ' ' + d.LastName) + '<br>';
    html += '<strong>Email:</strong> ' + Dashboard.escapeHtml(d.Email) + '<br>';
    if (d.Phone) html += '<strong>Phone:</strong> ' + Dashboard.escapeHtml(d.Phone) + '<br>';
    if (d.Birthday) html += '<strong>Birthday:</strong> ' + Dashboard.escapeHtml(d.Birthday) + '<br>';
    if (d.Gender) html += '<strong>Gender:</strong> ' + Dashboard.escapeHtml(d.Gender) + '<br>';
    if (d.AddressLine1) html += '<strong>Address:</strong> ' + Dashboard.escapeHtml(d.AddressLine1);
    if (d.AddressLine2) html += ', ' + Dashboard.escapeHtml(d.AddressLine2);
    if (d.City) html += ', ' + Dashboard.escapeHtml(d.City);
    if (d.State) html += ', ' + Dashboard.escapeHtml(d.State);
    if (d.Zip) html += ' ' + Dashboard.escapeHtml(d.Zip);
    html += '</div>';
    html += '<button class="button button-xs button-primary member-edit-info-btn" style="margin-left:18px;">Edit</button>';
    html += '</div>';
    $('#member-personal-info').html(html);
  }

  function loadPersonalInfo() {
    $('#member-personal-info').html('<div class="loading">Loading personal info...</div>');
    $.post(administration_plugin.ajax_url, {
      action: 'get_member_personal_info',
      nonce: administration_plugin.nonce
    }, function(resp) {
      if (resp.success && resp.data) {
        renderPersonalInfo(resp.data);
      } else {
        $('#member-personal-info').html('<div class="error">Could not load personal info.</div>');
      }
    });
  }

  function loadFamilyInfo() {
    $('#member-family-info').append('<div class="loading">Loading family/relationships...</div>');
    $.post(administration_plugin.ajax_url, {
      action: 'get_member_family_info',
      nonce: administration_plugin.nonce
    }, function(resp) {
      $('#member-family-info .loading').remove();
      if (resp.success && Array.isArray(resp.data) && resp.data.length) {
        var html = '<ul style="margin:0 0 0 12px;padding:0;">';
        resp.data.forEach(function(f) {
          html += '<li><strong>' + Dashboard.escapeHtml(f.Type) + ':</strong> ' + Dashboard.escapeHtml(f.Name) + '</li>';
        });
        html += '</ul>';
        $('#member-family-info').append(html);
      } else {
        $('#member-family-info').append('<div class="error">No family or relationships found.</div>');
      }
    });
  }

  function loadRolesInfo() {
    $('#member-roles-info').append('<div class="loading">Loading roles...</div>');
    $.post(administration_plugin.ajax_url, {
      action: 'get_member_roles_info',
      nonce: administration_plugin.nonce
    }, function(resp) {
      $('#member-roles-info .loading').remove();
      if (resp.success && Array.isArray(resp.data) && resp.data.length) {
        var html = '<ul style="margin:0 0 0 12px;padding:0;">';
        resp.data.forEach(function(r) {
          html += '<li><strong>' + Dashboard.escapeHtml(r.RoleTitle) + '</strong> in ' + Dashboard.escapeHtml(r.ProgramName) + '</li>';
        });
        html += '</ul>';
        $('#member-roles-info').append(html);
      } else {
        $('#member-roles-info').append('<div class="error">No staff roles found.</div>');
      }
    });
  }

  // Inline edit form rendering
  function renderPersonalInfoEditForm(d) {
    var html = '<form id="member-personal-info-edit-form" style="margin-bottom:0;">';
    html += '<div style="display:flex;gap:24px;align-items:center;margin-bottom:18px;">';
    html += '<div class="member-avatar-container" style="position:relative;width:84px;height:84px;">';
    html += '<img src="' + (d.AvatarURL ? Dashboard.escapeHtml(d.AvatarURL) : '/wp-content/plugins/administration-plugin/assets/img/avatar-default.png') + '" class="member-avatar-img" style="width:84px;height:84px;border-radius:50%;object-fit:cover;border:2px solid #e3e7ee;">';
    html += '</div>';
    html += '<div style="flex:1;">';
    html += '<div style="margin-bottom:8px;"><label>First Name</label><input type="text" name="FirstName" value="' + Dashboard.escapeHtml(d.FirstName) + '" class="input-xs" style="margin-left:8px;width:40%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Last Name</label><input type="text" name="LastName" value="' + Dashboard.escapeHtml(d.LastName) + '" class="input-xs" style="margin-left:8px;width:40%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Email</label><input type="email" name="Email" value="' + Dashboard.escapeHtml(d.Email) + '" class="input-xs" style="margin-left:8px;width:60%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Phone</label><input type="text" name="Phone" value="' + Dashboard.escapeHtml(d.Phone || '') + '" class="input-xs" style="margin-left:8px;width:50%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Birthday</label><input type="date" name="Birthday" value="' + Dashboard.escapeHtml(d.Birthday || '') + '" class="input-xs" style="margin-left:8px;width:40%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Gender</label><input type="text" name="Gender" value="' + Dashboard.escapeHtml(d.Gender || '') + '" class="input-xs" style="margin-left:8px;width:30%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Address</label><input type="text" name="AddressLine1" value="' + Dashboard.escapeHtml(d.AddressLine1 || '') + '" class="input-xs" style="margin-left:8px;width:60%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Address 2</label><input type="text" name="AddressLine2" value="' + Dashboard.escapeHtml(d.AddressLine2 || '') + '" class="input-xs" style="margin-left:8px;width:60%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>City</label><input type="text" name="City" value="' + Dashboard.escapeHtml(d.City || '') + '" class="input-xs" style="margin-left:8px;width:40%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>State</label><input type="text" name="State" value="' + Dashboard.escapeHtml(d.State || '') + '" class="input-xs" style="margin-left:8px;width:30%;"></div>';
    html += '<div style="margin-bottom:8px;"><label>Zip</label><input type="text" name="Zip" value="' + Dashboard.escapeHtml(d.Zip || '') + '" class="input-xs" style="margin-left:8px;width:30%;"></div>';
    html += '</div>';
    html += '</div>';
    html += '<div style="margin-top:10px;display:flex;gap:12px;">';
    html += '<button type="submit" class="button button-xs button-primary">Save</button>';
    html += '<button type="button" class="button button-xs button-secondary member-cancel-edit-info-btn">Cancel</button>';
    html += '</div>';
    html += '</form>';
    $('#member-personal-info').html(html);
  }

  // Edit button event
  $(document).on('click', '.member-edit-info-btn', function() {
    // Fetch current data again for safety
    $.post(administration_plugin.ajax_url, {
      action: 'get_member_personal_info',
      nonce: administration_plugin.nonce
    }, function(resp) {
      if (resp.success && resp.data) {
        renderPersonalInfoEditForm(resp.data);
      }
    });
  });

  // Cancel button event
  $(document).on('click', '.member-cancel-edit-info-btn', function() {
    loadPersonalInfo();
  });

  // Save event
  $(document).on('submit', '#member-personal-info-edit-form', function(e) {
    e.preventDefault();
    var $form = $(this);
    var data = $form.serializeArray();
    var postData = { action: 'update_member_personal_info', nonce: administration_plugin.nonce };
    data.forEach(function(f) { postData[f.name] = f.value; });
    $form.find('button[type=submit]').prop('disabled', true).text('Saving...');
    $.post(administration_plugin.ajax_url, postData, function(resp) {
      if (resp.success) {
        loadPersonalInfo();
      } else {
        alert(resp.data || 'Failed to update info.');
        $form.find('button[type=submit]').prop('disabled', false).text('Save');
      }
    });
  });

  // Event: Avatar upload button
  $(document).on('click', '.member-avatar-upload-btn', function() {
    $('#member-avatar-upload').click();
  });
  $(document).on('change', '#member-avatar-upload', function() {
    var file = this.files[0];
    if (!file) return;
    var formData = new FormData();
    formData.append('action', 'upload_member_avatar');
    formData.append('nonce', administration_plugin.nonce);
    formData.append('avatar', file);
    $.ajax({
      url: administration_plugin.ajax_url,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(resp) {
        if (resp.success && resp.data && resp.data.avatar_url) {
          $('.member-avatar-img').attr('src', resp.data.avatar_url);
        } else {
          alert('Failed to upload avatar.');
        }
      },
      error: function() { alert('Failed to upload avatar.'); }
    });
  });

  loadPersonalInfo();
  loadFamilyInfo();
  loadRolesInfo();
}); 