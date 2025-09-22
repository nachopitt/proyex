# Proyex Project Setup

This document outlines the steps to set up the Proyex project for local development on a new system.

## 1. Prerequisites

Before you begin, install the required system-level software.

*   **PHP 8.4 + Composer + Laravel**
    ```bash
    # https://php.new/
    /bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
*   **PHP 8.4:**
    ```bash
    # https://php.watch/articles/php-84-install-upgrade-guide-debian-ubuntu
    sudo LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php # Press enter to confirm.
    sudo apt update
    sudo apt install php8.4 php8.4-{curl,mbstring,xml,xdebug,fpm,cli,common,mysql,zip,gd,bcmath} 7zip unzip
    php -v
    ```
*   **Composer:**
    ```bash
    # https://getcomposer.org/download/
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'ed0feb545ba87161262f2d45a633e34f591ebb3381f2e0063c345ebea4d228dd0043083717770234ec00c5a9f9593792') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
    composer -V
    ```
*   **Laravel:**
    ```bash
    # https://laravel.com/docs/12.x/installation
    composer global require laravel/installer
    laravel new proyex
    php artisan -V
    ```
*   **MariaDB Server & Client:**
    ```bash
    sudo apt update
    sudo apt install mariadb-server mariadb-client
    mysql -V
    ```
*   **Node.js & npm:**
    ```bash
    # https://github.com/nvm-sh/nvm?tab=readme-ov-file#installing-and-updating
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
    nvm install node # "node" is an alias for the latest version
    nvm -v
    node -v
    npm -v
    ```

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
*   **Alternatively, to run everything at once:**
    ```bash
    composer run dev
    ```

Your application should now be running, typically at `http://127.0.0.1:8000`.

## 10. PHP Debugging with Xdebug

[Xdebug](https://xdebug.org/) is a powerful debugging tool for PHP. These instructions cover setting it up for this project.

### Install Xdebug

First, install the correct Xdebug package for your PHP version.

```bash
# Example for PHP 8.4
sudo apt install php8.4-xdebug
```

### Configure Xdebug

Next, configure Xdebug by creating a configuration file. Find your PHP configuration directory:

```bash
php --ini
```

Look for the "Scan for additional .ini files in" path (e.g., `/etc/php/8.4/cli/conf.d/`). Create a new file named `20-xdebug.ini` in that directory:

```bash
# The path may vary depending on your system
sudo nano /etc/php/8.4/cli/conf.d/20-xdebug.ini
```

Add the following content to the file. This configuration tells Xdebug to connect to a debugging client (like VS Code) on default port (9003) when a session is started.

```ini
[xdebug]
zend_extension=xdebug.so
xdebug.mode=debug,develop
;xdebug.start_with_request=trigger|yes
;xdebug.client_port=9003
```

### Configure VS Code

1.  Install the [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) extension by Xdebug in VS Code.
2.  Go to the "Run and Debug" view (Ctrl+Shift+D).
3.  Click "create a launch.json file" and select "PHP".
4.  This will create a `.vscode/launch.json` file in your project with the following default configuration:

    ```json
    {
        "version": "0.2.0",
        "configurations": [
            {
                "name": "Listen for Xdebug",
                "type": "php",
                "request": "launch",
                "port": 9003
            }
        ]
    }
    ```

### Start Debugging

1.  Set a breakpoint in your PHP code (e.g., in a controller method).
2.  Start the "Listen for Xdebug" configuration in VS Code's "Run and Debug" panel.
3.  Make a request to your application that hits the breakpoint (e.g., visit the corresponding URL in your browser).
4.  Execution should pause at your breakpoint, allowing you to inspect variables and step through the code.

### Running Tests with Xdebug

To debug your PHPUnit tests, you can run them with a special environment variable that enables Xdebug for that specific command:

```bash
# https://xdebug.org/docs/step_debug
# https://xdebug.org/docs/all_settings#start_with_request
XDEBUG_SESSION=1 php artisan test
```

### Web application debug session

To debug a web application, you can use a browser extension to start a debug session. The extension will set a cookie on the browser that tells Xdebug to start a debugging session.

Alternatively, you can initiate a debug session manually for a single request by adding `XDEBUG_SESSION=session_name` as a query parameter to the URL.

For example, to start a debug session for the URL `http://localhost:8000`, you would use the following URL:

```
http://localhost:8000?XDEBUG_SESSION=1
```

When Xdebug is activated, it will connect to the debugging client (VS Code) and you can start debugging your application.
