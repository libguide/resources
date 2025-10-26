# Library Portal - XAMPP Setup Guide

This package is ready to run under XAMPP (Windows/Mac/Linux). It contains a simple PHP + MySQL portal to upload, manage and export bibliographic records.

## Steps to run in XAMPP (Windows)

1. Extract the zip and copy the folder `library_portal_xampp` to `C:\xampp\htdocs\` so you have `C:\xampp\htdocs\library_portal_xampp\`.
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open phpMyAdmin at http://localhost/phpmyadmin and import `init.sql` (choose the file `init.sql` inside the project folder).
4. Visit http://localhost/library_portal_xampp/ in your browser.
5. Admin login at http://localhost/library_portal_xampp/admin/login.php
   - Default sample admin username: `admin`
   - Default sample password (plaintext): `ChangeMe123!` (the hash inserted in init.sql corresponds to this password). **Change it after logging in.**

## Steps for macOS / Linux with XAMPP (lampp)
1. Copy folder to `/opt/lampp/htdocs/library_portal_xampp/` (use sudo if needed).
2. Start XAMPP: `sudo /opt/lampp/lampp start`
3. Import `init.sql` via phpMyAdmin (http://localhost/phpmyadmin) or use mysql CLI:
   ```bash
   mysql -u root -p < init.sql
   ```
4. Open http://localhost/library_portal_xampp/

## Notes
- db.php is configured to use `root` with empty password (default XAMPP). If your MySQL root has a password, update `db.php` accordingly.
- CSV format for upload: `title,author,issn,subject,department,publisher,type,link`.
- Duplicate records are skipped using a unique index on (title,author,issn). Duplicate file uploads are detected via checksum.
- If you want to change the admin password, generate a bcrypt hash in PHP:
  ```bash
  php -r "echo password_hash('NewPassword', PASSWORD_BCRYPT).PHP_EOL;"
  ```
  Then update the `admins` table in phpMyAdmin with the new hash.
