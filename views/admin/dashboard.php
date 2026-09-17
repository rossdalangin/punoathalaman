<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 class="hero-title" style="margin-bottom:4px;">Control Center & Expert Review Dashboard</h1>
            <p class="hero-subtitle" style="margin-bottom:0;">Manage Philippine flora database, edit medicinal and safety attributes, curate images, review pending identifications, and configure system AI parameters.</p>
        </div>
        <div id="admin-auth-header" style="display:none;">
            <button type="button" onclick="logoutAdmin()" class="btn btn-outline btn-sm">🔒 Logout Admin</button>
        </div>
    </div>
</section>

<!-- Login Box if unauthenticated -->
<div id="admin-login-card" class="hero-card" style="max-width: 420px; margin: 0 auto;">
    <h3 class="hero-title" style="font-size: 1.4rem; text-align: center;">Administrator Authentication</h3>
    <p style="text-align:center; font-size:0.88rem; color:#555; margin-bottom:20px;">Sign in to access species curation, settings, and review tools.</p>
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
            <div class="metric-icon">⚙️</div>
            <div>
                <div class="metric-val" id="stat-active-provider" style="font-size:1.2rem; text-transform:uppercase;">MOCK</div>
                <div class="metric-label">Active AI Provider</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="admin-tabs">
        <button type="button" class="tab-btn active" onclick="switchAdminTab('tab-species', this)">🌱 Species Directory & Management</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-add-plant', this)">➕ Add New Species</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-reviews', this)">👨‍🌾 Expert Review Queue</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-settings', this)">⚙️ System Settings</button>
        <button type="button" class="tab-btn" onclick="switchAdminTab('tab-sources', this)">📚 Sources & References</button>
    </div>

    <!-- TAB 1: Species Directory & Management -->
    <div id="tab-species" class="tab-content active">
        <div class="hero-card" style="padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
                <h3 class="section-title" style="margin-bottom:0;">Registered Species Directory</h3>
                <div style="display:flex; gap:8px;">
                    <input type="text" id="species-search" class="form-control" style="width:250px;" placeholder="Search common/scientific/alias..." onkeyup="filterSpeciesTable()">
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table" id="species-table">
                    <thead>
                        <tr>
                            <th>Image</th>
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
                            <option value="NATURALIZED">NATURALIZED</option>
                            <option value="INVASIVE">INVASIVE</option>
                            <option value="CULTIVATED">CULTIVATED</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Default Image Relative Path / URL:</label>
                    <input type="text" name="image_path" class="form-control" placeholder="e.g. assets/images/species/tsaang_gubat.jpg">
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

    <!-- TAB 4: System Settings -->
    <div id="tab-settings" class="tab-content">
        <div class="hero-card">
            <h3 class="section-title">⚙️ System Configuration & AI Settings</h3>
            <form id="settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Active AI Provider:</label>
                        <select id="setting-ai-provider" name="ai_provider" class="form-control">
                            <option value="gemini">Google Gemini AI (gemini-2.0-flash / gemini-1.5-flash Free Tier)</option>
                            <option value="openai">OpenAI (GPT-4o Vision API)</option>
                            <option value="mock">Mock Offline Provider (Testing / Zero Key)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Active AI Model Name:</label>
                        <input type="text" id="setting-ai-model" name="ai_model" class="form-control" value="gemini-2.0-flash">
                    </div>
                </div>

                <div class="form-group">
                    <label>AI API Key:</label>
                    <input type="text" id="setting-ai-key" name="ai_api_key" class="form-control" placeholder="Paste OpenAI or Google Gemini API Key">
                </div>

                <hr style="margin: 20px 0; border:0; border-top:1px solid var(--border);">

                <div class="grid-2">
                    <div class="form-group">
                        <label>Max Image Upload Size (MB):</label>
                        <input type="number" id="setting-max-upload" name="max_upload_size_mb" class="form-control" value="10">
                    </div>
                    <div class="form-group">
                        <label>Field Observations Public Submission:</label>
                        <select id="setting-public-obs" name="public_observations" class="form-control">
                            <option value="enabled">Enabled (Open to Public Logging)</option>
                            <option value="disabled">Disabled (Admin & Experts Only)</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:20px;">
                    <button type="submit" class="btn btn-primary">💾 Save System Settings</button>
                    <button type="button" class="btn btn-warning" onclick="reseedFloraDatabase()">🌱 Re-Seed Philippine Flora Database</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 5: Sources & References -->
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

