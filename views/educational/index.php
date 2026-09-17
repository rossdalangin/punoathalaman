<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 class="hero-title" style="margin-bottom:4px;">Botanical Training & Interactive Exercises</h1>
            <p class="hero-subtitle" style="margin-bottom:0;">Develop practical skills in identifying Philippine leaves, trees, medicinal herbs, and dangerous look-alikes.</p>
        </div>
        <div id="quiz-score-badge" class="badge badge-success" style="font-size:1.1rem; padding:8px 16px;">
            Score: <span id="current-score">0</span> / <span id="total-answered">0</span>
        </div>
    </div>
</section>

<!-- Training Drills Tabs -->
<div class="admin-tabs" style="margin-bottom: 20px;">
    <button type="button" class="tab-btn active" onclick="switchEduTab('tab-drill-1', this)">🍃 1. Leaf Morphology Drills</button>
    <button type="button" class="tab-btn" onclick="switchEduTab('tab-drill-2', this)">🌳 2. Tree Bark & Habit Practice</button>
    <button type="button" class="tab-btn" onclick="switchEduTab('tab-drill-3', this)">⚠️ 3. Poisonous vs. Medicinal Safety</button>
    <button type="button" class="tab-btn" onclick="switchEduTab('tab-drill-4', this)">🇵🇭 4. Philippine Endemic Species</button>
    <button type="button" class="tab-btn" onclick="switchEduTab('tab-modules', this)">📚 Competency Curriculum</button>
</div>

<!-- DRILL 1: LEAF MORPHOLOGY -->
<div id="tab-drill-1" class="edu-tab-content active">
    <div class="questionnaire-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h3 class="section-title" style="margin-bottom:0;">Drill 1: Leaf Arrangement & Venation Identification</h3>
            <span class="badge badge-info">Level: Basic Botanical Skills</span>
        </div>
        <p style="margin-top:8px;">Examine the 3 key visual leaf characteristics below and identify the species:</p>

        <div style="background: #f4f8f5; border: 1px solid var(--border); border-radius: 8px; padding: 18px; margin: 16px 0;">
            <p style="font-weight:600; color:var(--primary-dark); margin-top:0;">🔍 Diagnostic Features Observed:</p>
            <ul style="margin: 8px 0 0 20px; line-height: 1.6;">
                <li><strong>Arrangement:</strong> Opposite 3 to 5 linear-lanceolate compound leaflets</li>
                <li><strong>Texture / Underside:</strong> Whitish velvety hairy leaf underside with aromatic scent when crushed</li>
                <li><strong>Margin:</strong> Entire to slightly wavy serrated margins</li>
            </ul>
        </div>

        <div class="form-group">
            <label>Which Philippine plant matches these leaf traits?</label>
            <select id="drill-1-select" class="form-control">
                <option value="">-- Select Species --</option>
                <option value="Banaba">Banaba (Lagerstroemia speciosa)</option>
                <option value="Lagundi">Lagundi (Vitex negundo)</option>
                <option value="Sambong">Sambong (Blumea balsamifera)</option>
                <option value="Tsaang Gubat">Tsaang Gubat (Carmona retusa)</option>
            </select>
        </div>
        <button type="button" onclick="evaluateDrill('drill-1-select', 'Lagundi', 'drill-1-feedback', 'Lagundi (Vitex negundo) has palmately 3-5 compound leaflets arranged oppositely, with a whitish hairy underside and aromatic crushed leaves used for cough/asthma relief.')" class="btn btn-primary">Check Leaf Answer</button>
        <div id="drill-1-feedback" style="margin-top: 14px;"></div>
    </div>
</div>

<!-- DRILL 2: TREE BARK & HABIT -->
<div id="tab-drill-2" class="edu-tab-content" style="display:none;">
    <div class="questionnaire-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h3 class="section-title" style="margin-bottom:0;">Drill 2: Native Tree Canopy & Bark Recognition</h3>
            <span class="badge badge-info">Level: Field Forester Skills</span>
        </div>
        <p style="margin-top:8px;">Analyze the whole tree growth habit and bark traits:</p>

        <div style="background: #f4f8f5; border: 1px solid var(--border); border-radius: 8px; padding: 18px; margin: 16px 0;">
            <p style="font-weight:600; color:var(--primary-dark); margin-top:0;">🔍 Field Trunk & Habit Observation:</p>
            <ul style="margin: 8px 0 0 20px; line-height: 1.6;">
                <li><strong>Habit:</strong> Large timber canopy tree (30m tall) with wide spreading crown and buttress roots</li>
                <li><strong>Bark / Exudate:</strong> Grayish-brown flaky bark that exudes red crimson ("dragon blood") sap when cut</li>
                <li><strong>Fruit Pod:</strong> Disc-like papery winged fruit pod (samara)</li>
            </ul>
        </div>

        <div class="form-group">
            <label>What native Philippine tree is this?</label>
            <select id="drill-2-select" class="form-control">
                <option value="">-- Select Species --</option>
                <option value="Kamagong">Kamagong (Diospyros blancoí)</option>
                <option value="Narra">Narra (Pterocarpus indicus)</option>
                <option value="Molave">Molave (Vitex parviflora)</option>
                <option value="Katmon">Katmon (Dillenia philippinensis)</option>
            </select>
        </div>
        <button type="button" onclick="evaluateDrill('drill-2-select', 'Narra', 'drill-2-feedback', 'Narra (Pterocarpus indicus) is the national tree of the Philippines, known for crimson bark resin exudate and flat winged disc fruit pods.')" class="btn btn-primary">Check Tree Answer</button>
        <div id="drill-2-feedback" style="margin-top: 14px;"></div>
    </div>
