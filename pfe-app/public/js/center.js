// ============ Add Center ============
document
    .getElementById("add-center-form")
    ?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearErrors("add-center-form");

        const res = await ajaxPost("/center-register", new FormData(this));

        if (res.ok) {
            const data = await res.json();
            if (data.success) {
                this.reset();
                closeModal("add-center-modal");
                if (data.center) {
                    addNewCenterToGridDirect(data.center);
                } else {
                    window.location.reload();
                }
            }
        } else {
            const data = await res.json();
            if (data.errors) showErrors("add-center-form", data.errors);
        }
    });

// ============ Edit Center ============
function openEditModal(id, center) {
    const container = document.getElementById("edit-form-container");
    container.innerHTML = `
    <form id="edit-center-form" data-id="${id}">
        <input type="hidden" name="_token" value="${document.querySelector("[name=_token]").value}">
        <div class="form-group">
            <label class="form-label">Center Name</label>
            <input type="text" name="name" class="form-input" value="${center.name}">
            <p class="error-msg" id="edit-center-form-err-name"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-input">${center.description ?? ""}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-input" value="${center.address}">
            <p class="error-msg" id="edit-center-form-err-address"></p>
        </div>
        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-input" value="${center.city}">
            <p class="error-msg" id="edit-center-form-err-city"></p>
        </div>
        <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-input" value="${center.phone ?? ""}">
        </div>
        <div class="form-group">
            <label class="form-label">Location on Map</label>
            <p class="text-xs mb-2" style="color:#a09890">Click on the map to pin your center's location</p>
            <div id="edit-center-form-map" style="height:220px;border-radius:12px;border:1px solid #E8E5DF;overflow:hidden;z-index:1"></div>
            <input type="hidden" name="lat" id="edit-center-form-lat" value="${center.lat ?? ""}">
            <input type="hidden" name="lng" id="edit-center-form-lng" value="${center.lng ?? ""}">
            <p class="text-xs mt-2" id="edit-center-form-coords" style="color:#a09890">
                ${center.lat ? "📍 " + parseFloat(center.lat).toFixed(5) + ", " + parseFloat(center.lng).toFixed(5) : "No location selected"}
            </p>
        </div>
        <button type="submit" class="search-btn w-full py-3 text-sm mt-2">Update Center</button>
    </form>
`;
    openModal("edit-center-modal");
    setTimeout(() => initCenterMap("edit-center-form"), 100);

    document
        .getElementById("edit-center-form")
        .addEventListener("submit", async function (e) {
            e.preventDefault();
            clearErrors("edit-center-form");

            const formData = new FormData(this);
            const res = await ajaxPost(`/center/${id}/update`, formData);

            if (res.ok) {
                closeModal("edit-center-modal");
                updateCenterInGrid(id, formData);
            } else {
                const data = await res.json();
                if (data.errors) showErrors("edit-center-form", data.errors);
            }
        });
}

// ============ Toggle Active ============
async function toggleActive(id, btn) {
    const res = await fetch(`/center/${id}/toggle-active`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("[name=_token]").value,
            Accept: "application/json",
        },
    });

    if (res.ok) {
        const data = await res.json();
        btn.className =
            "status-badge " + (data.is_active ? "active" : "inactive");
        btn.textContent = data.is_active ? "Active" : "Inactive";
    }
}

