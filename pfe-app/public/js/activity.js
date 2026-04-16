// ============ Schedule rows ============
const DAYS = [
    "monday",
    "tuesday",
    "wednesday",
    "thursday",
    "friday",
    "saturday",
    "sunday",
];

function addScheduleRow(containerId, data = {}) {
    const container = document.getElementById(containerId);
    const index = container.children.length;
    const row = document.createElement("div");
    row.className = "schedule-row";

    const daysOptions = DAYS.map(
        (d) =>
            `<option value="${d}" ${data.day_of_week === d ? "selected" : ""}>${d.charAt(0).toUpperCase() + d.slice(1)}</option>`,
    ).join("");

    row.innerHTML = `
        <select name="schedules[${index}][day_of_week]">
            <option value="">Day</option>
            ${daysOptions}
        </select>
        <input type="time" name="schedules[${index}][start_time]" value="${data.start_time ?? ""}"
            onchange="validateScheduleTime(this)">
        <input type="time" name="schedules[${index}][end_time]" value="${data.end_time ?? ""}"
            onchange="validateScheduleTime(this)">
        <button type="button" class="remove-schedule-btn" onclick="removeScheduleRow(this)">✕</button>
    `;

    container.appendChild(row);
}

function validateScheduleTime(input) {
    const row = input.closest(".schedule-row");
    const start = row.querySelector('[name*="start_time"]').value;
    const end = row.querySelector('[name*="end_time"]').value;
    const endInput = row.querySelector('[name*="end_time"]');

    if (start && end && end <= start) {
        endInput.classList.add("input-error");
        endInput.title = "End time must be after start time";
    } else {
        endInput.classList.remove("input-error");
        endInput.title = "";
    }
}

function validateAllSchedules(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    Array.from(container.children).forEach((row) => {
        const endInput = row.querySelector('[name*="end_time"]');
        if (endInput) validateScheduleTime(endInput);
    });
}

function removeScheduleRow(btn) {
    const container = btn.closest(".schedule-row").parentElement;
    btn.closest(".schedule-row").remove();
    Array.from(container.children).forEach((row, i) => {
        row.querySelector('[name*="day_of_week"]').name = `schedules[${i}][day_of_week]`;
        row.querySelector('[name*="start_time"]').name  = `schedules[${i}][start_time]`;
        row.querySelector('[name*="end_time"]').name    = `schedules[${i}][end_time]`;
    });
}

// ============ Show All Schedules Modal ============
function showAllSchedules(activityId, title) {
    const dataEl = document.getElementById(`schedules-data-${activityId}`);
    if (!dataEl) return;

    const schedules = JSON.parse(dataEl.textContent);

    const schedulesHtml = schedules.map(s => `
        <div class="schedule-item" style="padding:10px 0;border-bottom:1px solid #F0EDE6">
            <span class="schedule-day" style="font-weight:600;font-size:13px">${s.day_of_week.charAt(0).toUpperCase() + s.day_of_week.slice(1)}</span>
            <span class="schedule-time" style="font-size:13px;color:#8a7a6a">${s.start_time.substring(0,5)} – ${s.end_time.substring(0,5)}</span>
        </div>
    `).join('');

    const modal = document.createElement('div');
    modal.id = 'schedules-modal';
    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px';
    modal.innerHTML = `
        <div style="background:#fff;border-radius:20px;padding:32px;max-width:400px;width:100%">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                <h3 style="font-family:var(--font-display);font-size:18px;font-weight:700;color:#1a1a18">${title}</h3>
                <button onclick="document.getElementById('schedules-modal').remove()"
                    style="background:none;border:none;cursor:pointer;color:#a09890;font-size:20px;line-height:1">✕</button>
            </div>
            <p style="font-size:11px;text-transform:uppercase;letter-spacing:0.07em;color:#a09890;margin-bottom:8px">All Schedules</p>
            <div>${schedulesHtml}</div>
        </div>
    `;

    document.body.appendChild(modal);
    modal.addEventListener('click', function(e) {
        if (e.target === this) this.remove();
    });
}

// ============ Add Activity ============
document
    .getElementById("add-activity-form")
    ?.addEventListener("submit", async function (e) {
        e.preventDefault();

        const invalidSchedules = document.querySelectorAll("#add-activity-form .input-error");
        if (invalidSchedules.length > 0) {
            invalidSchedules[0].scrollIntoView({ behavior: "smooth", block: "center" });
            return;
        }

        clearErrors("add-activity-form");

        const res = await ajaxPost(STORE_URL, new FormData(this));

        if (res.ok) {
            const data = await res.json();
            if (data.success) {
                this.reset();
                document.getElementById("add-activity-form-schedules").innerHTML = "";
                closeModal("add-activity-modal");
                addActivityToGrid(data.activity);
            }
        } else {
            const data = await res.json();
            if (data.errors) showErrors("add-activity-form", data.errors);
        }
    });

