// ============ Modal helpers ============
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Close on backdrop click
document.querySelectorAll('[id$="-modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
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
        const errEl = document.getElementById(`${formId}-err-${field}`);
        const input = document.querySelector(`#${formId} [name=${field}]`);
        if (errEl) errEl.textContent = messages[0];
        if (input) input.classList.add('input-error');
    }
    const firstError = document.querySelector(`#${formId} .error-msg:not(:empty)`);
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ============ Add Center ============
document.getElementById('add-center-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors('add-center-form');

    const formData = new FormData(this);
    const res = await ajaxPost('/center-register', formData);

    if (res.ok) {
        const data = await res.json();
        if (data.success) {
            this.reset();
            closeModal('add-center-modal');

            // Add new center to the grid without refresh using returned data
            if (data.center) {
                await addNewCenterToGridDirect(data.center);
            } else {
                // Fallback to old method if no center data returned
                await addNewCenterToGrid();
            }
        }
    } else {
        const data = await res.json();
        if (data.errors) showErrors('add-center-form', data.errors);
    }
});

// ============ Edit Center ============
function openEditModal(id, center) {
    const csrfToken = document.querySelector('[name=_token]').value;
    const container = document.getElementById('edit-form-container');
    container.innerHTML = `
        <form id="edit-center-form" data-id="${id}">
            <input type="hidden" name="_token" value="${csrfToken}">
            <div class="form-group">
                <label class="form-label">Center Name</label>
                <input type="text" name="name" class="form-input" value="${center.name}">
                <p class="error-msg" id="edit-center-form-err-name"></p>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input">${center.description ?? ''}</textarea>
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
                <input type="text" name="phone" class="form-input" value="${center.phone ?? ''}">
            </div>
            <button type="submit" class="search-btn w-full py-3 text-sm mt-2">Update Center</button>
        </form>
    `;

    openModal('edit-center-modal');

    document.getElementById('edit-center-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors('edit-center-form');

        const formData = new FormData(this);
        const res = await ajaxPost(`/center/${id}/update`, formData);

        if (res.ok) {
            const data = await res.json();
            if (data.success) {
                closeModal('edit-center-modal');

                // Update center in the grid without refresh
                await updateCenterInGrid(id, formData);
            }
        } else {
            const data = await res.json();
            if (data.errors) showErrors('edit-center-form', data.errors);
        }
    });
}

// ============ Toggle Active ============
async function toggleActive(id, btn) {
    const res = await fetch(`/center/${id}/toggle-active`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json',
        },
    });

    if (res.ok) {
        const data = await res.json();
        btn.className = 'status-badge ' + (data.is_active ? 'active' : 'inactive');
        btn.textContent = data.is_active ? 'Active' : 'Inactive';
    }
}

// ============ Delete Center ============
async function deleteCenter(id) {
    // Create custom confirmation modal
    const modalContainer = document.createElement('div');
    modalContainer.id = 'delete-confirm-modal-' + id;
    modalContainer.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modalContainer.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-sm w-full mx-4 shadow-2xl">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <span class="material-icons text-red-600" style="font-size: 24px;">delete_outline</span>
            </div>
            <h3 class="text-lg font-bold text-center mb-2">Delete Center</h3>
            <p class="text-gray-600 text-center text-sm mb-6">Are you sure you want to delete this center? This action cannot be undone.</p>
            <div class="flex gap-3">
                <button onclick="document.getElementById('delete-confirm-modal-${id}').remove()"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmDeleteCenter(${id})"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                    Delete
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modalContainer);

    // Close on backdrop click
    modalContainer.addEventListener('click', function(e) {
        if (e.target === this) this.remove();
    });
}