// ============ Delete Center ============
async function deleteCenter(id) {
    const modalContainer = document.createElement("div");
    modalContainer.id = `delete-confirm-modal-${id}`;
    modalContainer.style.cssText =
        "position:fixed;inset:0;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px";
    modalContainer.innerHTML = `
        <div style="background:#fff;border-radius:24px;padding:40px 32px;max-width:360px;width:100%;text-align:center">
            <div style="width:56px;height:56px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e05252" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>
            <h3 style="font-family:var(--font-display);font-size:20px;font-weight:700;color:#1a1a18;margin-bottom:8px">Delete Center</h3>
            <p style="color:#8a7a6a;font-size:13px;line-height:1.6;margin-bottom:28px">Are you sure you want to delete this center?<br>This action cannot be undone.</p>
            <div style="display:flex;gap:10px">
                <button onclick="document.getElementById('delete-confirm-modal-${id}').remove()"
                    style="flex:1;padding:12px;border:1px solid #E8E5DF;border-radius:999px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#5a5751;font-family:'DM Sans',sans-serif"
                    onmouseover="this.style.borderColor='#1a1a18'" onmouseout="this.style.borderColor='#E8E5DF'">
                    Cancel
                </button>
                <button onclick="confirmDeleteCenter(${id})"
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

async function confirmDeleteCenter(id) {
    const modal = document.getElementById(`delete-confirm-modal-${id}`);

    const res = await fetch(`/center/${id}/delete`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("[name=_token]").value,
            Accept: "application/json",
        },
    });

    if (res.ok) {
        modal?.remove();
        const card = document.getElementById(`center-card-${id}`);
        if (card) {
            card.style.opacity = "0";
            card.style.transition = "opacity 0.3s";
            setTimeout(() => {
                card.remove();
                const stat = document.querySelectorAll(".stat-number")[0];
                if (stat)
                    stat.textContent = Math.max(
                        0,
                        parseInt(stat.textContent || 0) - 1,
                    );
                const grid = document.getElementById("centers-grid");
                if (
                    grid &&
                    grid.querySelectorAll('[id^="center-card-"]').length === 0
                ) {
                    grid.innerHTML = `
                        <div class="no-results col-span-3">
                            <p style="font-size:16px;font-weight:500;margin-bottom:8px">No centers yet</p>
                            <p style="font-size:13px;color:#a09890">Click "Add Center" to get started</p>
                        </div>`;
                }
            }, 300);
        }
    } else {
        modal?.remove();
    }
}

// ============ Add center to grid ============
function addNewCenterToGridDirect(center) {
    const grid = document.getElementById("centers-grid");
    if (!grid) return;

    const noResults = grid.querySelector(".no-results");
    if (noResults) noResults.remove();

    const stat = document.querySelectorAll(".stat-number")[0];
    if (stat) stat.textContent = parseInt(stat.textContent || 0) + 1;

    const isActive = center.is_active ?? true;
    const initials = center.name
        ? center.name.substring(0, 2).toUpperCase()
        : "CN";

    const card = document.createElement("div");
    card.className = "center-card";
    card.id = `center-card-${center.id}`;
    card.innerHTML = `
        <div class="center-card-logo-wrapper">
            <div class="center-card-logo">
                <div class="center-logo-placeholder">${initials}</div>
            </div>
            <div class="card-actions-overlay">
                <button onclick="openEditModal(${center.id}, ${JSON.stringify(center).replace(/"/g, "&quot;")})"
                    class="icon-btn edit-btn" title="Edit">
                    <span class="material-icons">edit</span>
                </button>
                <button onclick="deleteCenter(${center.id})" class="icon-btn delete-btn" title="Delete">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </div>
        <div class="center-card-body">
            <div class="center-card-header">
                <h3 class="font-display text-lg font-bold">${center.name}</h3>
                <button class="status-badge ${isActive ? "active" : "inactive"}"
                    onclick="toggleActive(${center.id}, this)">
                    ${isActive ? "Active" : "Inactive"}
                </button>
            </div>
            <p class="text-xs mb-1" style="color:#8a7a6a">${center.address}, ${center.city}</p>
            <p class="text-xs mb-4" style="color:#8a7a6a">0 activities</p>
            <div style="display:flex;gap:8px;padding-top:12px;border-top:1px solid #F0EDE6">
                <a href="/center/${center.id}/activities" class="dashboard-btn flex-1 text-center text-xs">Manage Activities</a>
                <a href="/center/${center.id}/bookings" class="dashboard-btn-outline flex-1 text-center text-xs">Bookings</a>
            </div>
        </div>
    `;

    grid.insertBefore(card, grid.firstChild);
    setTimeout(
        () => card.scrollIntoView({ behavior: "smooth", block: "center" }),
        100,
    );
}

// ============ Update center in grid ============
function updateCenterInGrid(id, formData) {
    const card = document.getElementById(`center-card-${id}`);
    if (!card) {
        window.location.reload();
        return;
    }

    const name = formData.get("name");
    const address = formData.get("address");
    const city = formData.get("city");

    const nameEl = card.querySelector("h3");
    if (nameEl) nameEl.textContent = name;

    const ps = card.querySelectorAll("p");
    if (ps[0]) ps[0].textContent = `${address}, ${city}`;

    const editBtn = card.querySelector(".edit-btn");
    if (editBtn) {
        const isActive = card
            .querySelector(".status-badge")
            ?.classList.contains("active")
            ? 1
            : 0;
        const updated = {
            id,
            name,
            address,
            city,
            description: formData.get("description"),
            phone: formData.get("phone"),
            is_active: isActive,
        };
        editBtn.onclick = () => openEditModal(id, updated);
    }

    const grid = document.getElementById("centers-grid");
    if (grid) {
        grid.insertBefore(card, grid.firstChild);
        setTimeout(
            () => card.scrollIntoView({ behavior: "smooth", block: "center" }),
            100,
        );
    }
}
