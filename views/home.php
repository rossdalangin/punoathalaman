<?php require_once __DIR__ . '/partials/header.php'; ?>

<section class="hero-card">
    <h1 class="hero-title" data-i18n="hero_title">Kilalanin ang Halaman sa Larawan</h1>
    <p class="hero-subtitle" data-i18n="hero_subtitle">Upload a photo or screenshot of a leaf, flower, bark, fruit, or tree to identify Philippine plant species.</p>

    <!-- Upload Input Options -->
    <div class="upload-box-wrapper" id="drop-zone" onclick="document.getElementById('file-input-single').click();">
        <div class="upload-icon">📷 🖼️</div>
        <p id="upload-status-text" data-i18n="drag_drop_text">Drag & drop plant image here or click to browse</p>
        <div id="file-preview-container" style="display:none; margin-top: 15px;">
            <img id="image-preview" src="" alt="Plant Preview" style="max-height: 220px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
        </div>
    </div>

    <!-- Hidden Inputs -->
    <form id="identify-form" enctype="multipart/form-data">
        <input type="file" id="file-input-single" name="image" accept="image/*" style="display: none;" onchange="handleSingleFileSelect(this)">
        <input type="file" id="file-input-multi" name="images[]" accept="image/*" multiple style="display: none;" onchange="handleMultiFileSelect(this)">

        <div class="action-buttons">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('file-input-single').setAttribute('capture', 'environment'); document.getElementById('file-input-single').click();">
                <span data-i18n="btn_take_photo">📷 Take Photo</span>
            </button>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('file-input-single').removeAttribute('capture'); document.getElementById('file-input-single').click();">
                <span data-i18n="btn_upload_image">🖼 Upload Image</span>
            </button>
            <button type="button" class="btn btn-outline" onclick="document.getElementById('file-input-multi').click();">
                <span data-i18n="btn_upload_multi">📁 Upload Multiple Photos</span>
            </button>
        </div>

        <!-- Optional Follow-up Questionnaire -->
        <div class="questionnaire-card" style="margin-top: 24px; text-align: left;">
            <h3 class="section-title" data-i18n="questions_title">Optional Context (Improves Accuracy)</h3>
            <div class="grid-2">
                <div class="form-group">
                    <label data-i18n="q_location">Where was it found?</label>
                    <select name="location_found" class="form-control">
                        <option value="">-- Select Environment --</option>
                        <option value="forest">Forest / Gubat</option>
                        <option value="farm">Farm / Bukid</option>
                        <option value="garden">Garden / Bakuran</option>
                        <option value="roadside">Roadside / Tabi ng kalsada</option>
                        <option value="mountain">Mountain / Bundok</option>
                        <option value="coastal">Coastal / Dalampasigan</option>
                        <option value="urban">Urban Area / Lungsod</option>
                    </select>
                </div>
                <div class="form-group">
                    <label data-i18n="q_province">Province / Location:</label>
                    <input type="text" name="province" class="form-control" placeholder="e.g. Laguna, Quezon, Cebu">
                </div>
                <div class="form-group">
                    <label data-i18n="q_flowers">Does it have visible flowers?</label>
                    <select name="has_flowers" class="form-control">
                        <option value="">-- Unknown --</option>
                        <option value="yes">Yes / May bulaklak</option>
                        <option value="no">No / Walang bulaklak</option>
                    </select>
                </div>
                <div class="form-group">
                    <label data-i18n="q_sap">Does it produce white/milky sap?</label>
                    <select name="milky_sap" class="form-control">
                        <option value="">-- Unknown --</option>
                        <option value="yes">Yes / May puting gatas</option>
                        <option value="no">No / Walang gatas</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" id="btn-submit-id" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 1.1rem;" disabled>
            <span data-i18n="btn_identify">Suriin at Kilalanin ang Halaman</span>
        </button>
    </form>
</section>

<!-- Loading Spinner -->
<div id="loading-spinner" style="display: none; text-align: center; padding: 40px 0;">
    <div class="spinner"></div>
    <p style="font-weight: 600; color: var(--primary);" data-i18n="analyzing_text">Analyzing image with AI Vision & Philippine Biodiversity Database...</p>
</div>

<!-- Container for Result Card -->
<div id="result-container" style="display: none;"></div>

<script>
let selectedMultiFiles = [];

function handleSingleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('file-preview-container').style.display = 'block';
            document.getElementById('upload-status-text').innerText = "Selected: " + file.name;
            document.getElementById('btn-submit-id').disabled = false;
        };
        reader.readAsDataURL(file);
    }
}

function handleMultiFileSelect(input) {
    if (input.files && input.files.length > 0) {
        selectedMultiFiles = Array.from(input.files);
        document.getElementById('upload-status-text').innerText = `Selected ${input.files.length} photographs for multi-angle identification.`;
        document.getElementById('file-preview-container').style.display = 'none';
        document.getElementById('btn-submit-id').disabled = false;
    }
}

// Drag and drop handlers
const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.background = 'var(--accent)'; });
dropZone.addEventListener('dragleave', (e) => { e.preventDefault(); dropZone.style.background = '#fff'; });
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fff';
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        document.getElementById('file-input-single').files = e.dataTransfer.files;
        handleSingleFileSelect(document.getElementById('file-input-single'));
    }
});