// ============ Edit Activity ============
function openEditActivityModal(id, activity) {
    const container = document.getElementById("edit-activity-form-container");

    const cats = CATEGORIES.map(
        (c) =>
            `<option value="${c.id}" ${activity.category_id == c.id ? "selected" : ""}>${c.icon} ${c.name}</option>`,
    ).join("");

    const levels = ["beginner", "intermediate", "advanced"];
    const levelOptions = levels
        .map(
            (l) =>
                `<option value="${l}" ${activity.level === l ? "selected" : ""}>${l.charAt(0).toUpperCase() + l.slice(1)}</option>`,
        )
        .join("");

    container.innerHTML = `
        <form id="edit-activity-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="${document.querySelector("[name=_token]").value}">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-input" value="${activity.title ?? ""}">
                    <p class="error-msg" id="edit-activity-form-err-title"></p>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-input">${activity.description ?? ""}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-input">
                        <option value="">Select category</option>
                        ${cats}
                    </select>
                    <p class="error-msg" id="edit-activity-form-err-category_id"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <select name="level" class="form-input">
                        <option value="">Any level</option>
                        ${levelOptions}
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price ($/session)</label>
                    <input type="number" name="price" class="form-input" value="${activity.price ?? ""}" min="0" step="0.01">
                    <p class="error-msg" id="edit-activity-form-err-price"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-input" value="${activity.capacity ?? ""}" min="1">
                    <p class="error-msg" id="edit-activity-form-err-capacity"></p>
                </div>
                <div class="form-group">
                    <label class="form-label">Min Age</label>
                    <input type="number" name="min_age" class="form-input" value="${activity.min_age ?? ""}" placeholder="optional" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Age</label>
                    <input type="number" name="max_age" class="form-input" value="${activity.max_age ?? ""}" placeholder="optional" min="0">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <div style="display:flex;align-items:center;gap:12px">
                        <input type="checkbox" name="is_private" id="edit-activity-is_private" value="1"
                            ${activity.is_private ? "checked" : ""} style="width:16px;height:16px;accent-color:#D4A350">
                        <label for="edit-activity-is_private" style="font-size:13px;color:#5a5751">Private session (1-on-1)</label>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top:8px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <label class="form-label" style="margin-bottom:0">Schedules</label>
                    <button type="button" onclick="addScheduleRow('edit-activity-form-schedules')"
                        style="font-size:12px;color:#D4A350;background:none;border:none;cursor:pointer">+ Add slot</button>
                </div>
                <div id="edit-activity-form-schedules" style="display:flex;flex-direction:column;gap:8px"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Image (leave empty to keep current)</label>
                <input type="file" name="image" accept="image/*" class="form-input" style="padding:8px">
                <p class="error-msg" id="edit-activity-form-err-image"></p>
            </div>

            <button type="submit" class="search-btn w-full py-3 text-sm" style="margin-top:8px;width:100%">Update Activity</button>
        </form>
    `;

    if (activity.schedules && activity.schedules.length > 0) {
        activity.schedules.forEach((s) =>
            addScheduleRow("edit-activity-form-schedules", s),
        );
        validateAllSchedules("edit-activity-form-schedules");
    }

    openModal("edit-activity-modal");

    document
        .getElementById("edit-activity-form")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const invalidSchedules = document.querySelectorAll("#edit-activity-form .input-error");
            if (invalidSchedules.length > 0) {
                invalidSchedules[0].scrollIntoView({ behavior: "smooth", block: "center" });
                return;
            }

            clearErrors("edit-activity-form");

            const res = await ajaxPost(`/activity/${id}/update`, new FormData(this));

            if (res.ok) {
                const data = await res.json();
                closeModal("edit-activity-modal");
                updateActivityInGrid(id, data.activity);
            } else {
                const data = await res.json();
                if (data.errors) showErrors("edit-activity-form", data.errors);
            }
        });
}

