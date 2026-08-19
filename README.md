🎓 UCSH Digital Catalogue System

The Digital Catalogue System is a Laravel 12 and MySQL project designed for the University of Computer Studies, Hinthada (UCSH). This project is officially owned by ucsh.edu.mm and hosted at [https://catalogue.ucsh.edu.mm](https://catalogue.ucsh.edu.mm). The source code repository is located at [https://github.com/AungPyaeSoneUCS/Catalogue](https://github.com/AungPyaeSoneUCS/Catalogue).

### 🚀 Overview & Architecture

This deployment architecture uses a LEMP stack optimized for the university's domain, tailored specifically for Laravel 12.

* **Web Server & Gateway:** NGINX handles incoming HTTP/HTTPS requests for `catalogue.ucsh.edu.mm` and routes them to the application's `public` directory.
* **Application Environment:** PHP 8.4-FPM processes the backend Laravel logic.
* **Database Engine:** A native MySQL database stores system data, user profiles, and catalogue records.

### 💻 Local Development Setup

To run and modify this project locally in your development environment:

1. **Clone the repository:**
```bash
git clone https://github.com/AungPyaeSoneUCS/Catalogue.git
cd Catalogue

```


2. **Install Dependencies:** Run `composer install` to pull in Laravel framework packages.
3. **Environment Setup:** Copy `.env.example` to `.env` and run `php artisan key:generate` to generate your application key.
4. **Database Configuration:**
* Create a local MySQL database named `ucsh_catalogue`.
* Import the provided database `.sql` file into this new database.
* Update the local database credentials in your `.env` file (DB_DATABASE, DB_USERNAME, DB_PASSWORD).


5. **Serve Application:** Run `php artisan serve` to start the local development server.

### 🛠️ Server Preparation & Prerequisites

Ensure required tools and PHP extensions are installed by running:

```bash
sudo apt update && sudo apt install -y curl git nano unzip ufw mysql-server
sudo apt install -y php8.4-fpm php8.4-mysql php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath

```

### 📦 Initial Server Deployment

You can deploy the application using either a direct file upload (Zip) or by cloning directly from the Git repository.

**Option A: Deployment via File Extraction (Copy File)**
Deploying the application requires uploading the packaged source code and extracting it directly into the web root.

1. Securely upload your compressed project file and move the archive to the web directory by executing `sudo mv catalog_system.zip /var/www/`.
2. Extract the files using the unzip utility via `sudo unzip /var/www/catalog_system.zip -d /var/www/catalog_system`.

**Option B: Deployment via Git Clone (Recommended)**
Cloning directly to the server makes future updates much easier.

1. Navigate to the web root:
```bash
cd /var/www/

```


2. Clone the repository directly into the target folder:
```bash
sudo git clone https://github.com/AungPyaeSoneUCS/Catalogue.git catalog_system

```


3. Install production dependencies:
```bash
cd catalog_system
sudo chown -R $USER:www-data /var/www/catalog_system
composer install --optimize-autoloader --no-dev

```



### 🗄️ Database & Environment Configuration

To prevent access denial errors, the application must connect using a dedicated MySQL user rather than the system root account.

1. Log into the database shell (`sudo mysql`) and create the user:
```sql
CREATE USER 'catalogueuser'@'localhost' IDENTIFIED BY 'Ucsh@2026';
GRANT ALL PRIVILEGES ON ucsh_catalogue.* TO 'catalogueuser'@'localhost';
FLUSH PRIVILEGES;
exit;

```


2. Update the configuration file using `nano /var/www/catalog_system/.env` and update the database variables to match the above credentials.

### 🔐 Permissions & NGINX Configuration

Applying the principle of least privilege ensures security while allowing NGINX and PHP-FPM to read files, compile caches, and accept large uploads (like payslips and user profiles).

1. **Assign Ownership:** Assign ownership of the application directory to the web server:
```bash
sudo chown -R hinthadauser:www-data /var/www/catalog_system

```


2. **Set Directory Permissions:** Set specific write permissions for Laravel's core functional directories and custom upload folders:
```bash
sudo chmod -R 775 /var/www/catalog_system/storage
sudo chmod -R 775 /var/www/catalog_system/bootstrap/cache
sudo chmod -R 775 /var/www/catalog_system/public/userProfile
sudo chmod -R 775 /var/www/catalog_system/public/payslipImage
sudo chmod -R 775 /var/www/catalog_system/public/document
sudo chmod -R 775 /var/www/catalog_system/public/image

```


3. **Link Storage:** Connect the storage directory to the public path:
```bash
cd /var/www/catalog_system && php artisan storage:link

```


4. **NGINX Setup:** Open the NGINX configuration file: `sudo nano /etc/nginx/sites-available/catalogue.ucsh.edu.mm`. Ensure the `server_name` is set to `catalogue.ucsh.edu.mm`, `client_max_body_size` is increased, and the `root` points to `/var/www/catalog_system/public`.
5. **Apply Changes:** Apply services changes by executing `sudo systemctl restart php8.4-fpm` followed by `sudo systemctl reload nginx`.

### 🔄 Server Update Workflow (Git Pull)

When new changes are pushed to the GitHub repository, use this workflow to seamlessly upgrade the live server.

1. **Navigate to the application directory:**
```bash
cd /var/www/catalog_system

```


2. **Pull the latest updates:**
```bash
sudo git pull origin main

```


*(Troubleshooting: If local file modifications block the pull, run `sudo git fetch --all` and `sudo git reset --hard origin/main` to force sync with the remote repository).*
3. **Refresh Laravel Caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear

```


4. **Reset Permissions:** Newly pulled files may inherit root ownership if pulled via `sudo`. Always re-apply safe permissions:
```bash
sudo chown -R hinthadauser:www-data /var/www/catalog_system

```



### ✅ Verification & Monitoring

Verify the deployment by navigating to `[https://catalogue.ucsh.edu.mm](https://catalogue.ucsh.edu.mm)`.

* If you encounter **500 Internal Server Errors** during file uploads, verify the folder write permissions using `chmod -R 775`.
* If you encounter **502 Bad Gateway** or unexpected behavior, monitor real-time errors via `sudo tail -f /var/log/nginx/catalogue_error.log`.
* For Laravel-specific application errors, check the internal logs via `tail -f /var/www/catalog_system/storage/logs/laravel.log`.

Developed and maintained by the University of Computer Studies, Hinthada.
