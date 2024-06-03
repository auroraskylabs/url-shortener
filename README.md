# AuroraSky URL Shortener

AuroraSky URL Shortener is a simple PHP-based URL shortening service that allows users to shorten URLs and retrieve their history using browser tokens. The short URLs are in the format `https://url.aurorasky.me/go/$CODE` and can be used indefinitely.

## Features

- Generate short URLs for long URLs
- User-specific token-based URL history
- IP address logging for each shortened URL
- Simple installation and setup process

## How It Works

1. **URL Shortening**: Users can enter a long URL on the homepage and receive a shortened URL.
2. **Redirection**: The shortened URL redirects to the original URL.
3. **User Tokens**: Each user is assigned a unique token stored in their browser. This token is used to track the history of URLs they have shortened.
4. **History**: Users can view their URL shortening history using a modal that fetches data from the database based on their unique token.
5. **Installation**: The installer guides users through setting up their database and configuration.

## Installation

Follow these steps to install and configure AuroraSky URL Shortener:

### 1. Clone the Repository

```sh
git clone https://github.com/auroraskylabs/url-shortener.git
cd url-shortener
```

### 2. Setup the Database

Ensure you have a MySQL database setup and ready to use. Note down the database host, name, user, and password.

### 3. Run the Installer

Open your web browser and navigate to the `install.php` script.

```sh
http://yourdomain.com/install.php
```

Follow the steps in the installer:

1. **Database Configuration**: Enter your database host, name, user, and password.
2. **Build Database**: The installer will create the necessary database tables.
3. **Test Installation**: A test URL will be created to verify the setup.
4. **Final Step**: Remove the installation files for security.

### 4. Lock the Installer

After installation, an `.installer_lock` file is created to prevent reinstallation. If you need to reinstall, delete this file.

## File Structure

- **index.html**: The homepage where users can enter URLs to shorten.
- **shorten.php**: Handles the URL shortening logic.
- **go.php**: Redirects short URLs to their original URLs.
- **install.php**: The installation script for setting up the database and configuration.
- **init-db.php**: Creates the database table for storing URLs.
- **urlconfig.php**: Configuration file created during installation containing database connection details.

## Security

**Important**: Remove the `install.php` and `init-db.php` files after installation to prevent unauthorized reconfiguration.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
