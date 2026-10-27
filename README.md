# Student Information System (PHP + MySQL)

## Included
- Login/logout with PHP sessions and password hashing
- Dashboard counts
- Department CRUD
- Course CRUD
- Student CRUD and status updates
- Student academic history CRUD
- PDO prepared statements, CSRF tokens, validation, basic logging
- MySQL schema and setup script

## Local setup
1. Create a MySQL database.
2. Import `database/schema.sql`.
3. Edit `config/database.php` with database credentials.
4. Create the first admin using the one-time setup script described below.

## Create the first administrator
Temporarily upload/open `setup_admin.php` in your browser after configuring the database.
It creates `admin` / `admin123` only if no admin account exists.
Immediately remove `setup_admin.php` after it reports success and change the password using the database or a future account-management page.

## InfinityFree deployment
Upload the contents of this folder into `htdocs`. Create a MySQL database in the hosting control panel, import `database/schema.sql` with phpMyAdmin, then update `config/database.php` using the database host/name/user/password shown by the host.
Do not publish real student data or database credentials.