<!-- EDIT SPECIES MODAL DRAWER -->
<div id="edit-plant-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; overflow-y:auto; padding:20px;">
    <div style="background:#fff; max-width:900px; margin:40px auto; padding:25px; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.3); position:relative;">
        <button type="button" onclick="closeEditModal()" style="position:absolute; top:15px; right:20px; border:none; background:none; font-size:1.5rem; cursor:pointer;">✖</button>
        <h2 style="color:var(--primary-dark); margin-bottom:16px;">🌿 Edit Species Profile: <span id="modal-species-title"></span></h2>

        <!-- Modal Inner Tabs -->
        <div class="admin-tabs" style="margin-bottom:15px;">
            <button type="button" class="tab-btn active" onclick="switchModalTab('modal-tab-general', this)">Taxonomy & Habitat</button>
            <button type="button" class="tab-btn" onclick="switchModalTab('modal-tab-medicinal', this)">Medicinal Properties</button>
            <button type="button" class="tab-btn" onclick="switchModalTab('modal-tab-safety', this)">Safety & Warnings</button>
            <button type="button" class="tab-btn" onclick="switchModalTab('modal-tab-aliases', this)">Aliases / Local Names</button>
            <button type="button" class="tab-btn" onclick="switchModalTab('modal-tab-images', this)">Reference Images</button>
        </div>

        <form id="edit-plant-form">
            <input type="hidden" id="edit-plant-id" name="plant_id">

            <!-- Modal Tab 1: Taxonomy -->
            <div id="modal-tab-general" class="modal-subtab active">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Scientific Name:</label>
                        <input type="text" id="edit-scientific-name" name="scientific_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Primary Common Name:</label>
                        <input type="text" id="edit-common-name" name="primary_common_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Family:</label>
                        <input type="text" id="edit-family" name="family" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Genus:</label>
                        <input type="text" id="edit-genus" name="genus" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Species:</label>
                        <input type="text" id="edit-species" name="species" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Native Status:</label>
                        <select id="edit-native-status" name="native_status" class="form-control">
                            <option value="NATIVE">NATIVE</option>
                            <option value="ENDEMIC">ENDEMIC</option>
                            <option value="INTRODUCED">INTRODUCED</option>
                            <option value="NATURALIZED">NATURALIZED</option>
                            <option value="INVASIVE">INVASIVE</option>
                            <option value="CULTIVATED">CULTIVATED</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Habitat Description:</label>
                    <textarea id="edit-habitat" name="habitat" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Philippine Regional Distribution:</label>
                    <textarea id="edit-distribution" name="philippine_distribution" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <!-- Modal Tab 2: Medicinal -->
            <div id="modal-tab-medicinal" class="modal-subtab" style="display:none;">
                <div class="form-group">
                    <label>Recognized Medicinal Status:</label>
                    <select id="edit-med-status" class="form-control">
                        <option value="YES">YES (DOH / PITAHC Recognized)</option>
                        <option value="TRADITIONALLY_USED">TRADITIONALLY_USED</option>
                        <option value="POTENTIAL">POTENTIAL / UNDER RESEARCH</option>
                        <option value="NO_RELIABLE_USE">NO RELIABLE MEDICINAL USE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Traditional Ethnobotanical Uses:</label>
                    <textarea id="edit-med-traditional" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Scientifically Established Evidence:</label>
                    <textarea id="edit-med-scientific" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Known Active Phytochemical Compounds:</label>
                    <input type="text" id="edit-med-compounds" class="form-control" placeholder="e.g. Corosolic acid, quercetin, tannins">
                </div>
                <div class="form-group">
                    <label>Known Risks & Preparation Precautions:</label>
                    <input type="text" id="edit-med-risks" class="form-control">
                </div>
            </div>

            <!-- Modal Tab 3: Safety -->
            <div id="modal-tab-safety" class="modal-subtab" style="display:none;">
                <div class="form-group">
                    <label>Safety Classification Category:</label>
                    <select id="edit-safety-category" class="form-control">
                        <option value="SAFE_FOR_GENERAL_CONTACT">SAFE FOR GENERAL CONTACT</option>
                        <option value="CAUTION">CAUTION</option>
                        <option value="POTENTIALLY_TOXIC">POTENTIALLY TOXIC</option>
                        <option value="KNOWN_POISONOUS_PLANT">KNOWN POISONOUS PLANT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Primary Safety Warning Banner:</label>
                    <textarea id="edit-safety-warning" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Toxic Parts (if applicable):</label>
                    <input type="text" id="edit-safety-toxic" class="form-control" placeholder="e.g. Seeds, concentrated sap">
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Look-alike Species Name:</label>
                        <input type="text" id="edit-safety-lookalike" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Look-alike Diagnostic Distinction:</label>
                        <input type="text" id="edit-safety-distinction" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Modal Tab 4: Aliases -->
            <div id="modal-tab-aliases" class="modal-subtab" style="display:none;">
                <div id="aliases-list-container" style="margin-bottom:15px;"></div>
                <div style="background:#f4f8f5; padding:15px; border-radius:8px;">
                    <h4 style="margin-top:0; font-size:0.95rem;">➕ Add Regional / Dialect Alias Name</h4>
                    <div class="grid-2">
                        <input type="text" id="new-alias-name" class="form-control" placeholder="Alias Name (e.g. Subusub)">
                        <select id="new-alias-type" class="form-control">
                            <option value="tagalog">Tagalog</option>
                            <option value="english">English</option>
                            <option value="local">Local Dialect</option>
                            <option value="regional">Regional Name</option>
                        </select>
                    </div>
                    <div style="margin-top:8px; display:flex; gap:10px;">
                        <input type="text" id="new-alias-region" class="form-control" placeholder="Region / Language (e.g. Ilocos, Visayas)">
                        <button type="button" class="btn btn-outline btn-sm" onclick="addAliasRecord()">Add Alias</button>
                    </div>
                </div>
            </div>

            <!-- Modal Tab 5: Images -->
            <div id="modal-tab-images" class="modal-subtab" style="display:none;">
                <div id="images-list-container" style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:15px;"></div>
                <div style="background:#f4f8f5; padding:15px; border-radius:8px;">
                    <h4 style="margin-top:0; font-size:0.95rem;">🖼️ Attach Reference Image URL / Path</h4>
                    <div class="grid-2">
                        <input type="text" id="new-image-path" class="form-control" placeholder="Path/URL e.g. assets/images/species/bayabas.jpg">
                        <select id="new-image-type" class="form-control">
                            <option value="leaf">Leaf</option>
                            <option value="flower">Flower</option>
                            <option value="fruit">Fruit</option>
                            <option value="bark">Bark</option>
                            <option value="whole_plant">Whole Plant</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" style="margin-top:10px;" onclick="addImageRecord()">Attach Image</button>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:25px; padding-top:15px; border-top:1px solid #eee;">
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSpeciesProfile()">🗑️ Delete Species</button>
                <div style="display:flex; gap:10px;">
                    <button type="button" class="btn btn-outline" onclick="closeEditModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSpeciesProfile()">💾 Save All Changes</button>
                </div>
            </div>
        </form>
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