</div>

<!-- DRILL 3: POISONOUS VS MEDICINAL SAFETY -->
<div id="tab-drill-3" class="edu-tab-content" style="display:none;">
    <div class="questionnaire-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h3 class="section-title" style="margin-bottom:0;">Drill 3: Poisonous Species Safety Awareness</h3>
            <span class="badge badge-danger">Level: Critical Safety Protocol</span>
        </div>
        <p style="margin-top:8px;">You encounter a fence plant with sticky milky sap and lobed leaves:</p>

        <div style="background: #fff3f3; border: 1px solid var(--danger); border-radius: 8px; padding: 18px; margin: 16px 0;">
            <p style="font-weight:600; color:var(--danger); margin-top:0;">⚠️ Diagnostic Warning Features:</p>
            <ul style="margin: 8px 0 0 20px; line-height: 1.6;">
                <li><strong>Habit:</strong> Coarse fence-line shrub with thick green stems and sticky white sap</li>
                <li><strong>Fruit / Seed:</strong> Smooth green 3-lobed capsule containing oil-rich black seeds</li>
                <li><strong>Toxicity Warning:</strong> Seeds contain deadly curcin toxalbumin and phorbol esters</li>
            </ul>
        </div>

        <div class="form-group">
            <label>What highly poisonous plant is this?</label>
            <select id="drill-3-select" class="form-control">
                <option value="">-- Select Species --</option>
                <option value="Tsaang Gubat">Tsaang Gubat (Carmona retusa)</option>
                <option value="Tubang Bakod">Tubang Bakod / Physic Nut (Jatropha curcas)</option>
                <option value="Sambong">Sambong (Blumea balsamifera)</option>
                <option value="Akapulko">Akapulko (Senna alata)</option>
            </select>
        </div>
        <button type="button" onclick="evaluateDrill('drill-3-select', 'Tubang Bakod', 'drill-3-feedback', 'DANGER VERIFIED! Tubang Bakod (Jatropha curcas) seeds are highly poisonous containing curcin. Never consume seeds or confuse with edible crops.')" class="btn btn-primary">Check Safety Answer</button>
        <div id="drill-3-feedback" style="margin-top: 14px;"></div>
    </div>
</div>

<!-- DRILL 4: ENDEMIC SPECIES -->
<div id="tab-drill-4" class="edu-tab-content" style="display:none;">
    <div class="questionnaire-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h3 class="section-title" style="margin-bottom:0;">Drill 4: Philippine Endemic Flora Recognition</h3>
            <span class="badge badge-success">Level: Philippine Biodiversity Specialist</span>
        </div>
        <p style="margin-top:8px;">Identify this endemic tree species found only in the Philippine archipelago:</p>

        <div style="background: #f4f8f5; border: 1px solid var(--border); border-radius: 8px; padding: 18px; margin: 16px 0;">
            <p style="font-weight:600; color:var(--primary-dark); margin-top:0;">🔍 Endemic Trait Breakdown:</p>
            <ul style="margin: 8px 0 0 20px; line-height: 1.6;">
                <li><strong>Coin Feature:</strong> Philippine endemic tree featured on the 25-centimo coin</li>
                <li><strong>Flower & Fruit:</strong> Large showy 10-15cm white flower; globose sour fruit enclosed in fleshy persistent green sepals used in Sinigang</li>
                <li><strong>Leaf:</strong> Simple thick leaf with very parallel prominent lateral veins terminating at margin teeth</li>
            </ul>
        </div>

        <div class="form-group">
            <label>What Philippine endemic tree species is this?</label>
            <select id="drill-4-select" class="form-control">
                <option value="">-- Select Species --</option>
                <option value="Katmon">Katmon (Dillenia philippinensis)</option>
                <option value="Kamagong">Kamagong (Diospyros blancoí)</option>
                <option value="Narra">Narra (Pterocarpus indicus)</option>
                <option value="Bayabas">Bayabas (Psidium guajava)</option>
            </select>
        </div>
        <button type="button" onclick="evaluateDrill('drill-4-select', 'Katmon', 'drill-4-feedback', 'Katmon (Dillenia philippinensis) is a Philippine endemic tree species whose sour segmented fruits are traditionally used as a culinary acidulant.')" class="btn btn-primary">Check Endemic Answer</button>
        <div id="drill-4-feedback" style="margin-top: 14px;"></div>
    </div>
