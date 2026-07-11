# 🏃 Activio Platform - Complete Application Mapping
## All Pages, Controllers, Routes & JavaScript Logic

**Last Updated:** July 5, 2026  
**Project:** Activity Booking Platform (Laravel + Blade + JavaScript)  
**Version:** Complete Extraction

---

## 📑 Table of Contents
1. [Route Structure](#route-structure)
2. [Public Pages](#public-pages)
3. [User Dashboard & Profile](#user-dashboard--profile)
4. [Activity Management](#activity-management)
5. [Center Owner Workflow](#center-owner-workflow)
6. [Admin Dashboard](#admin-dashboard)
7. [Payment & Credits System](#payment--credits-system)
8. [JavaScript Modules](#javascript-modules)
9. [Controllers Reference](#controllers-reference)

---

## 🗺️ Route Structure

### Root Routes
```
GET  /                      → HomeController@index (name: home)
GET  /about                 → Static view: about.blade.php
GET  /privacy               → Static view: privacy.blade.php
```

### Public Pages
```
GET  /activities            → ActivityController@index (Activities grid)
GET  /activities/{activity} → ActivityController@show (Activity detail)
GET  /contact               → ContactController@index
POST /contact               → ContactController@store
GET  /for-centers           → CenterController@index (Center registration page)
GET  /search                → SearchController@index
POST /search                → SearchController@search
POST /save-location         → HomeController@saveLocation (auth required)
```

### Booking & Interactions
```
POST /schedule/{schedule}/book      → BookingController@store
POST /activity/{activity}/favourite → FavouriteController@toggle (auth)
POST /booking/{booking}/review      → ReviewController@store (auth)
POST /booking/{booking}/status      → BookingController@updateStatus (center_owner auth)
DELETE /booking/{booking}/delete    → BookingController@destroy (auth)
```

### Center Operator Routes (Prefix: `/center/`, Middleware: `auth`, `center_owner`)
```
POST /center-register                → CenterController@store
GET  /center/dashboard               → CenterController@dashboard
POST /center/{center}/toggle-active  → CenterController@toggleActive
POST /center/{center}/update         → CenterController@update
DELETE /center/{center}/delete       → CenterController@destroy
GET  /center/{center}/bookings       → CenterController@bookings
GET  /center/{center}/activities     → ActivityController@activities
POST /center/{center}/activities     → ActivityController@store
POST /activity/{activity}/update     → ActivityController@update
DELETE /activity/{activity}/delete   → ActivityController@destroy
POST /activity/{activity}/toggle-active → ActivityController@toggleActive
```

### Admin Routes (Prefix: `/admin/`, Middleware: `auth`, `admin`)
```
GET    /admin/                              → AdminController@dashboard
GET    /admin/users                         → AdminController@users
POST   /admin/users/{user}/toggle           → AdminController@toggleUser
DELETE /admin/users/{user}                  → AdminController@destroyUser
GET    /admin/centers                       → AdminController@centers
POST   /admin/centers/{center}/toggle       → AdminController@toggleCenter
DELETE /admin/centers/{center}              → AdminController@destroyCenter
GET    /admin/activities                    → AdminController@activities
DELETE /admin/activity/{activity}/delete    → AdminController@destroy
POST   /admin/activity/{activity}/toggle-active → AdminController@toggleActive
GET    /admin/bookings                      → AdminController@bookings
GET    /admin/payments                      → AdminController@payments
GET    /admin/reviews                       → AdminController@reviews
DELETE /admin/reviews/{review}              → AdminController@destroyReview
GET    /admin/profile                       → AdminController@profile
```

### User Profile Routes (Middleware: `auth`)
```
GET  /dashboard                 → view('dashboard')
GET  /profile                   → ProfileController@index
POST /profile/update            → ProfileController@update
POST /profile/password          → ProfileController@updatePassword
GET  /notifications             → NotificationController@index
POST /notifications/read-all    → NotificationController@readAll
POST /notifications/{id}/read   → NotificationController@read
```

### Credit System Routes (Prefix: `/credits/`, Middleware: `auth`)
```
GET /credits/              → CreditController@dashboard
GET /credits/purchase      → CreditController@purchase
GET /credits/api/stats     → CreditController@getStats
```

### Payment Routes (Prefix: `/payment/`, Middleware: `auth`)
```
GET  /payment/packages        → PaymentController@showPackages
POST /payment/create-intent   → PaymentController@createPaymentIntent
POST /payment/confirm         → PaymentController@confirmPayment
POST /webhook/stripe          → PaymentController@webhook (no auth)
```

---

## 📄 Public Pages

### 1. Home Page (`resources/views/home.blade.php`)
**Route:** `GET /`  
**Controller:** `HomeController@index`

**Key Features:**
- AI search hero with input suggestions
- Location-based activity cards
- Popular activity listings
- Call-to-action sections
- Map display of nearby activities (Leaflet.js)

**Inline JavaScript:**
```javascript
// AI Parse button click
document.getElementById('ai-parse-btn')?.addEventListener('click', async () => {
    const query = document.getElementById('search-input').value;
    const res = await fetch('/search', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ q: query })
    });
    if (res.ok) window.location.href = '/search?q=' + encodeURIComponent(query);
});

// Auto-location detection
if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(pos => {
        fetch('/save-location', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            })
        });
    });
}

// Map initialization
const map = L.map('map-container').setView([33.8938, 35.5018], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
nearbyActivities.forEach(act => {
    L.marker([act.lat, act.lng]).bindPopup(act.title).addTo(map);
});
```

---

### 2. Activities Listing Page (`resources/views/activities.blade.php`)
**Route:** `GET /activities`  
**Controller:** `ActivityController@index`

**Key Features:**
- Activity grid with filters
- Category dropdown selector
- Level filter (All, Beginner, Intermediate, Advanced)
- Price range filter (slider)
- Age filter
- Real-time filtering via dropdown change
- Modal for detailed activity view

**Inline JavaScript:**
```javascript
// Category filter
document.getElementById('category-filter')?.addEventListener('change', function() {
    const url = new URL(window.location);
    this.value ? url.searchParams.set('category', this.value) : url.searchParams.delete('category');
    window.location.href = url;
});

// Level filter
document.getElementById('level-filter')?.addEventListener('change', function() {
    const url = new URL(window.location);
    this.value ? url.searchParams.set('level', this.value) : url.searchParams.delete('level');
    window.location.href = url;
});

// Price range filter
const priceRange = document.getElementById('price-range');
if (priceRange) {
    priceRange.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('max_price', this.value);
        window.location.href = url;
    });
}

// Activity card click → show details in modal
document.querySelectorAll('.activity-card').forEach(card => {
    card.addEventListener('click', function() {
        const activityId = this.dataset.id;
        const activity = window.ACTIVITIES.find(a => a.id == activityId);
        showActivityModal(activity);
    });
});
```

---

### 3. Activity Detail Page (`resources/views/activity-detail.blade.php`)
**Route:** `GET /activities/{activity}`  
**Controller:** `ActivityController@show`

**Key Features:**
- Full activity details (title, description, images, category, price, schedules)
- Instructor/center info card
- Reviews section
- Schedule display (multiple time slots)
- Booking button (opens booking modal)
- Favorite toggle
- Similar activities carousel

**Inline JavaScript:**
```javascript
// Favorite toggle
document.getElementById('favourite-btn')?.addEventListener('click', async function() {
    const res = await fetch(`/activity/${ACTIVITY_ID}/favourite`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
    });
    if (res.ok) {
        const data = await res.json();
        this.classList.toggle('saved', data.saved);
        this.querySelector('svg').setAttribute('fill', data.saved ? 'currentColor' : 'none');
    }
});

// Book activity (schedule selection)
document.getElementById('book-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const scheduleId = document.getElementById('schedule-select').value;
    const res = await fetch(`/schedule/${scheduleId}/book`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ seats: 1 })
    });
    if (res.ok) showSuccess('Booking confirmed! Check your profile.');
});

// Reviews carousel/pagination
document.querySelectorAll('.review-card').forEach((card, i) => {
    if (i >= 3) card.style.display = 'none'; // Show first 3
});

document.getElementById('load-more-reviews')?.addEventListener('click', function() {
    document.querySelectorAll('.review-card:hidden').slice(0, 3).forEach(c => c.style.display = 'block');
});
```

---

### 4. Search Results Page (`resources/views/search.blade.php`)
**Route:** `GET /search`, `POST /search`  
**Controller:** `SearchController@index`, `SearchController@search`

**Key Features:**
- AI-powered search query parsing
- Search box with suggestions
- Filter by category, level, age, price
- Results display with relevance ranking
- Pagination

**Inline JavaScript:**
```javascript
// AI search auto-suggestions
document.getElementById('search-input').addEventListener('input', async function(e) {
    if (e.target.value.length < 2) return;
    const res = await fetch(`/search?q=${encodeURIComponent(e.target.value)}&json=1`, {
        headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    const suggestions = document.getElementById('suggestions');
    suggestions.innerHTML = data.results.slice(0, 5)
        .map(a => `<div onclick="selectSuggestion('${a.title}')">${a.title}</div>`)
        .join('');
});

// Filter by category
document.getElementById('search-category')?.addEventListener('change', function() {
    document.getElementById('search-form').submit();
});

// Filter by age range (dynamically set min/max age)
document.getElementById('age-range').addEventListener('change', function() {
    document.getElementById('age-input').value = this.value;
    document.getElementById('search-form').submit();
});
```

---

### 5. Contact Page (`resources/views/contact.blade.php`)
**Route:** `GET /contact`, `POST /contact`  
**Controller:** `ContactController@index`, `ContactController@store`

**Key Features:**
- Contact form (name, email, subject, message)
- AJAX submission
- Real-time validation
- Success/error message display

**Inline JavaScript:**
```javascript
document.getElementById('contact-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors('contact-form');

    const res = await ajaxPost('/contact', new FormData(this));

    if (res.ok) {
        const data = await res.json();
        if (data.success) {
            this.reset();
            showSuccess('Message sent! We will contact you soon.');
        }
    } else {
        const data = await res.json();
        if (data.errors) showErrors('contact-form', data.errors);
    }
});
```

---

### 6. For Centers Page (`resources/views/for-centers.blade.php`)
**Route:** `GET /for-centers`  
**Controller:** `CenterController@index`

**Key Features:**
- Center registration CTA
- Benefits showcase
- Add Center modal form
- Map location picker (Leaflet)
- Form validation
- Centers grid display (if user is logged in as center owner)

**Inline JavaScript:**
```javascript
document.getElementById('add-center-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors('add-center-form');

    const res = await ajaxPost('/center-register', new FormData(this));

    if (res.ok) {
        const data = await res.json();
        if (data.success) {
            this.reset();
            closeModal('add-center-modal');
            if (data.center) {
                addNewCenterToGridDirect(data.center);
            } else {
                window.location.reload();
            }
        }
    } else {
        const data = await res.json();
        if (data.errors) showErrors('add-center-form', data.errors);
    }
});

// Map initialization in modal
document.getElementById('add-center-modal')?.addEventListener('show', () => {
    setTimeout(() => initCenterMap('add-center-form'), 100);
});
```

---

### 7. About Page (`resources/views/about.blade.php`)
**Route:** `GET /about`

**Content:**
- Hero section ("Built for Lebanon, Powered by AI")
- Mission statement
- Key features: AI-Powered Search, Local First, Book Instantly
- Built in Lebanon callout
- No dynamic JavaScript

---

### 8. Privacy Policy Page (`resources/views/privacy.blade.php`)
**Route:** `GET /privacy`

**Content:**
- 7 sections: Information Collection, Usage, Storage, Cookies, Third Parties, Rights, Policy Changes
- Contact link to contact form
- No dynamic JavaScript

---

## 👤 User Dashboard & Profile

### 1. User Profile Page (`resources/views/profile.blade.php`)
**Route:** `GET /profile`  
**Controller:** `ProfileController@index`

**Key Features:**
- User profile display
- Edit profile form
- Change password form
- Logout button

**Data:**
- Profile: name, email, phone, age, gender, bio
- Account: email verification status
- Security: password change

**Forms:**
```javascript
// Profile update
document.getElementById('profile-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors('profile-form');

    const res = await ajaxPost('/profile/update', new FormData(this));
    if (res.ok) showSuccess('Profile updated successfully');
    else showErrors('profile-form', (await res.json()).errors);
});

// Password change
document.getElementById('password-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors('password-form');

    const res = await ajaxPost('/profile/password', new FormData(this));
    if (res.ok) {
        this.reset();
        showSuccess('Password changed successfully');
    } else {
        showErrors('password-form', (await res.json()).errors);
    }
});
```

---

### 2. User Dashboard (`resources/views/dashboard.blade.php`)
**Route:** `GET /dashboard`  
**Middleware:** `auth`, `verified`

**Key Features:**
- Upcoming bookings widget
- Recent activity feed
- Credits balance display
- Quick links to key pages

---

### 3. My Bookings / History
**Implied View:** Bookings listed in user dashboard or profile

**Features:**
- Past and upcoming bookings
- Review button for completed bookings
- Cancel button for upcoming bookings (pre-activity only)
- Booking status: pending, confirmed, completed, cancelled

---

## 🎯 Activity Management

### 1. Center Owner Activities Management
**Route:** `GET /center/{center}/activities`  
**Controller:** `ActivityController@activities`

**Key Features:**
- Grid of activities for the center
- Add activity modal form
- Edit activity modal
- Delete activity confirmation
- Toggle active/inactive status
- Schedule management (multiple time slots per activity)

**Models Form Fields:**
```
- title
- description
- category_id (with visual picker)
- level (beginner, intermediate, advanced)
- price (per session)
- capacity (participants)
- min_age, max_age
- images
- is_private (1-on-1 flag)
- schedules[] (array of { day_of_week, start_time, end_time })
```

**Inline JavaScript (from `public/js/activity.js`):**

**Category Picker:**
```javascript
function pickCategory(formId, catId, btn) {
    document.getElementById(formId + '-category_id').value = catId;
    document.querySelectorAll('#' + formId + '-cat-picker .cat-pick-btn').forEach(b => {
        b.style.borderColor = '#e5e7eb';
        b.style.background = '#fff';
    });
    btn.style.borderColor = '#D4A350';
    btn.style.background = '#FDF8EE';
}
```

**Schedule Management:**
```javascript
const DAYS = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"];

function addScheduleRow(containerId, data = {}) {
    const container = document.getElementById(containerId);
    const index = container.children.length;
    const row = document.createElement("div");
    row.className = "schedule-row";

    const daysOptions = DAYS.map(
        (d) => `<option value="${d}" ${data.day_of_week === d ? "selected" : ""}>${d.charAt(0).toUpperCase() + d.slice(1)}</option>`
    ).join("");

    row.innerHTML = `
        <select name="schedules[${index}][day_of_week]">
            <option value="">Day</option>
            ${daysOptions}
        </select>
        <input type="time" name="schedules[${index}][start_time]" value="${data.start_time ?? ""}" onchange="validateScheduleTime(this)">
        <input type="time" name="schedules[${index}][end_time]" value="${data.end_time ?? ""}" onchange="validateScheduleTime(this)">
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

function removeScheduleRow(btn) {
    const container = btn.closest(".schedule-row").parentElement;
    btn.closest(".schedule-row").remove();
    // Re-index all rows
    Array.from(container.children).forEach((row, i) => {
        row.querySelector('[name*="day_of_week"]').name = `schedules[${i}][day_of_week]`;
        row.querySelector('[name*="start_time"]').name  = `schedules[${i}][start_time]`;
        row.querySelector('[name*="end_time"]').name    = `schedules[${i}][end_time]`;
    });
}
```

**Show All Schedules Modal:**
```javascript
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
                <button onclick="document.getElementById('schedules-modal').remove()" style="background:none;border:none;cursor:pointer;color:#a09890;font-size:20px;line-height:1">✕</button>
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
```

**Add Activity Submit:**
```javascript
document.getElementById("add-activity-form")?.addEventListener("submit", async function (e) {
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
            document.querySelectorAll('#add-activity-form-cat-picker .cat-pick-btn').forEach(b => {
                b.style.borderColor = '#e5e7eb';
                b.style.background = '#fff';
            });
            closeModal("add-activity-modal");
            addActivityToGrid(data.activity);
        }
    } else {
        const data = await res.json();
        if (data.errors) showErrors("add-activity-form", data.errors);
    }
});
```

**Edit Activity Modal:**
```javascript
function openEditActivityModal(id, activity) {
    const container = document.getElementById("edit-activity-form-container");

    const catPickerBtns = CATEGORIES.map((c) =>
        `<button type="button"
            onclick="pickCategory('edit-activity-form', ${c.id}, this)"
            class="cat-pick-btn"
            style="...styling...">
            <img src="/images/categories/${c.icon}" alt="${c.name}">
            <span>${c.name}</span>
        </button>`
    ).join('');

    // Form HTML with all fields pre-filled...
    container.innerHTML = `<form id="edit-activity-form" enctype="multipart/form-data">...`;

    if (activity.schedules && activity.schedules.length > 0) {
        activity.schedules.forEach((s) => addScheduleRow("edit-activity-form-schedules", s));
        validateAllSchedules("edit-activity-form-schedules");
    }

    openModal("edit-activity-modal");

    document.getElementById("edit-activity-form").addEventListener("submit", async function (e) {
        e.preventDefault();
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
```

**Toggle Activity Active/Inactive:**
```javascript
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
```

**Delete Activity:**
```javascript
async function deleteActivity(id) {
    const modalContainer = document.createElement("div");
    modalContainer.id = `delete-activity-modal-${id}`;
    // ... styled confirmation modal ...
    document.body.appendChild(modalContainer);
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
                    grid.innerHTML = `<div class="no-results">No activities yet</div>`;
                }
            }, 300);
        }
    } else {
        modal?.remove();
    }
}
```

**Add/Update Activity in Grid:**
```javascript
function addActivityToGrid(act) {
    const grid = document.getElementById("activities-grid");
    if (!grid) return;

    const noResults = grid.querySelector(".no-results");
    if (noResults) noResults.remove();

    // Build card HTML
    const card = document.createElement("div");
    card.className = "activity-mgmt-card";
    card.id = `activity-card-${act.id}`;
    card.innerHTML = `...activity card HTML...`;

    grid.insertBefore(card, grid.firstChild);
    setTimeout(() => card.scrollIntoView({ behavior: "smooth", block: "nearest" }), 100);
}

function updateActivityInGrid(id, act) {
    const card = document.getElementById(`activity-card-${id}`);
    if (!card) {
        window.location.reload();
        return;
    }
    // Update card content...
}

function updateActivityCount(delta) {
    const countEl = document.querySelector(".text-white\\/40");
    if (countEl) {
        const current = parseInt(countEl.textContent) || 0;
        const newCount = Math.max(0, current + delta);
        countEl.textContent = newCount + (newCount === 1 ? " activity" : " activities");
    }
}
```

---

## 🏢 Center Owner Workflow

### 1. Center Dashboard (`resources/views/center/dashboard.blade.php`)
**Route:** `GET /center/dashboard`  
**Controller:** `CenterController@dashboard`

**Key Features:**
- Centers grid (each center is clickable/manageable)
- Center stats: active/inactive, activities count, bookings count
- Add center modal
- Edit center modal
- Delete center confirmation
- Map location picker for each center

**Inline JavaScript (from `public/js/center.js`):**

**Add Center Form Submit:**
```javascript
document.getElementById("add-center-form")?.addEventListener("submit", async function (e) {
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
```

**Edit Center Modal:**
```javascript
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

    document.getElementById("edit-center-form").addEventListener("submit", async function (e) {
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
```

**Toggle Center Active:**
```javascript
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
        btn.className = "status-badge " + (data.is_active ? "active" : "inactive");
        btn.textContent = data.is_active ? "Active" : "Inactive";
    }
}
```

**Delete Center:**
```javascript
async function deleteCenter(id) {
    const modalContainer = document.createElement("div");
    modalContainer.id = `delete-confirm-modal-${id}`;
    // ... styled confirmation modal ...
    document.body.appendChild(modalContainer);
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
                if (stat) stat.textContent = Math.max(0, parseInt(stat.textContent || 0) - 1);
                const grid = document.getElementById("centers-grid");
                if (grid && grid.querySelectorAll('[id^="center-card-"]').length === 0) {
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
```

**Grid Update Helpers:**
```javascript
function addNewCenterToGridDirect(center) {
    const grid = document.getElementById("centers-grid");
    if (!grid) return;

    const noResults = grid.querySelector(".no-results");
    if (noResults) noResults.remove();

    const stat = document.querySelectorAll(".stat-number")[0];
    if (stat) stat.textContent = parseInt(stat.textContent || 0) + 1;

    const isActive = center.is_active ?? true;
    const initials = center.name ? center.name.substring(0, 2).toUpperCase() : "CN";

    const card = document.createElement("div");
    card.className = "center-card";
    card.id = `center-card-${center.id}`;
    card.innerHTML = `...center card HTML...`;

    grid.insertBefore(card, grid.firstChild);
    setTimeout(() => card.scrollIntoView({ behavior: "smooth", block: "center" }), 100);
}

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

    const grid = document.getElementById("centers-grid");
    if (grid) {
        grid.insertBefore(card, grid.firstChild);
        setTimeout(() => card.scrollIntoView({ behavior: "smooth", block: "center" }), 100);
    }
}
```

---

### 2. Center Bookings (`resources/views/center/bookings.blade.php`)
**Route:** `GET /center/{center}/bookings`  
**Controller:** `CenterController@bookings`

**Key Features:**
- List of bookings for all activities in the center
- Filter by status (pending, confirmed, completed, cancelled)
- Booking details: user info, activity, date/time, participants, price
- Accept/Reject booking buttons
- Mark as completed

**Expected Columns:**
- User (name, email)
- Activity (title, date)
- Time Slot
- Participants / Seats
- Total Price
- Status
- Actions

---

## 👨‍💼 Admin Dashboard

### 1. Admin Dashboard (`resources/views/admin/dashboard.blade.php`)
**Route:** `GET /admin/`  
**Controller:** `AdminController@dashboard`

**Key Features:**
- Overview stats: total users, centers, activities, bookings
- Recent activity feed
- Charts (users trend, bookings trend, revenue)
- Quick action buttons

---

### 2. Admin Users Management (`resources/views/admin/users.blade.php`)
**Route:** `GET /admin/users`  
**Controller:** `AdminController@users`

**Key Features:**
- Users table with filtering
- Columns: name, email, phone, role, status, join date
- Toggle user active/inactive
- Delete user confirmation modal
- Search/filter by name or email

**Table Actions:**
- Toggle Active/Inactive status via AJAX: `POST /admin/users/{user}/toggle`
- Delete user via AJAX: `DELETE /admin/users/{user}`

---

### 3. Admin Centers Management (`resources/views/admin/centers.blade.php`)
**Route:** `GET /admin/centers`  
**Controller:** `AdminController@centers`

**Key Features:**
- Centers table
- Columns: center name, owner, address, activities count, status
- Toggle center active/inactive
- Delete center
- Mock filtering/search

**Table Actions:**
- Toggle Active/Inactive: `POST /admin/centers/{center}/toggle`
- Delete Center: `DELETE /admin/centers/{center}`

---

### 4. Admin Activities Management (`resources/views/admin/activities.blade.php`)
**Route:** `GET /admin/activities`  
**Controller:** `AdminController@activities`

**Key Features:**
- Activities table
- Columns: title, category, center owner, participants, price, status
- Toggle active/inactive
- Delete activity confirmation modal

**Table Actions:**
- Delete: `DELETE /admin/activity/{activity}/delete`
- Toggle Active: `POST /admin/activity/{activity}/toggle-active`

---

### 5. Admin Bookings & Payments

#### Bookings View (`resources/views/admin/bookings.blade.php`)
**Route:** `GET /admin/bookings`

**Features:**
- Bookings table with status, user, activity, date, price
- Mock filtering

#### Payments View (`resources/views/admin/payments.blade.php`)
**Route:** `GET /admin/payments`

**Features:**
- Transaction history table with user, amount, date, method, status

---

### 6. Admin Reviews Management (`resources/views/admin/reviews.blade.php`)
**Route:** `GET /admin/reviews`  
**Controller:** `AdminController@reviews`

**Key Features:**
- Reviews table with user, activity, rating, text
- Delete review confirmation modal

**Table Actions:**
- Delete: `DELETE /admin/reviews/{review}`

---

### 7. Admin Profile (`resources/views/admin/profile.blade.php`)
**Route:** `GET /admin/profile`

**Features:**
- Admin account info
- Change password form

---

## 💳 Payment & Credits System

### 1. Credits Dashboard (`resources/views/credits/dashboard.blade.php`)
**Route:** `GET /credits/`  
**Controller:** `CreditController@dashboard`

**Key Features:**
- Credit balance display
- Transaction history table
- Purchase credits button link
- No inline JavaScript

---

### 2. Credits Purchase (`resources/views/credits/purchase.blade.php`)
**Route:** `GET /credits/purchase`  
**Controller:** `CreditController@purchase`

**Key Features:**
- Credit packages (e.g., 10 credits, 25 credits, 50 credits)
- Package cards with price and bonus display
- Select package → triggers payment flow

---

### 3. Payment Packages (`resources/views/payment/packages.blade.php`)
**Route:** `GET /payment/packages`  
**Controller:** `PaymentController@showPackages`

**Key Features:**
- Display Stripe-integrated credit packages
- Package selection
- Accept Stripe payments

**Payment Flow:**
1. Select package
2. POST `/payment/create-intent` → Stripe client-secret
3. Stripe.js payment element display
4. POST `/payment/confirm` → process payment
5. POST `/webhook/stripe` → webhook confirmation

---

## 🎮 JavaScript Modules

### `public/js/helpers.js`

**Modal Helpers:**
```javascript
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
```

**AJAX Helper:**
```javascript
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
```

**Form Error Display:**
```javascript
function clearErrors(formId) {
    document.querySelectorAll(`#${formId} .error-msg`).forEach(el => el.textContent = '');
    document.querySelectorAll(`#${formId} .input-error`).forEach(el => el.classList.remove('input-error'));
}

function showErrors(formId, errors) {
    for (const [field, messages] of Object.entries(errors)) {
        // Handle nested schedule errors (e.g., schedules.0.day_of_week)
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

        // Normal field errors
        const errEl = document.getElementById(`${formId}-err-${field}`);
        const input = document.querySelector(`#${formId} [name="${field}"]`);
        if (errEl) errEl.textContent = messages[0];
        if (input) input.classList.add('input-error');
    }

    const firstError = document.querySelector(`#${formId} .input-error`);
    if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
```

**Success Toast:**
```javascript
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
```

**Center Map (Leaflet):**
```javascript
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
```

**Favorite Toggle:**
```javascript
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
                const grid = document.querySelector('.grid.grid-cols-2');
                if (grid && grid.querySelectorAll('.fav-card').length === 0) {
                    grid.outerHTML = `
                        <div class="no-results">
                            <div class="mb-3" style="...">
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
```

---

### `public/js/center.js`
**Complete code already listed in Center Owner Workflow section above.**

---

### `public/js/activity.js`
**Complete code already listed in Activity Management section above.**

---

### `public/js/activio-home.js`
```javascript
// Home page JS placeholder.
// We keep this file for future interactivity (e.g., search suggestions, AI parsing UX).
```

---

## 🔧 Controllers Reference

### ContactController
```
GET  /contact            → index()    → resources/views/contact.blade.php
POST /contact            → store()    → create contact in DB
```

### CenterController
```
GET  /for-centers                  → index()           → for-centers page view
POST /center-register              → store()           → create center
GET  /center/dashboard             → dashboard()       → center owner dashboard
GET  /center/{center}/bookings     → bookings()        → center bookings list
POST /center/{center}/toggle-active → toggleActive()   → toggle center status
POST /center/{center}/update       → update()          → update center details
DELETE /center/{center}/delete     → destroy()         → delete center
```

### BookingController
```
POST /schedule/{schedule}/book        → store()        → create booking
POST /booking/{booking}/status        → updateStatus() → change booking status
DELETE /booking/{booking}/delete      → destroy()      → cancel/delete booking
```

### FavouriteController
```
POST /activity/{activity}/favourite   → toggle()       → add/remove favorite
```

### ReviewController
```
POST /booking/{booking}/review        → store()        → create/store review
```

### ActivityController
```
GET  /activities                       → index()        → activities grid
GET  /activities/{activity}            → show()         → activity detail
GET  /center/{center}/activities       → activities()   → center owner's activities
POST /center/{center}/activities       → store()        → create activity
POST /activity/{activity}/update       → update()       → update activity
DELETE /activity/{activity}/delete     → destroy()      → delete activity
POST /activity/{activity}/toggle-active → toggleActive() → toggle activity status
```

### AdminController
```
GET    /admin/                           → dashboard()      → admin dashboard
GET    /admin/users                      → users()          → users management
POST   /admin/users/{user}/toggle        → toggleUser()     → toggle user status
DELETE /admin/users/{user}               → destroyUser()    → delete user
GET    /admin/centers                    → centers()        → centers management
POST   /admin/centers/{center}/toggle    → toggleCenter()   → toggle center status
DELETE /admin/centers/{center}           → destroyCenter()  → delete center
GET    /admin/activities                 → activities()     → activities management
DELETE /admin/activity/{activity}/delete → destroy()        → delete activity
POST   /admin/activity/{activity}/toggle-active → toggleActive() → toggle status
GET    /admin/bookings                   → bookings()       → bookings list
GET    /admin/payments                   → payments()       → payments/transactions
GET    /admin/reviews                    → reviews()        → reviews management
DELETE /admin/reviews/{review}           → destroyReview()  → delete review
GET    /admin/profile                    → profile()        → admin profile
```

### ProfileController
```
GET  /profile                → index()          → user profile page
POST /profile/update         → update()         → update profile
POST /profile/password       → updatePassword() → change password
```

### CreditController
```
GET /credits/              → dashboard() → credits dashboard
GET /credits/purchase      → purchase()  → purchase page
GET /credits/api/stats     → getStats()  → get credit stats (JSON)
```

### PaymentController
```
GET  /payment/packages         → showPackages()        → payment packages
POST /payment/create-intent    → createPaymentIntent() → create Stripe intent
POST /payment/confirm          → confirmPayment()      → confirm payment
POST /webhook/stripe           → webhook()             → Stripe webhook
```

### SearchController
```
GET  /search   → index()  → search results page
POST /search   → search() → AI search query
```

### HomeController
```
GET  /              → index()        → home page
POST /save-location → saveLocation() → save user geolocation
```

---

## 📋 Summary

**Total Routes:** 70+  
**Total Views:** 25+  
**Total Controllers:** 12  
**Total JavaScript Modules:** 4 (helpers, center, activity, home)  
**External Libraries:**
- Laravel 11
- Blade templating
- Tailwind CSS
- Leaflet.js (maps)
- Stripe.js (payments)
- Vanilla JavaScript (no jQuery/React)

**Key Workflows:**
1. User discovers activities → Books activity → Leaves review
2. Center owner registers → Creates activities → Manages bookings
3. Admin manages users, centers, activities, payments, reviews
4. Credits system for booking prepayment
5. Stripe payment integration for credit purchases

---

**End of Complete App Mapping**
