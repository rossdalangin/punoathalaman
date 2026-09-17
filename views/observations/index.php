<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <h1 class="hero-title">Field Observation & Biodiversity Documentation</h1>
    <p class="hero-subtitle">Record and document plant occurrences for foresters, researchers, community science, and conservation monitoring.</p>
</section>

<div class="questionnaire-card">
    <h3 class="section-title">New Plant Observation Record</h3>
    <form id="observation-form">
        <div class="grid-2">
            <div class="form-group">
                <label>Observer Name:</label>
                <input type="text" name="observer_name" class="form-control" placeholder="e.g. Forester Juan dela Cruz" required>
            </div>
            <div class="form-group">
                <label>Observation Date:</label>
                <input type="date" name="observation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Province:*</label>
                <input type="text" name="province" class="form-control" placeholder="e.g. Laguna" required>
            </div>
            <div class="form-group">
                <label>Municipality / City:*</label>
                <input type="text" name="municipality" class="form-control" placeholder="e.g. Los Baños" required>
            </div>
            <div class="form-group">
                <label>Barangay:</label>
                <input type="text" name="barangay" class="form-control" placeholder="e.g. Batong Malake">
            </div>
            <div class="form-group">
                <label>Habitat Description:</label>
                <select name="habitat" class="form-control">
                    <option value="Lowland Forest">Lowland Dipterocarp Forest</option>
                    <option value="Riparian / Riverbank">Riparian / Riverbank</option>
                    <option value="Coastal / Beach">Coastal / Beach Forest</option>
                    <option value="Montane Forest">Montane / Ridge Forest</option>
                    <option value="Agricultural / Farm">Agricultural / Farm Field</option>
                    <option value="Urban Park / Garden">Urban Park / Garden</option>
                </select>
            </div>
            <div class="form-group">
                <label>GPS Latitude (e.g. 14.1648):</label>
                <input type="number" step="any" name="latitude" class="form-control" placeholder="14.1648">
            </div>
            <div class="form-group">
                <label>GPS Longitude (e.g. 121.2413):</label>
                <input type="number" step="any" name="longitude" class="form-control" placeholder="121.2413">
            </div>
            <div class="form-group">
                <label>Plant Height (meters):</label>
                <input type="number" step="0.1" name="plant_height_m" class="form-control" placeholder="e.g. 12.5">
            </div>
            <div class="form-group">
                <label>Diameter at Breast Height DBH (cm):</label>
                <input type="number" step="0.1" name="estimated_dbh_cm" class="form-control" placeholder="e.g. 35.0">
            </div>
        </div>

        <div class="form-group">
            <label>Field Notes & Additional Observations:</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Describe flowering stage, fruiting stage, associated wildlife, threats..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">💾 Save Field Observation Record</button>
    </form>
</div>

<div class="hero-card" style="margin-top: 24px; text-align: left;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:16px;">
        <h3 class="section-title" style="margin-bottom:0;">Recent Field Observations</h3>
        <div>
            <a href="/api/observations/export?format=csv" class="btn btn-outline" style="font-size:0.85rem;">📥 Export CSV</a>
            <a href="/api/observations/export?format=json" class="btn btn-outline" style="font-size:0.85rem;">📥 Export JSON</a>
        </div>
    </div>

    <div id="observations-list">
        <p>Loading records...</p>
    </div>
</div>

<script>
document.getElementById('observation-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);

    try {
        const res = await fetch('/api/observations', { method: 'POST', body: fd });
        const json = await res.json();
        if (json.success) {
            alert(json.message);
            this.reset();
            loadObservations();
        } else {
            alert('Error: ' + json.error);
        }
    } catch (err) {
        alert('Failed to save observation: ' + err.message);
    }
});

async function loadObservations() {
    try {
        const res = await fetch('/api/observations');
        const data = await res.json();
        const listContainer = document.getElementById('observations-list');

        if (!data.observations || data.observations.length === 0) {
            listContainer.innerHTML = '<p>No observation records found yet.</p>';
            return;
        }

        let html = '<div style="display:flex; flex-direction:column; gap:12px;">';
        data.observations.forEach(obs => {
            html += `
                <div class="fact-box">
                    <div style="display:flex; justify-content:space-between;">
                        <strong>${obs.observation_code} - ${obs.observer_name}</strong>
                        <span style="font-size:0.85rem; color:#666;">${obs.observation_date}</span>
                    </div>
                    <p style="margin:4px 0;">📍 <strong>Location:</strong> ${obs.municipality}, ${obs.province} ${obs.barangay ? '('+obs.barangay+')' : ''}</p>
                    <p style="margin:4px 0;">🌲 <strong>Habitat:</strong> ${obs.habitat || 'N/A'} ${obs.plant_height_m ? '| Height: ' + obs.plant_height_m + 'm' : ''}</p>
                    ${obs.notes ? `<p style="font-style:italic; font-size:0.9rem; color:#444;">"${obs.notes}"</p>` : ''}
                </div>
            `;
        });
        html += '</div>';
        listContainer.innerHTML = html;
    } catch (e) {
        console.error(e);
    }
}

document.addEventListener('DOMContentLoaded', loadObservations);
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
