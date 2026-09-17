<?php require_once __DIR__ . '/../partials/header.php'; ?>

<section class="hero-card">
    <h1 class="hero-title">Learn Plant Identification (Edukasyon)</h1>
    <p class="hero-subtitle">Interactive lessons and training exercises aligned with botanical taxonomy and Philippine biodiversity knowledge.</p>
</section>

<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Interactive Quiz / Practice -->
    <div class="questionnaire-card">
        <h3 class="section-title">🌿 Practice Identification Exercise</h3>
        <p style="margin-bottom: 12px;">Test your visual plant identification skills before revealing the AI result!</p>

        <div style="background: #e9ecef; border-radius: 8px; padding: 16px; text-align: center; margin-bottom: 16px;">
            <p><strong>Observe these 3 Diagnostic Features:</strong></p>
            <ul style="text-align: left; margin: 10px 0 10px 20px;">
                <li>Opposite or near-opposite thick leaf arrangement</li>
                <li>Reddish shed leaves and crinkled purple/pink petals</li>
                <li>Large serrated woody seed capsules</li>
            </ul>
        </div>

        <div class="form-group">
            <label>What species is this?</label>
            <select id="quiz-select" class="form-control">
                <option value="">-- Choose Species --</option>
                <option value="Lagundi">Lagundi (Vitex negundo)</option>
                <option value="Banaba">Banaba (Lagerstroemia speciosa)</option>
                <option value="Sambong">Sambong (Blumea balsamifera)</option>
                <option value="Narra">Narra (Pterocarpus indicus)</option>
            </select>
        </div>
        <button type="button" onclick="checkQuizAnswer()" class="btn btn-primary" style="width: 100%; justify-content: center;">Check Answer</button>
        <div id="quiz-feedback" style="margin-top: 12px; font-weight: 600;"></div>
    </div>

    <!-- TESDA-Aligned Modules Overview -->
    <div class="questionnaire-card">
        <h3 class="section-title">📚 Educational Competency Modules</h3>
        <p style="font-size: 0.9rem; color: #555; margin-bottom: 12px;">Designed to support educational institutions, forestry students, and community conservation workers.</p>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <div class="fact-box">
                <strong>Module 1: Leaf Morphology & Arrangement</strong>
                <p style="font-size: 0.85rem;">Learn simple vs. compound leaves, opposite vs. alternate, pinnate venation.</p>
            </div>
            <div class="fact-box">
                <strong>Module 2: Philippine Native & Endemic Trees</strong>
                <p style="font-size: 0.85rem;">Distinguishing Narra, Katmon, Molave, Kamagong from introduced invasives.</p>
            </div>
            <div class="fact-box">
                <strong>Module 3: Plant Safety & Look-Alike Hazards</strong>
                <p style="font-size: 0.85rem;">Identifying dangerous species like Tubang Bakod and safe herbal plants.</p>
            </div>
            <div class="fact-box">
                <strong>Module 4: Field Documentation & GPS Logging</strong>
                <p style="font-size: 0.85rem;">Proper botanical photography and recording field observations.</p>
            </div>
        </div>
    </div>
</div>

<script>
function checkQuizAnswer() {
    const val = document.getElementById('quiz-select').value;
    const fb = document.getElementById('quiz-feedback');
    if (val === 'Banaba') {
        fb.style.color = 'var(--primary)';
        fb.innerHTML = '✅ Correct! The features describe Banaba (Lagerstroemia speciosa). Excellent visual identification!';
    } else if (val) {
        fb.style.color = 'var(--warning)';
        fb.innerHTML = '❌ Not quite. Tip: Notice the crinkled purple petals and large thick leaves characteristic of Banaba.';
    } else {
        fb.innerHTML = 'Please select a species answer first.';
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
