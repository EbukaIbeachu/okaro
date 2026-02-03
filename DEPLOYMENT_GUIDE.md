# Deployment Guide for InfinityFree (Shared Hosting)

Follow these steps to deploy **Okaro & Associates** to InfinityFree.

## 1. Prepare Your Database
1.  Log in to your InfinityFree Control Panel.
2.  Go to **MySQL Databases** and create a new database (e.g., `epiz_xxxx_okaro`).
3.  Note down the:
    *   **MySQL Host Name** (e.g., `sql123.infinityfree.com`)
    *   **MySQL User Name** (e.g., `epiz_xxxx`)
    *   **MySQL Password** (your vPanel password)
    *   **Database Name**
4.  Go to **phpMyAdmin** in the control panel.
5.  Select your new database.
6.  Click **Import**.
7.  Upload the `okaro_production.sql` file located in your project root.

## 2. Prepare Your Files
InfinityFree uses `htdocs` as the public folder. We need to structure the Laravel app so the `public` folder contents go into `htdocs`, and the rest of the app sits outside (or protected).

**Option A (Recommended for Free Hosting):**
Since you might not be able to access folders outside `htdocs` easily, we will put everything in `htdocs` but protect sensitive files.

1.  **Upload Files**:
    *   Use an FTP client (like FileZilla) to connect to your InfinityFree account.
    *   Upload **ALL** files and folders from your local `okaro` folder into the `htdocs` folder on the server.
    *   *Note: Excluding `node_modules` and `.git` folder is recommended to save space and time.*

2.  **Configure Document Root**:
    *   Move everything from the `public` folder **directly into** `htdocs`.
    *   (e.g., `htdocs/public/index.php` becomes `htdocs/index.php`).
    *   Update `htdocs/index.php`:
        ```php
        // Change:
        require __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        
        // To:
        require __DIR__.'/vendor/autoload.php';
        $app = require_once __DIR__.'/bootstrap/app.php';
        ```

3.  **Secure Sensitive Files**:
    *   Create or edit the `.htaccess` file in `htdocs` to prevent access to `.env` and other core folders.
    *   (A ready-to-use `.htaccess` file has been created for you in the project root named `.htaccess.production`).

## 3. Configure Environment
1.  Rename `.env.example` to `.env` on the server (or upload your local `.env`).
2.  Edit `.env` on the server:
    *   Set `APP_ENV=production`
    *   Set `APP_DEBUG=false`
    *   Set `APP_URL=http://your-domain.infinityfreeapp.com`
    *   Update Database credentials (from Step 1):
        ```env
        DB_CONNECTION=mysql
        DB_HOST=sqlxxx.epizy.com
        DB_PORT=3306
        DB_DATABASE=epiz_xxxx_okaro
        DB_USERNAME=epiz_xxxx
        DB_PASSWORD=your_password
        ```

## 4. Final Checks
*   Visit your website URL.
*   If you see a "500 Server Error", check if the `.env` file has correct permissions (644) and correct credentials.
*   Ensure the PHP version in InfinityFree control panel is set to 8.1 or higher (Laravel 10 requirement).

## Troubleshooting
*   **Symlink Error**: If images don't show up, you might need to manually copy `storage/app/public` contents to `public/storage` (since `php artisan storage:link` won't work).