// ============ Update activity in grid ============
function updateActivityInGrid(id, act) {
    const card = document.getElementById(`activity-card-${id}`);
    if (!card) {
        window.location.reload();
        return;
    }

    const imgWrapper = card.querySelector(".activity-img-wrapper");
    if (imgWrapper) {
        const overlay = imgWrapper.querySelector(".activity-actions-overlay").outerHTML;
        if (act.images && act.images.length > 0) {
            const imgUrl = "/" + act.images[0].image_path + "?t=" + Date.now();
            imgWrapper.innerHTML = `<img src="${imgUrl}" alt="${act.title}" class="activity-img">${overlay}`;
        } else {
            imgWrapper.innerHTML = `<div class="activity-img-placeholder" style="background:#2A2520;display:flex;align-items:center;justify-content:center;font-size:48px;height:100%">${act.category?.icon ?? "🏃"}</div>${overlay}`;
        }
    }

    const body = card.querySelector(".activity-mgmt-body");
    if (body) {
        const isActive = card.querySelector(".status-badge")?.classList.contains("active");

        const firstSchedule = act.schedules && act.schedules.length > 0 ? act.schedules[0] : null;
        const remainingCount = act.schedules ? act.schedules.length - 1 : 0;

        const schedulesHtml = firstSchedule
            ? `<div class="schedule-item">
                    <span class="schedule-day">${firstSchedule.day_of_week.charAt(0).toUpperCase() + firstSchedule.day_of_week.slice(1)}</span>
                    <span class="schedule-time">${firstSchedule.start_time.substring(0,5)} – ${firstSchedule.end_time.substring(0,5)}</span>
               </div>
               ${remainingCount > 0 ? `<button onclick="showAllSchedules(${id}, '${act.title.replace(/'/g, "\\'")}')" class="show-more-btn">+${remainingCount} more ${remainingCount === 1 ? 'slot' : 'slots'}</button>` : ''}`
            : '<p style="font-size:11px;color:#b0a898">No schedules added</p>';

        body.innerHTML = `
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                <h3 class="font-display" style="font-size:15px;font-weight:700;line-height:1.3">${act.title}</h3>
                <button class="status-badge ${isActive ? "active" : "inactive"} flex-shrink-0"
                    onclick="toggleActivityActive(${id}, this)">
                    ${isActive ? "Active" : "Inactive"}
                </button>
            </div>
            <p style="font-size:11px;color:#8a7a6a;margin-bottom:10px">
                ${act.category?.name ?? ""}${act.level ? " · " + act.level.charAt(0).toUpperCase() + act.level.slice(1) : ""}
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
                <span class="pill">$${Math.round(act.price)}/session</span>
                <span class="pill">${act.capacity} spots</span>
                ${act.min_age || act.max_age
                    ? `<span class="pill">Ages ${act.min_age ?? "0"}${act.max_age ? "–" + act.max_age : "+"}</span>`
                    : '<span class="pill">All ages</span>'}
                ${act.is_private ? '<span class="pill">Private</span>' : ""}
            </div>
            <div class="schedules-list">${schedulesHtml}</div>
            <script type="application/json" id="schedules-data-${id}">${JSON.stringify(act.schedules || [])}<\/script>
        `;
    }

    const editBtn = card.querySelector(".edit-btn");
    if (editBtn) editBtn.onclick = () => openEditActivityModal(id, act);

    const grid = document.getElementById("activities-grid");
    if (grid) {
        grid.insertBefore(card, grid.firstChild);
        setTimeout(() => card.scrollIntoView({ behavior: "smooth", block: "center" }), 100);
    }
}

// ============ Toggle Activity Active ============
async function toggleActivityActive(id, btn) {
    const res = await fetch(`/activity/${id}/toggle-active`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("[name=_token]").value,
            Accept: "application/json",
        },
    });

    if (res.ok) {
        const data = await res.json();
        btn.className = "status-badge flex-shrink-0 " + (data.is_active ? "active" : "inactive");
        btn.textContent = data.is_active ? "Active" : "Inactive";
    }
}

// ============ Delete Activity ============
async function deleteActivity(id) {
    const modalContainer = document.createElement("div");
    modalContainer.id = `delete-activity-modal-${id}`;
    modalContainer.style.cssText = "position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px";
    modalContainer.innerHTML = `
    <div style="background:#fff;border-radius:24px;padding:40px 32px;max-width:360px;width:100%;text-align:center">
        <div style="width:56px;height:56px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e05252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
            </svg>
        </div>
        <h3 style="font-family:var(--font-display);font-size:20px;font-weight:700;color:#1a1a18;margin-bottom:8px">Delete Activity</h3>
        <p style="color:#8a7a6a;font-size:13px;line-height:1.6;margin-bottom:28px">Are you sure you want to delete this activity?<br>This action cannot be undone.</p>
        <div style="display:flex;gap:10px">
            <button onclick="document.getElementById('delete-activity-modal-${id}').remove()"
                style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">
                Cancel
            </button>
            <button onclick="confirmDeleteActivity(${id})"
                style="flex:1;padding:12px;border:none;border-radius:999px;background:#e05252;color:#fff;cursor:pointer;font-size:13px;font-weight:500;font-family:'DM Sans',sans-serif"
                onmouseover="this.style.background='#c94040'" onmouseout="this.style.background='#e05252'">
                Delete
            </button>
        </div>
    </div>
`;
    document.body.appendChild(modalContainer);
    modalContainer.addEventListener("click", function (e) {
        if (e.target === this) this.remove();
    });
}