function switchModalTab(tabId, btn) {
    document.querySelectorAll('.modal-subtab').forEach(t => t.style.display = 'none');
    document.querySelectorAll('#edit-plant-modal .tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
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
    loadAdminSettings();
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
            document.getElementById('stat-active-provider').innerText = data.stats.active_provider || 'GEMINI';
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
            const imgThumb = p.main_image ? `<img src="${p.main_image}" style="width:40px; height:40px; object-fit:cover; border-radius:6px;">` : '🌿';
            html += `
                <tr>
                    <td>${imgThumb}</td>
                    <td><strong>${p.primary_common_name}</strong></td>
                    <td><em>${p.scientific_name}</em></td>
                    <td>${p.family}</td>
                    <td><span class="badge badge-success">${p.native_status}</span></td>
                    <td>
                        <button class="btn btn-outline btn-sm" onclick="openEditModal(${p.id})">✏️ Edit Profile</button>
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

async function openEditModal(plantId) {
    try {
        const res = await fetch(getApiUrl('api/plants/' + plantId));
        const json = await res.json();
        if (!json.plant) {
            alert('Species record not found');
            return;
        }

        const p = json.plant;
        document.getElementById('edit-plant-id').value = p.id;
        document.getElementById('modal-species-title').innerText = p.primary_common_name + ' (' + p.scientific_name + ')';

        // General Taxonomy
        document.getElementById('edit-scientific-name').value = p.scientific_name || '';
        document.getElementById('edit-common-name').value = p.primary_common_name || '';
        document.getElementById('edit-family').value = p.family || '';
        document.getElementById('edit-genus').value = p.genus || '';
        document.getElementById('edit-species').value = p.species || '';
        document.getElementById('edit-native-status').value = p.native_status || 'NATIVE';
        document.getElementById('edit-habitat').value = p.habitat || '';
        document.getElementById('edit-distribution').value = p.philippine_distribution || '';

        // Medicinal
        if (p.medicinal) {
            document.getElementById('edit-med-status').value = p.medicinal.is_recognized_medicinal || 'TRADITIONALLY_USED';
            document.getElementById('edit-med-traditional').value = p.medicinal.traditional_uses_text || '';
            document.getElementById('edit-med-scientific').value = p.medicinal.scientific_evidence_text || '';
            document.getElementById('edit-med-compounds').value = p.medicinal.active_compounds || '';
            document.getElementById('edit-med-risks').value = p.medicinal.known_risks || '';
        }

        // Safety
        if (p.safety) {
            document.getElementById('edit-safety-category').value = p.safety.safety_category || 'SAFE_FOR_GENERAL_CONTACT';
            document.getElementById('edit-safety-warning').value = p.safety.primary_warning || '';
            document.getElementById('edit-safety-toxic').value = p.safety.toxic_parts || '';
            document.getElementById('edit-safety-lookalike').value = p.safety.look_alike_species || '';
            document.getElementById('edit-safety-distinction').value = p.safety.look_alike_distinction || '';
        }

        // Render Aliases
        renderAliasesList(p.names || []);

        // Render Images
        renderImagesList(p.images || []);

        document.getElementById('edit-plant-modal').style.display = 'block';
    } catch (err) {
        alert('Failed to load species profile: ' + err.message);
    }
}

function closeEditModal() {
    document.getElementById('edit-plant-modal').style.display = 'none';
}

function renderAliasesList(names) {
    const container = document.getElementById('aliases-list-container');
    if (!names || names.length === 0) {
        container.innerHTML = '<p style="color:#777; font-size:0.9rem;">No secondary aliases recorded yet.</p>';
        return;
    }
    let html = '<div style="display:flex; flex-wrap:wrap; gap:8px;">';
    names.forEach(n => {
        html += `<span class="badge badge-info" style="display:inline-flex; align-items:center; gap:6px;">
            ${n.name} (${n.language_region || n.name_type})
            <button type="button" onclick="deleteAliasRecord(${n.id})" style="border:none; background:none; color:red; cursor:pointer; font-weight:bold;">×</button>
        </span>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

function renderImagesList(images) {
    const container = document.getElementById('images-list-container');
    if (!images || images.length === 0) {
        container.innerHTML = '<p style="color:#777; font-size:0.9rem;">No reference images attached yet.</p>';
        return;
    }
    let html = '';
    images.forEach(img => {
        html += `<div style="border:1px solid #ddd; padding:8px; border-radius:8px; text-align:center; width:120px;">
            <img src="${img.file_path}" style="width:100px; height:80px; object-fit:cover; border-radius:4px; display:block; margin:0 auto 6px;">
            <span style="font-size:0.75rem; color:#555; text-transform:uppercase;">${img.image_type}</span>
            <button type="button" onclick="deleteImageRecord(${img.id})" class="btn btn-danger btn-sm" style="font-size:0.7rem; padding:2px 6px; margin-top:4px;">Remove</button>
        </div>`;
    });
    container.innerHTML = html;
}

async function saveSpeciesProfile() {
    const plantId = document.getElementById('edit-plant-id').value;
    const payload = {
        scientific_name: document.getElementById('edit-scientific-name').value,
        primary_common_name: document.getElementById('edit-common-name').value,
        family: document.getElementById('edit-family').value,
        genus: document.getElementById('edit-genus').value,
        species: document.getElementById('edit-species').value,
        native_status: document.getElementById('edit-native-status').value,
        habitat: document.getElementById('edit-habitat').value,
        philippine_distribution: document.getElementById('edit-distribution').value,
        medicinal: {
            is_recognized_medicinal: document.getElementById('edit-med-status').value,
            traditional_uses_text: document.getElementById('edit-med-traditional').value,
            scientific_evidence_text: document.getElementById('edit-med-scientific').value,
            active_compounds: document.getElementById('edit-med-compounds').value,
            known_risks: document.getElementById('edit-med-risks').value
        },
        safety: {
            safety_category: document.getElementById('edit-safety-category').value,
            primary_warning: document.getElementById('edit-safety-warning').value,
            toxic_parts: document.getElementById('edit-safety-toxic').value,
            look_alike_species: document.getElementById('edit-safety-lookalike').value,
            look_alike_distinction: document.getElementById('edit-safety-distinction').value
        }
    };

    try {
        const res = await fetch(getApiUrl('api/admin/plants/' + plantId), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
            alert('Species profile updated successfully!');
            closeEditModal();
            loadPlantsDirectory();
        } else {
            alert('Update error: ' + (json.error || 'Failed to save changes'));
        }
    } catch (err) {
        alert('Network error: ' + err.message);
    }
}

async function deleteSpeciesProfile() {
    const plantId = document.getElementById('edit-plant-id').value;
    if (!confirm('Are you sure you want to permanently delete this plant species profile?')) return;

    try {
        const res = await fetch(getApiUrl('api/admin/plants/' + plantId), { method: 'DELETE' });
        const json = await res.json();
        alert(json.message || json.error);
        closeEditModal();
        loadPlantsDirectory();
        loadDashboardStats();
    } catch (err) {
        alert('Delete failed: ' + err.message);
    }
}

async function addAliasRecord() {
    const plantId = document.getElementById('edit-plant-id').value;
    const name = document.getElementById('new-alias-name').value;
    const type = document.getElementById('new-alias-type').value;
    const region = document.getElementById('new-alias-region').value;

    if (!name) { alert('Enter an alias name'); return; }

    const fd = new FormData();
    fd.append('name', name);
    fd.append('name_type', type);
    fd.append('language_region', region);

    const res = await fetch(getApiUrl('api/admin/plants/' + plantId + '/alias'), { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
        document.getElementById('new-alias-name').value = '';
        openEditModal(plantId);
    }
}

async function deleteAliasRecord(aliasId) {
    const plantId = document.getElementById('edit-plant-id').value;
    const res = await fetch(getApiUrl('api/admin/alias/' + aliasId), { method: 'DELETE' });
    const json = await res.json();
    if (json.success) openEditModal(plantId);
}

async function addImageRecord() {
    const plantId = document.getElementById('edit-plant-id').value;
    const path = document.getElementById('new-image-path').value;
    const type = document.getElementById('new-image-type').value;

    if (!path) { alert('Enter image path/URL'); return; }

    const fd = new FormData();
    fd.append('file_path', path);
    fd.append('image_type', type);

    const res = await fetch(getApiUrl('api/admin/plants/' + plantId + '/image'), { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
        document.getElementById('new-image-path').value = '';
        openEditModal(plantId);
    }
}

async function deleteImageRecord(imageId) {
    const plantId = document.getElementById('edit-plant-id').value;
    const res = await fetch(getApiUrl('api/admin/image/' + imageId), { method: 'DELETE' });
    const json = await res.json();
    if (json.success) openEditModal(plantId);
}

async function loadAdminSettings() {
    try {
        const res = await fetch(getApiUrl('api/admin/settings'));
        const data = await res.json();
        if (data.settings) {
            document.getElementById('setting-ai-provider').value = data.settings.ai_provider || 'gemini';
            document.getElementById('setting-ai-model').value = data.settings.ai_model || 'gemini-2.0-flash';
            document.getElementById('setting-ai-key').value = data.settings.ai_api_key || '';
            document.getElementById('setting-max-upload').value = data.settings.max_upload_size_mb || '10';
        }
    } catch (e) {
        console.error(e);
    }
}

document.getElementById('settings-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);

    try {
        const res = await fetch(getApiUrl('api/admin/settings'), { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            alert(json.message);
            loadDashboardStats();
        } else {
            alert('Error: ' + json.error);
        }
    } catch (err) {
        alert('Failed to save settings: ' + err.message);
    }
});

async function reseedFloraDatabase() {
    if (!confirm('Re-seed Philippine Flora Database? This will restore standard botanical species entries.')) return;
    try {
        const res = await fetch(getApiUrl('api/admin/reseed'), { method: 'POST' });
        const json = await res.json();
        alert(json.message || json.error);
        loadPlantsDirectory();
        loadDashboardStats();
    } catch (e) {
        alert('Reseed failed: ' + e.message);
    }
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
