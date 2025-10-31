# FMB Colombo - Laravel Application

A Laravel 11 application for Food Management Business (FMB) operations in Colombo. This application includes features for inventory management, procurement, event management, recipe management, vendor management, and more.

## Prerequisites

Before running this application, ensure you have the following installed:

- **PHP** >= 8.2
- **Composer** (PHP dependency manager)
- **Node.js** and **npm** (for frontend assets)
- **Database**: SQLite (default), MySQL, PostgreSQL, or MariaDB

### Required PHP Extensions

- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation Steps

### 1. Clone the Repository

```bash
git clone <repository-url>
cd fmb-colombo-main
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

Create a `.env` file from the template (if `.env.example` exists) or create one manually:

```bash
# On Windows PowerShell
copy .env.example .env

# On Linux/Mac
cp .env.example .env
```

If `.env.example` doesn't exist, create a `.env` file with the following minimum configuration:

```env
APP_NAME="FMB Colombo"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_LOCALE=en
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database Configuration (SQLite - Default)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# OR for MySQL/PostgreSQL/MariaDB:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=fmb_colombo
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Database Setup

#### Option A: Using SQLite (Default - Easiest)

Create the SQLite database file:

```bash
# On Windows PowerShell
New-Item -ItemType File -Path database\database.sqlite

# On Linux/Mac
touch database/database.sqlite
```

#### Option B: Using MySQL/PostgreSQL/MariaDB

1. Create a database for the application (e.g., `fmb_colombo`)
2. Update your `.env` file with database credentials (as shown in step 4)

### 7. Run Database Migrations

```bash
php artisan migrate
```

### 8. Seed the Database

This will create initial data including:
- A developer user account
- Roles and permissions
- Modules
- Designations
- Education types

```bash
php artisan db:seed
```

**Default Login Credentials:**
- **Email:** `developer@fmb.com`
- **Password:** `1234asdf@`

### 9. Create Storage Link

Link the storage directory for public access to uploaded files:

```bash
php artisan storage:link
```

### 10. Build Frontend Assets

For **development** (with hot reload):

```bash
npm run dev
```

For **production** (optimized build):

```bash
npm run build
```

## Running the Application

### Development Mode

1. **Start the Laravel development server** (in one terminal):

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000` or `http://127.0.0.1:8000`

2. **Start Vite dev server** (in another terminal, if using `npm run dev`):

```bash
npm run dev
```

### Production Mode

1. Build frontend assets:
```bash
npm run build
```

2. Optimize Laravel:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Start the server:
```bash
php artisan serve
```

Or configure with a web server like Apache/Nginx.

## Application Features

This application includes the following modules:

- **Dashboard** - Main dashboard with overview statistics
- **Management** - Various management functionalities
- **Configuration** - System configuration settings
- **Event Management** - Event creation and management
- **Procurement** - Purchase orders and vendor management
- **Stock Control** - Inventory management, goods received/issued notes
- **Reports** - Various reporting features

### Key Functionalities

- User authentication and role-based access control (RBAC)
- Inventory management
- Recipe and menu management
- Vendor and supplier management
- Purchase order processing
- Goods received/issued notes
- Event planning and management
- Location, store, and kitchen management

## Additional Commands

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Run Tests

```bash
php artisan test
# or
phpunit
```

### Create a New User (via Seeder or manually)

You can modify `database/seeders/UserSeeder.php` to add more users, or create them through the application interface after logging in.

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   - Ensure `storage/` and `bootstrap/cache/` directories are writable:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connection Errors**
   - Verify your `.env` database configuration matches your database server
   - Ensure your database server is running
   - For SQLite, ensure the database file exists and is writable

3. **Vite Assets Not Loading**
   - Make sure you've run `npm install` and `npm run dev` or `npm run build`
   - Check that the Vite dev server is running if in development mode

4. **Application Key Error**
   - Run `php artisan key:generate` to generate the application encryption key

## Technology Stack

- **Backend:** Laravel 11.x
- **PHP Version:** 8.2+
- **Frontend Build Tool:** Vite 5.x
- **JavaScript Libraries:** Axios
- **Permissions:** Spatie Laravel Permission
- **PDF Generation:** DomPDF
- **Excel Export:** Maatwebsite Excel

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
