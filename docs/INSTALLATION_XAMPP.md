# XAMPP & Windows Installation Guide for Puno at Halaman AI

This guide addresses the error:
`Warning: require_once(.../vendor/autoload.php): Failed to open stream: No such file or directory`

This error occurs when PHP Composer dependencies have not been installed yet in your local folder. Follow the step-by-step instructions below to resolve this and run the application on XAMPP (Windows).

---

## 🛠️ Step 1: Install Composer on Windows

1. Download the official **Composer-Setup.exe** installer from [getcomposer.org](https://getcomposer.org/download/).
2. Run the installer and select your XAMPP PHP executable path if prompted:
   - Example path: `C:\xampp2\php\php.exe` or `C:\xampp\php\php.exe`.
3. Complete the installation wizard.

---

## 📦 Step 2: Generate `vendor/autoload.php` via Composer

1. Open **Command Prompt (cmd)** or **PowerShell**.
2. Navigate to your project directory inside XAMPP:
   ```cmd
   cd C:\xampp2\htdocs\punoathalaman
   ```
3. Run the following command to download vendor packages and create `vendor/autoload.php`:
   ```cmd
   composer install
   ```
   *(If you already have vendor packages downloaded or modified `composer.json`, run `composer dump-autoload`)*

After running `composer install`, a `vendor` folder containing `vendor/autoload.php` will be created automatically.

---

## ⚙️ Step 3: Configure Environment File (`.env`)

1. Copy `.env.example` to create `.env` in your project root (`C:\xampp2\htdocs\punoathalaman\.env`).
2. Open `.env` in a text editor (e.g. VS Code or Notepad) and set your XAMPP MySQL settings:

```ini
APP_NAME="Puno at Halaman AI"
APP_ENV=development
APP_URL=http://localhost/punoathalaman/public

# Database Configuration for XAMPP
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=puno_at_halaman
DB_USERNAME=root
DB_PASSWORD=

# AI Provider Configuration (mock, openai, or gemini)
AI_PROVIDER=mock
AI_API_KEY=your_api_key_here
```

---

## 🗄️ Step 4: Create Database in XAMPP (phpMyAdmin)

1. Start **Apache** and **MySQL** in your **XAMPP Control Panel**.
2. Open your web browser and go to `http://localhost/phpmyadmin`.
3. Click **Databases** tab and create a new database named `puno_at_halaman` (utf8mb4_unicode_ci).
4. Select `puno_at_halaman`, click the **Import** tab, browse to `C:\xampp2\htdocs\punoathalaman\database\schema.sql`, and click **Go**.

---

## 🌱 Step 5: Seed Philippine Flora Data

In your Command Prompt inside `C:\xampp2\htdocs\punoathalaman`, run:
```cmd
php -r "require 'vendor/autoload.php'; App\Helpers\Config::loadEnv('.env'); Database\Seeders\SeedDatabase::run();"
```
You should see the output: `Database seeded successfully!`.

---

## 🚀 Step 6: Accessing the Application in Browser

Option A (Via Built-in PHP Server - Recommended):
1. In Command Prompt:
   ```cmd
   cd C:\xampp2\htdocs\punoathalaman
   php -S localhost:8000 -t public
   ```
2. Open browser to `http://localhost:8000`.

Option B (Via XAMPP Apache):
1. Open browser to `http://localhost/punoathalaman/public`.
