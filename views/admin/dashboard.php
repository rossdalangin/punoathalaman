<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 class="hero-title" style="margin-bottom:4px;">Control Center & Expert Review Dashboard</h1>
            <p class="hero-subtitle" style="margin-bottom:0;">Manage Philippine flora database, review pending user identifications, and monitor system metrics.</p>
        </div>
        <div id="admin-auth-header" style="display:none;">
            <button type="button" onclick="logoutAdmin()" class="btn btn-outline btn-sm">🔒 Logout Admin</button>
        </div>
    </div>
</section>

<!-- Login Box if unauthenticated -->
<div id="admin-login-card" class="hero-card" style="max-width: 420px; margin: 0 auto;">
    <h3 class="hero-title" style="font-size: 1.4rem; text-align: center;">Administrator Authentication</h3>
    <p style="text-align:center; font-size:0.88rem; color:#555; margin-bottom:20px;">Sign in to access species curation and review tools.</p>
    <form id="login-form">
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" class="form-control" value="admin@punoathalaman.ph" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" value="AdminSecret123!" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Sign In to Dashboard</button>
    </form>
</div>

<!-- Main Admin Panel (Hidden until login) -->
<div id="admin-panel" style="display: none;">

    <!-- Key System Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon">🌿</div>
            <div>
                <div class="metric-val" id="stat-plant-count">0</div>
                <div class="metric-label">Registered Flora Species</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">⏳</div>
            <div>
                <div class="metric-val" id="stat-review-count" style="color:var(--warning);">0</div>
                <div class="metric-label">Pending Expert Reviews</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">📍</div>
            <div>
                <div class="metric-val" id="stat-obs-count">0</div>
                <div class="metric-label">Field Observation Logs</div>
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-icon">📚</div>
            <div>
                <div class="metric-val" id="stat-sources-count">0</div>
                <div class="metric-label">Scientific Reference Sources</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="admin-tabs">
        <button type="button" class="tab-btn active" onclick="switchAdminTab('tab-species', this)">🌱 Species Directory</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-add-plant', this)">➕ Add New Species</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-reviews', this)">👨‍🌾 Expert Review Queue</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-sources', this)">📚 Sources & References</button>
    </div>

    <!-- TAB 1: Species Directory -->
    <div id="tab-species" class="tab-content active">
        <div class="hero-card" style="padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
                <h3 class="section-title" style="margin-bottom:0;">Registered Species Directory</h3>
                <input type="text" id="species-search" class="form-control" style="width:250px;" placeholder="Search species..." onkeyup="filterSpeciesTable()">
            </div>
            <div class="table-responsive">
                <table class="data-table" id="species-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Common Name</th>
                            <th>Scientific Name</th>
                            <th>Family</th>
                            <th>Native Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="species-table-body">
                        <tr><td colspan="6" style="text-align:center;">Loading species directory...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: Add New Species -->
    <div id="tab-add-plant" class="tab-content">
        <div class="hero-card">
            <h3 class="section-title">Add New Philippine Plant Species</h3>
            <form id="add-plant-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Scientific Name:*</label>
                        <input type="text" name="scientific_name" class="form-control" placeholder="e.g. Carmona retusa" required>
                    </div>
                    <div class="form-group">
                        <label>Primary Common Name:*</label>
                        <input type="text" name="primary_common_name" class="form-control" placeholder="e.g. Tsaang Gubat" required>
                    </div>
                    <div class="form-group">
                        <label>Family:*</label>
                        <input type="text" name="family" class="form-control" placeholder="e.g. Boraginaceae" required>
                    </div>
                    <div class="form-group">
                        <label>Genus:</label>
                        <input type="text" name="genus" class="form-control" placeholder="e.g. Carmona">
                    </div>
                    <div class="form-group">
                        <label>Species:</label>
                        <input type="text" name="species" class="form-control" placeholder="e.g. retusa">
                    </div>
                    <div class="form-group">
                        <label>Native Status:</label>
                        <select name="native_status" class="form-control">
                            <option value="NATIVE">NATIVE</option>
                            <option value="ENDEMIC">ENDEMIC</option>
                            <option value="INTRODUCED">INTRODUCED</option>
                            <option value="INVASIVE">INVASIVE</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Habitat Description:</label>
                    <textarea name="habitat" class="form-control" rows="2" placeholder="Lowland forests, secondary thickets..."></textarea>
                </div>
                <div class="form-group">
                    <label>Philippine Regional Distribution:</label>
                    <textarea name="philippine_distribution" class="form-control" rows="2" placeholder="Widespread across Luzon, Visayas, Mindanao..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">➕ Save Species Record</button>
            </form>
        </div>
    </div>

    <!-- TAB 3: Expert Review Queue -->
    <div id="tab-reviews" class="tab-content">
        <div class="hero-card" style="padding:20px;">
            <h3 class="section-title">Pending Expert Review Queue</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ident ID</th>
                            <th>Request Date</th>
                            <th>Confidence</th>
                            <th>Primary Candidate</th>
                            <th>User Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="reviews-table-body">
                        <tr><td colspan="7" style="text-align:center;">Loading expert review queue...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: Sources & References -->
    <div id="tab-sources" class="tab-content">
        <div class="hero-card" style="padding:20px;">
            <h3 class="section-title">Authoritative Sources & Citations</h3>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Source Name</th>
                            <th>Type</th>
                            <th>Plant Species</th>
                            <th>URL Link</th>
                        </tr>
                    </thead>
                    <tbody id="sources-table-body">
                        <tr><td colspan="4" style="text-align:center;">Loading reference sources...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
