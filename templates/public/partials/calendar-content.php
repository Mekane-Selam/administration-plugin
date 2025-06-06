<?php
// Calendar tab content partial
?>
<div class="calendar-list">
    <div class="calendar-embed-card" style="background:#fff;border-radius:14px;box-shadow:0 2px 12px rgba(34,113,177,0.09);padding:24px 18px 18px 18px;max-width:900px;margin:0 auto;">
        <div style="display:flex;align-items:center;gap:18px;margin-bottom:18px;">
            <label for="calendar-select" style="font-weight:500;color:#2271b1;">Select Calendar:</label>
            <select id="calendar-select" class="calendar-select" style="padding:6px 12px;border-radius:6px;border:1px solid #e3e7ee;min-width:220px;">
                <option value="c_ecf94bbee3e4fade6955dc0527c3c1858dcc1d5e7afdb1d6e517d7a5f5b010a5@group.calendar.google.com">Main Church Calendar</option>
            </select>
        </div>
        <div class="calendar-iframe-container" style="width:100%;min-height:600px;overflow:hidden;border-radius:12px;box-shadow:0 1px 6px rgba(34,113,177,0.07);background:#f7fafd;">
            <iframe id="google-calendar-iframe" src="https://calendar.google.com/calendar/embed?src=c_ecf94bbee3e4fade6955dc0527c3c1858dcc1d5e7afdb1d6e517d7a5f5b010a5@group.calendar.google.com&ctz=America/New_York" style="border:0;width:100%;height:600px;" frameborder="0" scrolling="no"></iframe>
        </div>
    </div>
</div> 