<?php
// Member profile UI
?>
<div class="member-profile-container" style="margin-top:0;padding-top:0;">
  <div class="member-profile-layout" style="display:flex;max-width:900px;margin:0px auto 0 auto;gap:32px;align-items:flex-start;">
    <nav class="member-profile-menu" style="min-width:180px;max-width:220px;background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(34,113,177,0.09);padding:24px 0;display:flex;flex-direction:column;gap:8px;">
      <button class="member-profile-menu-item active" data-section="personal" style="background:none;border:none;text-align:left;padding:12px 28px;font-size:1.08em;color:#2271b1;font-weight:600;cursor:pointer;border-radius:0 20px 20px 0;">Personal Info</button>
      <button class="member-profile-menu-item" data-section="family" style="background:none;border:none;text-align:left;padding:12px 28px;font-size:1.08em;color:#2271b1;font-weight:600;cursor:pointer;border-radius:0 20px 20px 0;">Family & Relationships</button>
      <button class="member-profile-menu-item" data-section="involvement" style="background:none;border:none;text-align:left;padding:12px 28px;font-size:1.08em;color:#2271b1;font-weight:600;cursor:pointer;border-radius:0 20px 20px 0;">Involvement</button>
    </nav>
    <div class="member-profile-card grades-card-ui" style="flex:1;min-width:0;margin-top:0;padding:32px 36px 28px 36px;border-radius:14px;box-shadow:0 2px 12px rgba(34,113,177,0.09);background:#fff;">
      <h2 style="margin-bottom:18px;color:#2271b1;font-weight:600;text-align:center;">My Profile</h2>
      <div class="member-profile-section" id="member-personal-info-section">
        <div class="member-profile-section" id="member-personal-info"></div>
      </div>
      <div class="member-profile-section" id="member-family-info-section" style="display:none;">
        <h3>Family & Relationships</h3>
        <div id="member-family-info"></div>
      </div>
      <div class="member-profile-section" id="member-involvement-section" style="display:none;">
        <h3>Involvement</h3>
        <div id="member-roles-info"></div>
      </div>
    </div>
  </div>
</div> 