function getApiUrl(endpoint) {
    return 'index.php?r=' + endpoint.replace(/^\/+/, '');
}

function switchAdminTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);

    try {
        const res = await fetch(getApiUrl('api/auth/login'), { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            showAdminPanel();
        } else {
            alert('Login failed: ' + (json.error || 'Invalid credentials'));
        }
    } catch (err) {
        alert('Authentication error: ' + err.message);
    }
});

function showAdminPanel() {
    document.getElementById('admin-login-card').style.display = 'none';
    document.getElementById('admin-panel').style.display = 'block';
    document.getElementById('admin-auth-header').style.display = 'block';
    loadDashboardStats();
    loadPlantsDirectory();
    loadExpertReviews();
    loadSources();
}

async function logoutAdmin() {
    await fetch(getApiUrl('api/auth/logout'), { method: 'POST' });
    document.getElementById('admin-login-card').style.display = 'block';
    document.getElementById('admin-panel').style.display = 'none';
    document.getElementById('admin-auth-header').style.display = 'none';
}

async function loadDashboardStats() {
    try {
        const res = await fetch(getApiUrl('api/admin/stats'));
        const data = await res.json();
        if (data.stats) {
            document.getElementById('stat-plant-count').innerText = data.stats.total_plants || 0;
            document.getElementById('stat-review-count').innerText = data.stats.pending_reviews || 0;
            document.getElementById('stat-obs-count').innerText = data.stats.total_observations || 0;
            document.getElementById('stat-sources-count').innerText = data.stats.total_sources || 0;
        }
    } catch (e) {
        console.error(e);
    }
}

async function loadPlantsDirectory() {
    try {
        const res = await fetch(getApiUrl('api/plants'));
        const data = await res.json();
        const tbody = document.getElementById('species-table-body');

        if (!data.plants || data.plants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No plant species in database.</td></tr>';
            return;
        }

        let html = '';
        data.plants.forEach(p => {
            html += `
                <tr>
                    <td>#${p.id}</td>
                    <td><strong>${p.primary_common_name}</strong></td>
                    <td><em>${p.scientific_name}</em></td>
                    <td>${p.family}</td>
                    <td><span class="badge badge-success">${p.native_status}</span></td>
                    <td>
                        <button class="btn btn-outline btn-sm" onclick="alert('Viewing record #${p.id}')">🔍 View Details</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error(e);
    }
}

function filterSpeciesTable() {
    const query = document.getElementById('species-search').value.toLowerCase();
    const rows = document.querySelectorAll('#species-table-body tr');
    rows.forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
}

async function loadExpertReviews() {
    try {
        const res = await fetch(getApiUrl('api/admin/reviews'));
        const data = await res.json();
        const tbody = document.getElementById('reviews-table-body');

        if (!data.reviews || data.reviews.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">No pending expert reviews in queue.</td></tr>';
            return;
        }

        let html = '';
        data.reviews.forEach(r => {
            html += `
                <tr>
                    <td>#${r.id}</td>
                    <td>${r.created_at}</td>
                    <td><span class="badge badge-warning">${r.confidence_level} (${r.confidence_score}%)</span></td>
                    <td><em>${r.scientific_name || 'Uncertain Specimen'}</em></td>
                    <td>${r.expert_notes || 'Requested verification'}</td>
                    <td><span class="badge badge-danger">${r.status}</span></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="verifyReview(${r.id})">✅ Verify Species</button>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error(e);
    }
}

async function verifyReview(id) {
    if (!confirm('Mark this plant identification request as VERIFIED?')) return;
    const fd = new FormData();
    fd.append('identification_id', id);

    const res = await fetch(getApiUrl('api/admin/reviews/verify'), { method: 'POST', body: fd });
    const json = await res.json();
    alert(json.message || json.error);
    loadExpertReviews();
    loadDashboardStats();
}

async function loadSources() {
    try {
        const res = await fetch(getApiUrl('api/sources'));
        const data = await res.json();
        const tbody = document.getElementById('sources-table-body');

        if (!data.sources || data.sources.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No reference sources logged.</td></tr>';
            return;
        }

        let html = '';
        data.sources.forEach(s => {
            html += `
                <tr>
                    <td><strong>${s.source_name}</strong></td>
                    <td><span class="badge badge-info">${s.source_type}</span></td>
                    <td>${s.primary_common_name ? s.primary_common_name + ' (<em>' + s.scientific_name + '</em>)' : 'General Botanical'}</td>
                    <td>${s.source_url ? '<a href="' + s.source_url + '" target="_blank" class="btn btn-outline btn-sm">🔗 Open Reference</a>' : 'N/A'}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (e) {
        console.error(e);
    }
}

document.getElementById('add-plant-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);

    try {
        const res = await fetch(getApiUrl('api/admin/plants'), { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            alert(json.message);
            this.reset();
            loadPlantsDirectory();
            loadDashboardStats();
        } else {
            alert('Error: ' + json.error);
        }
    } catch (err) {
        alert('Failed to save plant: ' + err.message);
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
