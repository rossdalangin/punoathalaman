# cPanel Deployment & Installation Guide for Puno at Halaman AI

This guide provides step-by-step instructions for hosting **Puno at Halaman AI** on cPanel shared hosting using the **Zero-Terminal Web Installation Wizard** (No Terminal or Composer required).

---

## 📋 Prerequisites
- A cPanel hosting account with PHP 8.2 or higher enabled.
- Access to cPanel **File Manager**, **MySQL Database Wizard**, and **phpMyAdmin**.

---

## 📁 Step 1: Uploading Files to cPanel

1. Compress your project folder into a ZIP archive (`punoathalaman.zip`).
2. Log in to cPanel and open **File Manager**.
3. Upload `punoathalaman.zip` into `public_html/` or a subdomain folder (e.g. `puno.yourdomain.com`).
4. Extract the ZIP archive.

---

## 🗄️ Step 2: Create MySQL Database in cPanel

1. In cPanel, click **MySQL Database Wizard**.
2. **Database Name:** Create a database (e.g., `username_puno_at_halaman`).
3. **Database User:** Create a user (e.g., `username_puno_user`) and generate a password.
4. **Privileges:** Select **ALL PRIVILEGES** and confirm.

---

## 🧙‍♂️ Step 3: Run the Web Installation Wizard

1. Open your browser and navigate to the installer wizard:
   ```
   https://yourdomain.com/install.php
   ```
   *(or `https://yourdomain.com/public/install.php` depending on document root setup)*
2. Check that all server requirements show green checkmarks (PHP >= 8.2, PDO MySQL, GD Library).
3. Click **Next: Database Setup**.
4. Enter your cPanel MySQL details:
   - **Database Host**: `127.0.0.1` or `localhost`
   - **Database Name**: `username_puno_at_halaman`
   - **Database User**: `username_puno_user`
   - **Database Password**: *your_cpanel_db_password*
5. Click **Install & Seed Application**.

---

## 🔒 Automated Installation & Security Self-Deletion

The Web Installer Wizard will:
1. Connect to MySQL and import all database tables.
2. Seed Philippine flora species data (Banaba, Sambong, Lagundi, Narra, Katmon, Tubang Bakod).
3. Automatically generate your `.env` environment file.
4. **Delete `install.php` automatically from the server** upon completion to prevent unauthorized re-installation!

---

## ✅ Step 4: Testing Your Live Application

Visit your domain in your browser:
`https://yourdomain.com`

Your "Puno at Halaman AI" application is live and operational!