</div>

<!-- COMPETENCY CURRICULUM -->
<div id="tab-modules" class="edu-tab-content" style="display:none;">
    <div class="grid-2">
        <div class="questionnaire-card">
            <h3 class="section-title">📚 Training Modules</h3>
            <div style="display: flex; flex-direction: column; gap: 12px; margin-top:12px;">
                <div class="fact-box">
                    <strong>Module 1: Leaf Morphology & Arrangement</strong>
                    <p style="font-size: 0.85rem; margin-top:4px;">Master simple vs. compound blades, opposite vs. alternate nodes, pinnate/palmate venation, and serrated margins.</p>
                </div>
                <div class="fact-box">
                    <strong>Module 2: Native vs. Introduced Tree Identification</strong>
                    <p style="font-size: 0.85rem; margin-top:4px;">Distinguish native canopy giants (Narra, Katmon, Molave, Kamagong) from invasive exotic trees.</p>
                </div>
                <div class="fact-box">
                    <strong>Module 3: Ethnobotanical & Medicinal Evidence</strong>
                    <p style="font-size: 0.85rem; margin-top:4px;">Differentiate DOH-PITAHC scientifically established herbal medicines from unproven traditional claims.</p>
                </div>
                <div class="fact-box">
                    <strong>Module 4: Field Observation & GPS Mapping</strong>
                    <p style="font-size: 0.85rem; margin-top:4px;">Learn standard DENR/BMB field documentation, GPS logging, DBH measurement, and dataset exporting.</p>
                </div>
            </div>
        </div>

        <div class="questionnaire-card">
            <h3 class="section-title">🎓 Certificate & Competency Record</h3>
            <p style="font-size: 0.9rem; color: #555; margin-bottom: 16px;">Complete interactive drills above to generate your training progress record.</p>
            <div style="background: #f8f9fa; border: 2px dashed var(--border); border-radius: 8px; padding: 20px; text-align: center;">
                <h4 style="margin-top:0; color:var(--primary-dark);">Philippine Plant Identification Training</h4>
                <p style="font-size:0.85rem; color:#666;">Practical Skills Self-Assessment Completed</p>
                <div style="font-size:1.4rem; font-weight:bold; color:var(--primary); margin: 10px 0;" id="cert-score-display">0 / 4 Drills Passed</div>
                <button type="button" class="btn btn-outline btn-sm" onclick="alert('Congratulations! Your plant identification competency progress record is saved.')">📜 Download Learning Record</button>
            </div>
        </div>
    </div>
</div>

<script>
let correctAnswers = 0;
let answeredSet = new Set();

function switchEduTab(tabId, btn) {
    document.querySelectorAll('.edu-tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.admin-tabs .tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}

function evaluateDrill(selectId, expectedValue, feedbackId, explanation) {
    const val = document.getElementById(selectId).value;
    const fb = document.getElementById(feedbackId);

    if (!val) {
        fb.innerHTML = '<span style="color:var(--danger); font-weight:600;">Please select a species answer first.</span>';
        return;
    }

    if (!answeredSet.has(selectId)) {
        answeredSet.add(selectId);
        document.getElementById('total-answered').innerText = answeredSet.size;
    }

    if (val === expectedValue) {
        correctAnswers++;
        document.getElementById('current-score').innerText = correctAnswers;
        document.getElementById('cert-score-display').innerText = correctAnswers + ' / 4 Drills Passed';
        fb.innerHTML = `<div class="fact-box" style="border-left: 4px solid var(--primary); background:#eef7f2;">
            <p style="color:var(--primary-dark); font-weight:bold; margin-bottom:4px;">✅ Excellent! Correct Identification.</p>
            <p style="font-size:0.88rem; margin:0;">${explanation}</p>
        </div>`;
    } else {
        fb.innerHTML = `<div class="fact-box" style="border-left: 4px solid var(--warning); background:#fff9ef;">
            <p style="color:var(--warning-dark, #b7791f); font-weight:bold; margin-bottom:4px;">❌ Not quite correct.</p>
            <p style="font-size:0.88rem; margin:0;">Hint: Review the diagnostic leaf arrangement and margin features, then try again.</p>
        </div>`;
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
