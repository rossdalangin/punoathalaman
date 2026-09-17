<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <h1 class="hero-title">Admin & Expert Review Dashboard</h1>
    <p class="hero-subtitle">Manage plant species database, aliases, sources, safety warnings, and expert review queue.</p>
</section>

<!-- Login Box if not authenticated -->
<div id="admin-login-card" class="questionnaire-card" style="max-width: 450px; margin: 0 auto;">
    <h3 class="section-title">Admin Login</h3>
    <form id="login-form">
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" class="form-control" value="admin@punoathalaman.ph" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" value="AdminSecret123!" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Login to Dashboard</button>
    </form>
</div>

<!-- Main Admin Panel (Hidden until login or if session exists) -->
<div id="admin-panel" style="display: none;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2>System Status & Species Management</h2>
        <button type="button" onclick="logoutAdmin()" class="btn btn-outline">Logout</button>
    </div>

    <!-- Quick Stats -->
    <div class="grid-2" style="margin-bottom: 24px;">
        <div class="fact-box">
            <strong>Registered Plant Species</strong>
            <p id="stat-plant-count" style="font-size: 1.5rem; font-weight:700; color:var(--primary);">6 Species</p>
        </div>
        <div class="fact-box">
            <strong>Pending Expert Reviews</strong>
            <p style="font-size: 1.5rem; font-weight:700; color:var(--warning);">1 Active Request</p>
        </div>
    </div>

    <!-- Add Plant Form -->
    <div class="questionnaire-card" style="margin-bottom:24px;">
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
                <textarea name="habitat" class="form-control" rows="2" placeholder="Secondary forests, thickets..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">➕ Save Plant Species</button>
        </form>
    </div>

    <!-- Plant Database Table -->
    <div class="hero-card" style="text-align: left;">
        <h3 class="section-title">Registered Plants Directory</h3>
        <div id="plants-table-container">
            <p>Loading database directory...</p>
        </div>
    </div>
</div>

<script>
function getApiUrl(endpoint) {
    return 'index.php?r=' + endpoint.replace(/^\/+/, '');
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
            alert('Login failed: ' + (json.error || 'Unknown error'));
        }
    } catch (err) {
        alert('Authentication error: ' + err.message);
    }
});

function showAdminPanel() {
    document.getElementById('admin-login-card').style.display = 'none';
    document.getElementById('admin-panel').style.display = 'block';
    loadPlantsDirectory();
}

async function logoutAdmin() {
    await fetch(getApiUrl('api/auth/logout'), { method: 'POST' });
    document.getElementById('admin-login-card').style.display = 'block';
    document.getElementById('admin-panel').style.display = 'none';
}

async function loadPlantsDirectory() {
    try {
        const res = await fetch(getApiUrl('api/plants'));
        const data = await res.json();
        const container = document.getElementById('plants-table-container');

        if (!data.plants || data.plants.length === 0) {
            container.innerHTML = '<p>No plants in database.</p>';
            return;
        }

        let html = '<div style="display:flex; flex-direction:column; gap:10px;">';
        data.plants.forEach(p => {
            html += `
                <div class="fact-box" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                    <div>
                        <strong>${p.primary_common_name} (<em>${p.scientific_name}</em>)</strong>
                        <p style="font-size:0.85rem; color:#666;">Family: ${p.family} | Status: ${p.native_status}</p>
                    </div>
                    <span class="confidence-badge conf-High" style="font-size:0.75rem;">Verified Record</span>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
        document.getElementById('stat-plant-count').innerText = data.plants.length + ' Species';
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
        } else {
            alert('Error: ' + json.error);
        }
    } catch (err) {
        alert('Failed to save plant: ' + err.message);
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