async function confirmDeleteActivity(id) {
    const modal = document.getElementById(`delete-activity-modal-${id}`);

    const res = await fetch(`/activity/${id}/delete`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("[name=_token]").value,
            Accept: "application/json",
        },
    });

    if (res.ok) {
        modal?.remove();
        const card = document.getElementById(`activity-card-${id}`);
        if (card) {
            card.style.opacity = "0";
            card.style.transition = "opacity 0.3s";
            updateActivityCount(-1);
            setTimeout(() => {
                card.remove();
                const grid = document.getElementById("activities-grid");
                if (grid && grid.querySelectorAll('[id^="activity-card-"]').length === 0) {
                    grid.innerHTML = `
                        <div class="no-results col-span-3">
                            <div style="font-size:48px;margin-bottom:16px">🏃</div>
                            <p style="font-size:16px;font-weight:500;margin-bottom:8px">No activities yet</p>
                            <p style="font-size:13px;color:#a09890">Click "+ Add Activity" to get started</p>
                        </div>`;
                }
            }, 300);
        }
    } else {
        modal?.remove();
    }
}

// ============ Add activity card to grid ============
function addActivityToGrid(act) {
    const grid = document.getElementById("activities-grid");
    if (!grid) return;

    const noResults = grid.querySelector(".no-results");
    if (noResults) noResults.remove();

    const image = act.images && act.images.length > 0
        ? `<img src="/${act.images[0].image_path}" alt="${act.title}" class="activity-img">`
        : `<div class="activity-img-placeholder" style="background:#2A2520;display:flex;align-items:center;justify-content:center;font-size:48px;height:100%">${act.category?.icon ?? "🏃"}</div>`;

    const firstSchedule = act.schedules && act.schedules.length > 0 ? act.schedules[0] : null;
    const remainingCount = act.schedules ? act.schedules.length - 1 : 0;

    const schedulesHtml = firstSchedule
        ? `<div class="schedule-item">
                <span class="schedule-day">${firstSchedule.day_of_week.charAt(0).toUpperCase() + firstSchedule.day_of_week.slice(1)}</span>
                <span class="schedule-time">${firstSchedule.start_time.substring(0,5)} – ${firstSchedule.end_time.substring(0,5)}</span>
           </div>
           ${remainingCount > 0 ? `<button onclick="showAllSchedules(${act.id}, '${act.title.replace(/'/g, "\\'")}')" class="show-more-btn">+${remainingCount} more ${remainingCount === 1 ? 'slot' : 'slots'}</button>` : ''}`
        : '<p style="font-size:11px;color:#b0a898">No schedules added</p>';

    const card = document.createElement("div");
    card.className = "activity-mgmt-card";
    card.id = `activity-card-${act.id}`;
    updateActivityCount(1);

    card.innerHTML = `
        <div class="activity-img-wrapper">
            ${image}
            <div class="activity-actions-overlay">
                <button onclick="openEditActivityModal(${act.id}, ${JSON.stringify(act).replace(/"/g, "&quot;")})"
                    class="icon-btn edit-btn" title="Edit">
                    <span class="material-icons">edit</span>
                </button>
                <button onclick="deleteActivity(${act.id})" class="icon-btn delete-btn" title="Delete">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </div>
        <div class="activity-mgmt-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                <h3 class="font-display" style="font-size:15px;font-weight:700;line-height:1.3">${act.title}</h3>
                <button class="status-badge active flex-shrink-0" onclick="toggleActivityActive(${act.id}, this)">Active</button>
            </div>
            <p style="font-size:11px;color:#8a7a6a;margin-bottom:10px">
                ${act.category?.name ?? ""}${act.level ? " · " + act.level.charAt(0).toUpperCase() + act.level.slice(1) : ""}
            </p>
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
                <span class="pill">$${Math.round(act.price)}/session</span>
                <span class="pill">${act.capacity} spots</span>
                ${act.min_age || act.max_age
                    ? `<span class="pill">Ages ${act.min_age ?? "0"}${act.max_age ? "–" + act.max_age : "+"}</span>`
                    : '<span class="pill">All ages</span>'}
                ${act.is_private ? '<span class="pill">Private</span>' : ""}
            </div>
            <div class="schedules-list">${schedulesHtml}</div>
            <script type="application/json" id="schedules-data-${act.id}">${JSON.stringify(act.schedules || [])}<\/script>
        </div>
    `;

    grid.insertBefore(card, grid.firstChild);
    setTimeout(() => card.scrollIntoView({ behavior: "smooth", block: "nearest" }), 100);
}

// ============ Count ============
function updateActivityCount(delta) {
    const countEl = document.querySelector(".text-white\\/40");
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        const newCount = Math.max(0, current + delta);
        countEl.textContent = newCount + (newCount === 1 ? " activity" : " activities");
    }
}
