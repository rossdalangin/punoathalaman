# Puno at Halaman AI 🌿
### AI-Assisted Philippine Plant, Tree & Herbal Knowledge Identifier

"Puno at Halaman AI" is a web application designed specifically for the Philippine context to assist ordinary Filipinos, students, foresters, community workers, nature guides, and researchers in identifying, documenting, and understanding Philippine native, endemic, introduced, agricultural, ornamental, and medicinal plant species.

---

## 🌟 Key Features

1. **AI Vision Identification & Multi-Photo Analysis**
   - Upload or drag-and-drop single or multiple plant photographs (leaves, bark, flowers, fruits, whole tree).
   - Combines diagnostic evidence across multiple angles.
   - Evaluates image quality (detects blurry images, low resolution, or poor outdoor lighting).

2. **Authoritative Philippine Knowledge Integration (RAG)**
   - Cross-checks AI predictions against an internal database of Philippine flora.
   - Categorizes native status: **NATIVE**, **ENDEMIC**, **INTRODUCED**, **INVASIVE**, or **CULTIVATED**.
   - Displays scientific taxonomy alongside English, Tagalog/Filipino, and local regional names.

3. **Strict Botanical & Medical Safety**
   - Clearly distinguishes **Scientifically Established Evidence** from **Traditional Ethnobotanical Uses**.
   - Prominently displays safety warnings and toxicity alerts for dangerous plants (e.g., *Jatropha curcas* / Tubang Bakod) and look-alike species.
   - Enforces scientific disclaimer rules (no medical dosage or prescription claims).

4. **Interactive "How to Verify" & Follow-Up Context**
   - Provides diagnostic field checklists to confirm identification manually.
   - Dynamically collects optional context (environment, flowers/fruit presence, milky sap) to refine identification confidence.

5. **Field Observation & Biodiversity Documentation**
   - Record observation entries with observer name, GPS coordinates, municipality/province, habitat type, tree height, and DBH (diameter at breast height).
   - Export observation datasets as **CSV** or **JSON**.

6. **Educational Mode & TESDA-Aligned Competencies**
   - Practice visual identification drills before revealing AI results.
   - Structured learning modules covering leaf morphology, native species, plant safety, and field logging.

7. **Bilingual Interface (English / Filipino)**
   - Instant language switching between English and Tagalog/Filipino across the entire user interface.

8. **Admin Control Panel & Expert Review Queue**
   - Manage species directory, aliases, safety alerts, and conservation status.
   - Submit uncertain identifications to the "Request Expert Review" queue for human verification.

---

## 🏗️ Architecture & Technology Stack

- **Backend:** Modern PHP 8.2+ (MVC Architecture)
- **Database:** MariaDB / MySQL with PDO prepared statements
- **AI Integration:** Modular service provider (`AIPlantIdentifierInterface`) supporting OpenAI (GPT-4o), Google Gemini, and offline Mock vision models
- **Frontend:** Responsive, mobile-first HTML5, CSS3, and JavaScript
- **Testing:** PHPUnit automated test suite

---

## 🚀 Installation & Quick Start

### ⚠️ Common Error Solution: `vendor/autoload.php missing`
If you encounter: `Fatal error: Uncaught Error: Failed opening required '.../vendor/autoload.php'`, you need to run `composer install` inside your project root folder to generate the vendor dependencies.

### Deployment Guides:
- 📖 [Windows / XAMPP Installation Guide](docs/INSTALLATION_XAMPP.md)
- 📖 [cPanel Shared Hosting Deployment Guide](docs/INSTALLATION_CPANEL.md)

### Linux / macOS / General Installation
1. **Requirements:** PHP 8.2+, MySQL/MariaDB, Composer.
2. **Setup `.env`:**
   ```bash
   cp .env.example .env
   ```
3. **Install Dependencies:**
   ```bash
   composer install
   ```
4. **Initialize & Seed Database:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS puno_at_halaman;"
   mysql -u root -p puno_at_halaman < database/schema.sql
   php -r "require 'vendor/autoload.php'; App\Helpers\Config::loadEnv('.env'); Database\Seeders\SeedDatabase::run();"
   ```
5. **Start Web Server:**
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
   Navigate to `http://localhost:8000` in your web browser.

---

## 🧪 Running Automated Tests

Run the PHPUnit test suite:
```bash
vendor/bin/phpunit tests/PlantIdentifierTest.php
```

---

## 🛡️ Security Measures

- **Database:** Prepared statements via PDO prevent SQL injection.
- **Uploads:** MIME-type validation, 10MB file size limits, non-executable upload storage directory, randomized filenames.
- **XSS & CSRF:** Output escaping and session CSRF middleware.
- **API Keys:** Kept strictly server-side in `.env` and never exposed to client-side JavaScript.

---

## 📄 License
This project is open source and released under the MIT License.