// Form submission AJAX
document.getElementById('identify-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const singleInput = document.getElementById('file-input-single');
    const multiInput = document.getElementById('file-input-multi');

    const formData = new FormData(this);
    let endpoint = 'api/identify';

    if (multiInput.files && multiInput.files.length > 1) {
        endpoint = 'api/identify/multiple';
    }

    document.getElementById('loading-spinner').style.display = 'block';
    document.getElementById('result-container').style.display = 'none';
    document.getElementById('btn-submit-id').disabled = true;

    try {
        const res = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        document.getElementById('loading-spinner').style.display = 'block';

        if (data.error) {
            alert('Error: ' + data.error);
            document.getElementById('loading-spinner').style.display = 'none';
            document.getElementById('btn-submit-id').disabled = false;
            return;
        }

        renderResult(data);
    } catch (err) {
        alert('Identification failed: ' + err.message);
    } finally {
        document.getElementById('loading-spinner').style.display = 'none';
        document.getElementById('btn-submit-id').disabled = false;
    }
});

function renderResult(data) {
    const container = document.getElementById('result-container');
    const cand = data.primary_candidate || {};
    const phil = data.philippine_context || {};
    const med = data.medicinal || {};
    const safe = data.safety || {};
    const cons = data.conservation || {};
    const ver = data.verification || {};

    let html = `
        <div class="result-card">
            <div class="result-header">
                <div>
                    <span style="text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; color: #555;">MOST LIKELY IDENTIFICATION</span>
                    <h2 class="plant-title">${cand.common_name || 'Unknown Species'}</h2>
                    <p class="scientific-title">Scientific name: <strong>${cand.scientific_name || 'N/A'}</strong></p>
                </div>
                <div class="confidence-badge conf-${data.confidence_level}">
                    ${data.confidence_level} Confidence (${data.confidence_score}%)
                </div>
            </div>

            <div class="warning-banner">
                <h4>⚠️ IMPORTANT SCIENTIFIC SAFETY NOTICE</h4>
                <p>This is an AI-assisted botanical identification. It must be verified before the plant is consumed, used medicinally, or planted for conservation purposes.</p>
            </div>
    `;

    if (safe.warning_text) {
        html += `
            <div class="warning-banner" style="background: #f8d7da; border-color: #f5c6cb;">
                <h4 style="color: #721c24;">🚨 ${safe.warning_text}</h4>
                <p><strong>Primary Warning:</strong> ${safe.primary_warning || ''}</p>
                ${safe.look_alike_species ? `<p><strong>Look-alike Species:</strong> ${safe.look_alike_species} (${safe.look_alike_distinction || ''})</p>` : ''}
            </div>
        `;
    }

    // Taxonomy & Names
    html += `
        <div class="info-section">
            <h3 class="section-title" data-i18n="sci_class">Scientific Classification</h3>
            <div class="grid-2">
                <div class="fact-box"><strong>Kingdom:</strong> ${cand.kingdom || 'Plantae'}</div>
                <div class="fact-box"><strong>Family:</strong> ${cand.family || 'N/A'}</div>
                <div class="fact-box"><strong>Genus:</strong> ${cand.genus || 'N/A'}</div>
                <div class="fact-box"><strong>Species:</strong> ${cand.species || 'N/A'}</div>
            </div>
        </div>

        <div class="info-section">
            <h3 class="section-title" data-i18n="ph_context">Philippine Context & Distribution</h3>
            <div class="grid-2">
                <div class="fact-box"><strong>Native Status:</strong> ${phil.native_status || 'UNKNOWN'}</div>
                <div class="fact-box"><strong>Philippine Distribution:</strong> ${phil.distribution || 'Widespread'}</div>
                <div class="fact-box"><strong>Habitat:</strong> ${phil.habitat || 'N/A'}</div>
                <div class="fact-box"><strong>Elevation Range:</strong> ${phil.elevation_range || 'Lowland to mid-altitude'}</div>
            </div>
        </div>

        <div class="info-section">
            <h3 class="section-title" data-i18n="herbal_title">Herbal & Medicinal Information</h3>
            <div class="fact-box" style="margin-bottom: 12px;">
                <strong>Recognized Medicinal Status:</strong> ${med.classification || 'TRADITIONALLY_USED'}
                <p style="margin-top: 4px;"><strong>Traditional Uses:</strong> ${med.traditional_uses_text || 'Used in traditional Philippine ethnobotanical practices.'}</p>
                <p style="margin-top: 4px;"><strong>Scientific Evidence:</strong> ${med.scientific_evidence_text || 'Preliminary research available.'}</p>
            </div>
        </div>

        <div class="info-section">
            <h3 class="section-title" data-i18n="verify_title">How Can I Confirm This Identification?</h3>
            <ul class="verify-checklist">
                ${(ver.recommended_checks || ['Compare leaf arrangement', 'Examine bark', 'Consult qualified botanist']).map(c => `<li>☑ ${c}</li>`).join('')}
            </ul>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 20px;">
            <a href="index.php?r=observations" class="btn btn-primary" data-i18n="btn_save_obs">💾 Record Field Observation</a>
            <button type="button" class="btn btn-warning" onclick="requestExpertReview(${data.identification_id})" data-i18n="btn_expert_review">👨‍🌾 Request Expert Review</button>
            <button type="button" class="btn btn-outline" onclick="location.reload()" data-i18n="btn_reset">Identify Another Plant</button>
        </div>
    `;

    container.innerHTML = html;
    container.style.display = 'block';
    container.scrollIntoView({ behavior: 'smooth' });
}

async function requestExpertReview(identId) {
    if (!identId) return;
    const notes = prompt("Please enter any additional notes for the botanical expert:");
    if (notes === null) return;

    const fd = new FormData();
    fd.append('identification_id', identId);
    fd.append('notes', notes);

    const res = await fetch('api/expert-review', { method: 'POST', body: fd });
    const json = await res.json();
    alert(json.message || json.error);
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
