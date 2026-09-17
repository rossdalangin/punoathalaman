# Puno at Halaman AI - Security Architecture & Data Protection

## 1. Overview
Security and scientific safety are paramount for "Puno at Halaman AI". This document outlines the defensive measures implemented across the application layers.

## 2. Database Security & Injection Defense
- **PDO Prepared Statements**: All database queries utilize PDO parameter binding. Direct string interpolation in SQL statements is prohibited.
- **Emulated Prepares Disabled**: `PDO::ATTR_EMULATE_PREPARES => false` is enforced in `Database.php` to ensure MySQL database-level statement preparation.

## 3. File Upload Security Pipeline
Uploads (`UploadService.php`) undergo multi-stage validation before being saved:
1. **MIME-Type Validation**: File headers are inspected using PHP `finfo_file()`. Only `image/jpeg`, `image/png`, and `image/webp` are accepted. Extension spoofs are rejected.
2. **File Size Limits**: Enforces a strict 10MB file size ceiling (`MAX_UPLOAD_SIZE_MB`).
3. **Randomized Filenames**: Files are saved with cryptographically secure 32-character hex names (`bin2hex(random_bytes(16))`) to prevent path traversal and file overwrites.
4. **Non-Executable Upload Storage**: Executable files (.php, .phtml, .sh) are prohibited. Uploads directory serves media files only.

## 4. Cross-Site Scripting (XSS) & CSRF Defense
- **Output Escaping**: Dynamic text rendered in views is sanitized using `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
- **CSRF Token Middleware**: `CsrfMiddleware` generates cryptographic session-bound CSRF tokens required for POST/PATCH state modifications.

## 5. Session & Authentication Security
- **Secure Password Hashing**: User passwords are stored using `PASSWORD_BCRYPT` with high cost factors.
- **Session Protection**: `PHPSESSID` cookies enforce `HttpOnly` and `SameSite=Lax` attributes to mitigate session hijacking.
- **API Key Protection**: OpenAI and Gemini API keys are loaded server-side via `.env` and are never exposed in client JavaScript or HTTP response payloads.
