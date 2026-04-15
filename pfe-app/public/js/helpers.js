// ============ Modal helpers ============
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function openCenterModal(modalId, formId) {
    openModal(modalId);
    setTimeout(() => initCenterMap(formId), 100);
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Close on backdrop click
document.addEventListener('click', function(e) {
    if (e.target.matches('[id$="-modal"]')) {
        closeModal(e.target.id);
    }
});

// ============ AJAX helper ============
async function ajaxPost(url, formData) {
    return await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json',
        },
        body: formData,
    });
}

// ============ Form errors ============
function clearErrors(formId) {
    document.querySelectorAll(`#${formId} .error-msg`).forEach(el => el.textContent = '');
    document.querySelectorAll(`#${formId} .input-error`).forEach(el => el.classList.remove('input-error'));
}

function showErrors(formId, errors) {
    for (const [field, messages] of Object.entries(errors)) {

        // Schedule errors مثل schedules.0.day_of_week
        if (field.startsWith('schedules.')) {
            const parts = field.split('.');
            const index = parts[1];
            const subField = parts[2];
            const container = document.getElementById(`${formId}-schedules`);
            if (container && container.children[index]) {
                const input = container.children[index].querySelector(`[name*="${subField}"]`);
                if (input) input.classList.add('input-error');
            }
            continue;
        }

        // Normal errors
        const errEl = document.getElementById(`${formId}-err-${field}`);
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (errEl) errEl.textContent = messages[0];
        if (input) input.classList.add('input-error');
    }

    const firstError = document.querySelector(`#${formId} .input-error`);
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ============ Success toast ============
function showSuccess(message) {
    const msg = document.createElement('div');
    msg.style.cssText = `
        position:fixed; top:20px; right:20px; z-index:9999;
        background:#1a1a18; color:#fff; padding:12px 20px;
        border-radius:12px; font-size:13px; font-weight:500;
        border-left:3px solid #D4A350; font-family:'DM Sans',sans-serif;
        transition: opacity 0.3s;
    `;
    msg.textContent = message;
    document.body.appendChild(msg);
    setTimeout(() => {
        msg.style.opacity = '0';
        setTimeout(() => msg.remove(), 300);
    }, 3000);
}

// ============ Center Map ============
function initCenterMap(formId, defaultLat = 33.8938, defaultLng = 35.5018) {
    const mapEl = document.getElementById(`${formId}-map`);
    if (!mapEl || mapEl._leaflet_id) return;

    const latInput = document.getElementById(`${formId}-lat`);
    const lngInput = document.getElementById(`${formId}-lng`);
    const coordsEl = document.getElementById(`${formId}-coords`);

    const lat = parseFloat(latInput?.value) || defaultLat;
    const lng = parseFloat(lngInput?.value) || defaultLng;

    const map = L.map(`${formId}-map`).setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;

    // إذا في موقع محفوظ حط marker
    if (latInput?.value && lngInput?.value) {
        marker = L.marker([lat, lng]).addTo(map);
    }

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marker) marker.remove();
        marker = L.marker([lat, lng]).addTo(map);

        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (coordsEl) coordsEl.textContent = `📍 ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
    });
}


async function toggleFavourite(activityId, btn) {
    const res = await fetch(`/activity/${activityId}/favourite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json',
        },
    });

    if (res.ok) {
        const data = await res.json();
        const svg = btn.querySelector('svg');
        if (data.saved) {
            btn.classList.add('saved');
            svg.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.remove('saved');
            svg.setAttribute('fill', 'none');
        }
    }
}

async function removeFavourite(activityId, btn) {
    const res = await fetch(`/activity/${activityId}/favourite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json',
        },
    });

    if (res.ok) {
        const card = document.getElementById(`fav-card-${activityId}`);
        if (card) {
            card.style.opacity = '0';
            card.style.transition = 'opacity 0.3s';
            setTimeout(() => {
                card.remove();
                // إذا ما في cards تانية
                const grid = document.querySelector('.grid.grid-cols-2');
                if (grid && grid.querySelectorAll('.fav-card').length === 0) {
                    grid.outerHTML = `
                        <div class="no-results">
                            <div class="mb-3" style="width:48px;height:48px;margin:0 auto;background:#F0EDE6;border-radius:50%;display:flex;align-items:center;justify-content:center">
                                <svg width="22" height="22" fill="none" stroke="#a09890" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </div>
                            <p class="font-medium mb-1">No saved activities</p>
                            <p class="text-sm"><a href="/activities" class="text-[#D4A350]">Explore activities</a></p>
                        </div>`;
                }
            }, 300);
        }
    }
}
