# Proyex Project Setup

This document outlines the steps to set up the Proyex project for local development on a new system.

## 1. Prerequisites

Before you begin, install the required system-level software.

*   **PHP 8.4:**
    ```bash
    /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
    ```
*   **MariaDB Server & Client:**
    ```bash
    sudo apt update
    sudo apt install mariadb-server mariadb-client-core-10.6
    ```
*   **Node.js & npm:** (Installation methods vary, e.g., using `nvm` or `apt`)
*   **Composer:** (Typically installed after PHP)


## 2. Initial Project Setup

First, get the base Laravel project files. If starting from scratch, this would be via a command like `composer create-project laravel/laravel proyex`.

## 3. Create Environment File

You need to create the `.env` file first, as the installation scripts may rely on it.

```bash
# Copy the example environment file
cp .env.example .env
```

## 4. Install Dependencies & Generate Key

Install all dependencies. After Composer is finished, you can then generate the application key.

```bash
# Install PHP dependencies
composer install

# Generate a unique application key
php artisan key:generate

# Install JavaScript dependencies
npm install
```

## 5. Database Setup

These steps create the database and a dedicated user for the application.

```bash
# Start the database service (for WSL)
sudo service mysql start

# Log in to the database server
mysql -u root -p
```

Once logged into the `mysql>` prompt, run the following SQL commands:

```sql
CREATE DATABASE proyex;
CREATE USER 'proyex'@'localhost' IDENTIFIED BY 'your_password'; -- Replace with a secure password
GRANT ALL PRIVILEGES ON proyex.* TO 'proyex'@'localhost';
FLUSH PRIVILEGES;
exit;
```

## 6. Update `.env` with Database Credentials

Open the `.env` file and update the database variables to match what you created in the previous step.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyex
DB_USERNAME=proyex
DB_PASSWORD=your_password
```

## 7. Run Initial Database Migrations

This command creates the initial tables (users, etc.) in your database.

```bash
php artisan migrate
```

## 8. Initialize Git Repository (Optional)

To place your project under version control.

```bash
git init
git add .
git commit -m "feat: Initial commit of Laravel project"
```

## 9. Run the Development Servers

You need two terminal sessions for this.

*   **In terminal 1 (Vite Frontend Server):**
    ```bash
    npm run dev
    ```
*   **In terminal 2 (Laravel Backend Server):**
    ```bash
    php artisan serve
    ```

Your application should now be running, typically at `http://127.0.0.1:8000`.