async function confirmDeleteCenter(id) {
    const modalId = 'delete-confirm-modal-' + id;
    const modal = document.getElementById(modalId);

    const res = await fetch(`/center/${id}/delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
            'Accept': 'application/json',
        },
    });

    if (res.ok) {
        // Remove card
        const centerCard = document.getElementById(`center-card-${id}`);
        if (centerCard) {
            centerCard.style.opacity = '0';
            centerCard.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                centerCard.remove();
            }, 300);
        }

        // Update stats - decrease centers count
        const statElements = document.querySelectorAll('.stat-number');
        if (statElements.length > 0) {
            const currentCount = parseInt(statElements[0].textContent) || 1;
            statElements[0].textContent = Math.max(0, currentCount - 1);
        }

        // Close modal
        if (modal) modal.remove();
    } else {
        alert('Error deleting center');
    }
}

// ============ Success Message ============
function showSuccessMessage(message) {
    // Create message element
    const msg = document.createElement('div');
    msg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    msg.textContent = String(message).trim();
    msg.style.fontFamily = 'DM Sans, sans-serif';
    msg.style.fontSize = '14px';
    msg.style.fontWeight = '500';
    msg.style.maxWidth = '300px';
    msg.style.wordWrap = 'break-word';

    document.body.appendChild(msg);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        if (msg.parentNode) {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                if (msg.parentNode) msg.remove();
            }, 300);
        }
    }, 3000);
}

// ============ Load Center Cards ============
// Removed - using dynamic updates instead

// ============ Add New Center to Grid ============
async function addNewCenterToGrid() {
    try {
        // Fetch updated centers data
        const response = await fetch(window.location.href);
        const html = await response.text();
        const parser = new DOMParser();
        const newDoc = parser.parseFromString(html, 'text/html');

        // Update stats
        const newStats = newDoc.querySelectorAll('.stat-number');
        const currentStats = document.querySelectorAll('.stat-number');
        newStats.forEach((newStat, index) => {
            if (currentStats[index]) {
                currentStats[index].textContent = newStat.textContent;
            }
        });

        // Update centers grid
        const newGrid = newDoc.querySelector('#centers-grid');
        const currentGrid = document.querySelector('#centers-grid');

        if (newGrid && currentGrid) {
            // Remove "no centers" message if it exists
            const noResults = currentGrid.querySelector('.no-results');
            if (noResults) {
                noResults.remove();
            }

            // Add new centers
            const existingCenterIds = Array.from(currentGrid.querySelectorAll('[id^="center-card-"]'))
                .map(card => card.id.replace('center-card-', ''));

            newGrid.querySelectorAll('[id^="center-card-"]').forEach(newCard => {
                const cardId = newCard.id.replace('center-card-', '');
                if (!existingCenterIds.includes(cardId)) {
                    currentGrid.appendChild(newCard.cloneNode(true));
                }
            });
        }
    } catch (error) {
        console.error('Error adding center to grid:', error);
        // Fallback to reload
        window.location.reload();
    }
}

// ============ Add New Center to Grid Direct ============
async function addNewCenterToGridDirect(center) {
    try {
        let grid = document.querySelector('#centers-grid');
        if (!grid) {
            const sectionTitle = document.querySelector('h2.font-display.text-2xl.font-bold.mb-6');
            grid = document.createElement('div');
            grid.id = 'centers-grid';
            grid.className = 'grid md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12';
            if (sectionTitle && sectionTitle.parentNode) {
                sectionTitle.parentNode.insertBefore(grid, sectionTitle.nextSibling);
            } else {
                document.body.appendChild(grid);
            }
        }

        // Remove "no centers" message if it exists
        const noResults = grid.querySelector('.no-results');
        if (noResults) {
            noResults.remove();
        }

        // Update stats
        const statElements = document.querySelectorAll('.stat-number');
        if (statElements.length > 0) {
            // Assuming first stat is centers count
            const currentCount = parseInt(statElements[0].textContent) || 0;
            statElements[0].textContent = currentCount + 1;
        }

        // Create center card HTML
        const centerCard = document.createElement('div');
        centerCard.className = 'center-card';
        centerCard.id = `center-card-${center.id}`;

        const isActive = center.is_active === undefined || center.is_active === null ? true : Boolean(center.is_active);

        // Create logo placeholder
        const logoInitials = center.name ? center.name.substring(0, 2).toUpperCase() : 'CN';

        centerCard.innerHTML = `
            <div class="center-card-logo-wrapper">
                <div class="center-card-logo">
                    <div class="center-logo-placeholder">${logoInitials}</div>
                </div>
                <div class="card-actions-overlay">
                    <button onclick="openEditModal(${center.id}, ${JSON.stringify(center).replace(/"/g, '&quot;')})"
                        class="icon-btn edit-btn" title="Edit">
                        <span class="material-icons">edit</span>
                    </button>
                    <button onclick="deleteCenter(${center.id})"
                        class="icon-btn delete-btn" title="Delete">
                        <span class="material-icons">delete</span>
                    </button>
                </div>
            </div>
            <div class="center-card-body">
                <div class="center-card-header">
                    <h3 class="font-display text-lg font-bold">${center.name}</h3>
                    <button class="status-badge ${isActive ? 'active' : 'inactive'}"
                        onclick="toggleActive(${center.id}, this)">
                        ${isActive ? 'Active' : 'Inactive'}
                    </button>
                </div>
                <p class="text-xs mb-1" style="color:#8a7a6a">${center.address}, ${center.city}</p>
                <p class="text-xs mb-4" style="color:#8a7a6a">0 activities</p>
                <div class="flex gap-2 w-full pt-4 border-t border-[#F0EDE6]">
                    <a href="/center/${center.id}/activities"
                        class="dashboard-btn flex-1 text-center text-xs">
                        Manage Activities
                    </a>
                    <a href="/center/${center.id}/bookings"
                        class="dashboard-btn-outline flex-1 text-center text-xs">
                        Bookings
                    </a>
                </div>
            </div>
        `;

        // Add to grid at top and scroll to it
        grid.insertBefore(centerCard, grid.firstChild);
        centerCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

    } catch (error) {
        console.error('Error adding center directly to grid:', error);
        // Fallback to reload
        window.location.reload();
    }
}

// ============ Update Center in Grid ============
async function updateCenterInGrid(centerId, formData) {
    try {
        const centerCard = document.getElementById(`center-card-${centerId}`);
        if (!centerCard) {
            // If card not found, reload page
            window.location.reload();
            return;
        }

        // Update card content with form data
        const name = formData.get('name');
        const address = formData.get('address');
        const city = formData.get('city');
        const description = formData.get('description');
        const phone = formData.get('phone');

        // Update name
        const nameElement = centerCard.querySelector('h3');
        if (nameElement && name) {
            nameElement.textContent = name;
        }

        // Update address and city
        const addressElements = centerCard.querySelectorAll('p');
        if (addressElements.length >= 1 && address && city) {
            addressElements[0].textContent = `${address}, ${city}`;
        }

        // Update description if it exists
        const descElement = centerCard.querySelector('.center-description');
        if (descElement && description) {
            descElement.textContent = description;
        }

        // Update phone if it exists
        const phoneElement = centerCard.querySelector('.center-phone');
        if (phoneElement && phone) {
            phoneElement.textContent = `Phone: ${phone}`;
        }

        // Update the edit button's onclick to reflect new data
        const editButton = centerCard.querySelector('.edit-btn');
        if (editButton) {
            const currentStatus = centerCard.querySelector('.status-badge')?.classList.contains('active') ? 1 : 0;
            const updatedCenterData = {
                id: centerId,
                name: name || '',
                description: description || '',
                address: address || '',
                city: city || '',
                phone: phone || '',
                is_active: currentStatus
            };
            editButton.onclick = () => openEditModal(centerId, updatedCenterData);
        }

        // Move updated card to top and scroll to it
        const grid = document.querySelector('#centers-grid');
        if (grid && centerCard.parentNode === grid) {
            grid.prepend(centerCard);
            centerCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

    } catch (error) {
        console.error('Error updating center in grid:', error);
        // Fallback to reload
        window.location.reload();
    }
}
