const i18n = {
    en: {
        nav_home: "Home",
        nav_learn: "Learn ID",
        nav_field: "Field Records",
        nav_admin: "Admin",
        footer_desc: "AI-Assisted Philippine Plant, Tree & Herbal Knowledge Identifier.",
        footer_disclaimer: "Important: This is an AI-assisted identification and educational tool. Always verify identifications with qualified botanists or foresters before consuming or using plants medicinally.",
        hero_title: "Kilalanin ang Halaman sa Larawan",
        hero_subtitle: "Upload a photo or screenshot of a leaf, flower, bark, fruit, or tree to identify Philippine plant species.",
        btn_take_photo: "📷 Take Photo",
        btn_upload_image: "🖼 Upload Image",
        btn_upload_multi: "📁 Upload Multiple Photos",
        drag_drop_text: "Drag & drop plant image here or click to browse",
        btn_identify: "Identify Plant Species",
        btn_reset: "Identify Another Plant",
        btn_save_obs: "💾 Save Observation",
        btn_expert_review: "👨‍🌾 Request Expert Review",
        questions_title: "Optional Context (Improves Accuracy)",
        q_location: "Where was it found?",
        q_height: "Estimated plant height (e.g. shrub, tree):",
        q_flowers: "Does it have visible flowers?",
        q_fruit: "Does it have fruit?",
        q_sap: "Does it produce white/milky sap when broken?",
        q_province: "Province / Municipality found:",
        analyzing_text: "Analyzing image with AI Vision & Philippine Biodiversity Database...",
        result_title: "PLANT IDENTIFICATION RESULT",
        confidence_label: "Confidence:",
        sci_class: "Scientific Classification",
        ph_context: "Philippine Context & Distribution",
        uses_title: "What Is This Plant Useful For?",
        herbal_title: "Herbal / Medicinal Information",
        safety_title: "Safety & Toxicity Warnings",
        conservation_title: "Conservation Status",
        verify_title: "How Can I Confirm This Identification?",
        sources_title: "Authoritative Reference Sources"
    },
    fil: {
        nav_home: "Tahanan",
        nav_learn: "Matuto Magkilala",
        nav_field: "Ulat sa Likas",
        nav_admin: "Tagapamahala",
        footer_desc: "AI na Gabay sa Pagkilala ng Halaman, Puno, at Gamot sa Pilipinas.",
        footer_disclaimer: "Mahalaga: Ito ay isang AI-assisted at edukasyonal na tool. Laging siguraduhing ipasuri ang halaman sa eksperto o botanist bago gamitin sa gamot o kainin.",
        hero_title: "Kilalanin ang Halaman sa Larawan",
        hero_subtitle: "Mag-upload ng litrato o screenshot ng dahon, bulaklak, balat ng puno, o buong halaman para makilala.",
        btn_take_photo: "📷 Kumuha ng Larawan",
        btn_upload_image: "🖼 Mag-upload ng Larawan",
        btn_upload_multi: "📁 Mag-upload ng Maraming Larawan",
        drag_drop_text: "I-drag at i-drop ang larawan ng halaman dito o i-click para pumili",
        btn_identify: "Suriin at Kilalanin ang Halaman",
        btn_reset: "Mag-suri ng Ibang Halaman",
        btn_save_obs: "💾 I-sanay/I-tala sa Field Observation",
        btn_expert_review: "👨‍🌾 Humingi ng Suri sa Eksperto",
        questions_title: "Karagdagang Impormasyon (Para sa Mas Tumpak na Suri)",
        q_location: "Saan ito natagpuan?",
        q_height: "Tantiya sa taas ng halaman (hal. huddle, puno):",
        q_flowers: "Mayroon bang bulaklak?",
        q_fruit: "Mayroon bang bunga?",
        q_sap: "NaglALABAS ba ng puting gatas/sap kapag naputol?",
        q_province: "Lalawigan / Bayan kung saan nakuha:",
        analyzing_text: "Sinisiyasat ang larawan sa tulong ng AI Vision at Philippine Biodiversity Database...",
        result_title: "RESULTA NG PAGKILALA SA HALAMAN",
        confidence_label: "Antas ng Katiyakan (Confidence):",
        sci_class: "Siyentipikong Pag-uuri (Taxonomy)",
        ph_context: "Konteksto sa Pilipinas at Habitat",
        uses_title: "Saan Nagagamit ang Halamang Ito?",
        herbal_title: "Impormasyon sa Gamot / Halaman",
        safety_title: "Mga Babala sa Kaligtasan at Lason",
        conservation_title: "Katayuan sa Konserbasyon",
        verify_title: "Paano Ko Makukumpirma ang Identipikasyon?",
        sources_title: "Mga Pinagkunan at Sanggunian"
    }
};

let currentLang = localStorage.getItem('ph_app_lang') || 'en';

function setLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('ph_app_lang', lang);

    document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
    const btn = document.getElementById(`btn-lang-${lang}`);
    if (btn) btn.classList.add('active');

    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (i18n[lang] && i18n[lang][key]) {
            el.innerText = i18n[lang][key];
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setLanguage(currentLang);
});
