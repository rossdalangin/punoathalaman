<?php

// Puno at Halaman AI - Web Installation Wizard
error_reporting(E_ALL);
ini_set('display_errors', 1);

$lockFile = __DIR__ . '/../storage/installed.lock';
if (file_exists($lockFile)) {
    die("<h1>Puno at Halaman AI</h1><p>Application is already installed. If you need to re-install, delete <code>storage/installed.lock</code>.</p>");
}

// Fallback autoloader for installation execution
spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
        'Database\\Seeders\\' => __DIR__ . '/../database/seeders/'
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

$step = $_GET['step'] ?? 1;
$errors = [];
$successMessage = "";

// Handle Step 2 POST (Database Configuration & Installation Execution)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = trim($_POST['db_port'] ?? '3306');
    $dbName = trim($_POST['db_name'] ?? 'puno_at_halaman');
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPass = $_POST['db_pass'] ?? '';
    $aiProvider = trim($_POST['ai_provider'] ?? 'mock');
    $aiApiKey = trim($_POST['ai_api_key'] ?? '');

    try {
        // 1. Test database connection
        $dsnNoDb = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
        $pdo = new PDO($dsnNoDb, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // 2. Create Database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbName}`;");

        // 3. Import Schema SQL
        $schemaSqlFile = __DIR__ . '/../database/schema.sql';
        if (!file_exists($schemaSqlFile)) {
            throw new Exception("Schema file database/schema.sql not found.");
        }
        $sql = file_get_contents($schemaSqlFile);
        $pdo->exec($sql);

        // 4. Generate .env File
        $envContent = <<<ENV
APP_NAME="Puno at Halaman AI"
APP_ENV=production
APP_URL=http://{$_SERVER['HTTP_HOST']}
APP_SECRET=puno_at_halaman_secret_key_change_in_production_2026

# Database Configuration
DB_DRIVER=mysql
DB_HOST={$dbHost}
DB_PORT={$dbPort}
DB_DATABASE={$dbName}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPass}

# AI Identifier Configuration
AI_PROVIDER={$aiProvider}
AI_API_KEY={$aiApiKey}
AI_MODEL=gpt-4o

# Upload Settings
MAX_UPLOAD_SIZE_MB=10
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,webp
STORAGE_PATH=storage/uploads

# Security
CSRF_ENABLED=true
ENV;

        file_put_contents(__DIR__ . '/../.env', $envContent);

        // Load config into memory for seeder
        App\Helpers\Config::loadEnv(__DIR__ . '/../.env');

        // 5. Seed Philippine Flora Data
        Database\Seeders\SeedDatabase::run();

        // 6. Create Installation Lock File
        file_put_contents($lockFile, date('Y-m-d H:i:s'));

        // 7. Security Self-Deletion: Unlink/delete install.php script
        $currentInstallerPath = __FILE__;
        @unlink($currentInstallerPath);

        $step = 3; // Move to Success Step
    } catch (Exception $e) {
        $errors[] = "Installation Failed: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Installer - Puno at Halaman AI</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f0f4f1; color: #081c15; line-height: 1.6; padding: 20px; }
        .installer-card { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
        .btn { background: #2d6a4f; color: #fff; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1b4332; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .req-list { list-style: none; padding: 0; }
        .req-list li { padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

<div class="installer-card">
    <h2 style="color: #2d6a4f; text-align: center;">🌿 Puno at Halaman AI</h2>
    <p style="text-align: center; color: #666; margin-bottom: 24px;">Web Application Setup & Installation Wizard</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($step == 1): ?>
        <h3>Step 1: Server Requirements Check</h3>
        <ul class="req-list">
            <li>PHP Version >= 8.2: <span><?= PHP_VERSION_ID >= 80200 ? '✅ Pass (' . PHP_VERSION . ')' : '❌ Fail' ?></span></li>
            <li>PDO Extension: <span><?= extension_loaded('pdo') ? '✅ Enabled' : '❌ Disabled' ?></span></li>
            <li>PDO MySQL Extension: <span><?= extension_loaded('pdo_mysql') ? '✅ Enabled' : '❌ Disabled' ?></span></li>
            <li>Fileinfo Extension: <span><?= extension_loaded('fileinfo') ? '✅ Enabled' : '❌ Disabled' ?></span></li>
            <li>GD Image Library: <span><?= extension_loaded('gd') ? '✅ Enabled' : '❌ Disabled' ?></span></li>
            <li>Storage Directory Writable: <span><?= is_writable(__DIR__ . '/../storage') ? '✅ Writable' : '❌ Not Writable' ?></span></li>
        </ul>

        <div style="text-align: right; margin-top: 24px;">
            <a href="install.php?step=2" class="btn">Next: Database Setup ➔</a>
        </div>

    <?php elseif ($step == 2): ?>
        <h3>Step 2: Database & System Configuration</h3>
        <form action="install.php?step=2" method="POST">
            <div class="form-group">
                <label>Database Host:</label>
                <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
            </div>
            <div class="form-group">
                <label>Database Port:</label>
                <input type="text" name="db_port" class="form-control" value="3306" required>
            </div>
            <div class="form-group">
                <label>Database Name:</label>
                <input type="text" name="db_name" class="form-control" value="puno_at_halaman" required>
            </div>
            <div class="form-group">
                <label>Database Username:</label>
                <input type="text" name="db_user" class="form-control" value="root" required>
            </div>
            <div class="form-group">
                <label>Database Password:</label>
                <input type="password" name="db_pass" class="form-control" placeholder="Leave empty for default XAMPP root">
            </div>

            <hr style="margin: 20px 0;">

            <div class="form-group">
                <label>AI Vision Provider:</label>
                <select name="ai_provider" class="form-control">
                    <option value="mock">Mock Offline Model (Ready Default)</option>
                    <option value="openai">OpenAI (GPT-4o Vision API)</option>
                    <option value="gemini">Google Gemini Vision API</option>
                </select>
            </div>
            <div class="form-group">
                <label>AI API Key (Optional):</label>
                <input type="text" name="ai_api_key" class="form-control" placeholder="Paste OpenAI or Gemini API key">
            </div>

            <button type="submit" class="btn" style="width: 100%;">Install & Seed Application</button>
        </form>

    <?php elseif ($step == 3): ?>
        <div class="alert alert-success">
            <h3>🎉 Installation Completed Successfully!</h3>
            <p>Database created, schema imported, and Philippine flora database seeded successfully.</p>
            <p><strong>🔒 Security Notice:</strong> The installation script (<code>install.php</code>) has been automatically deleted from the server for security reasons.</p>
        </div>

        <div style="text-align: center; margin-top: 24px;">
            <a href="./" class="btn">🚀 Open Puno at Halaman AI Application</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
