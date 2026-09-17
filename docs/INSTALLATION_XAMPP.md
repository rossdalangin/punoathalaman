# XAMPP & Windows Installation Guide for Puno at Halaman AI

This guide explains how to install and run **Puno at Halaman AI** on Windows using **XAMPP** using the **Zero-Terminal Web Installer Wizard**.

No command line, terminal, or Composer installation is required!

---

## 🛠️ Step 1: Start XAMPP Control Panel

1. Open the **XAMPP Control Panel**.
2. Click **Start** for both **Apache** and **MySQL**.

---

## 📁 Step 2: Copy Application Files

1. Copy or extract your `punoathalaman` project folder into your XAMPP web directory:
   - Path: `C:\xampp\htdocs\punoathalaman` or `C:\xampp2\htdocs\punoathalaman`.

---

## 🧙‍♂️ Step 3: Run the Web Installation Wizard

1. Open your web browser and go to:
   ```
   http://localhost/punoathalaman/public/install.php
   ```
2. The Web Installer Wizard will perform a system requirements check (verifying PHP >= 8.2, PDO, Fileinfo, GD image library).
3. Click **Next: Database Setup**.

---

## 🗄️ Step 4: Configure Database Settings

In the installer form, enter your XAMPP MySQL credentials:
- **Database Host**: `127.0.0.1`
- **Database Port**: `3306`
- **Database Name**: `puno_at_halaman`
- **Database Username**: `root`
- **Database Password**: *(Leave blank for default XAMPP setups)*
- **AI Vision Provider**: Choose `Mock Offline Model` or enter your OpenAI / Gemini API key.

Click **Install & Seed Application**.

---

## 🎉 What Happens Automatically:
The Web Installer Wizard will:
1. Connect to MySQL and create the `puno_at_halaman` database.
2. Import all database tables and botanical schemas (`database/schema.sql`).
3. Seed the database with rich Philippine flora species (Banaba, Sambong, Lagundi, Narra, Katmon, Tubang Bakod).
4. Generate the `.env` configuration file automatically.
5. **Delete `install.php` automatically for security!**

---

## 🚀 Step 5: Open the Application

Click the button or navigate to:
```
http://localhost/punoathalaman/public
```

You are ready to identify and document Philippine plants!
