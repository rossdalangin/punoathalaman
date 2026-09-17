<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Puno at Halaman AI') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <a href="index.php" class="brand">
                <span class="brand-icon">🌿</span>
                <span class="brand-text">Puno at Halaman <span class="brand-badge">AI</span></span>
            </a>

            <nav class="site-nav">
                <a href="index.php" class="nav-link" data-i18n="nav_home">Home</a>
                <a href="index.php?r=educational" class="nav-link" data-i18n="nav_learn">Learn ID</a>
                <a href="index.php?r=observations" class="nav-link" data-i18n="nav_field">Field Records</a>
                <a href="index.php?r=admin" class="nav-link" data-i18n="nav_admin">Admin</a>
            </nav>

            <div class="lang-selector">
                <button type="button" id="btn-lang-en" class="lang-btn active" onclick="setLanguage('en')">English</button>
                <button type="button" id="btn-lang-fil" class="lang-btn" onclick="setLanguage('fil')">Filipino</button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
