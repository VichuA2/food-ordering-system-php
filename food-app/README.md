# Laravel User & Blog CRUD Application

A Laravel application with User and Blog CRUD operations, built with Laravel UI (Bootstrap). Includes role-based access control (RBAC) using Spatie Laravel Permission package.

## Requirements

- XAMPP (includes PHP >= 8.2 and MySQL)
- Composer

## Installation

### 1. Extract the project zip file

1. **Clone this Repository** to your XAMPP `htdocs` directory:
   - **Windows**: `C:\xampp\htdocs\`
   - **macOS/Linux**: `/Applications/XAMPP/htdocs/` or your XAMPP installation path

2. **Rename the extracted folder** (if needed) to your preferred project name (e.g., `laravel-app`)

3. **Open a terminal/command prompt** and navigate to the project directory:
   ```bash
   cd C:\xampp\htdocs\laravel-app
   ```
   Or on macOS/Linux:
   ```bash
   cd /Applications/XAMPP/htdocs/laravel-app
   ```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Start XAMPP services

1. **Open XAMPP Control Panel**
2. **Start Apache** (click the "Start" button)
3. **Start MySQL** (click the "Start" button)

### 4. Environment setup

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Update the `.env` file with your MySQL database credentials. For XAMPP, use these default settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_mail_address
MAIL_PASSWORD=app_password_your_mail_address
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_mail_address
MAIL_FROM_NAME="${APP_NAME}"
```



> **Note**: XAMPP's default MySQL username is `root` with no password. If you've set a password, update `DB_PASSWORD` accordingly.

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Database setup

Create a MySQL database using phpMyAdmin:

1. **Open phpMyAdmin** in your browser: `http://localhost/phpmyadmin`
2. **Click on "New"** in the left sidebar
3. **Enter database name**: `laravel_app` (or the name you used in `.env`)
4. **Click "Create"**

Alternatively, you can create the database using SQL:

1. Go to the **SQL** tab in phpMyAdmin
2. Run this command:
   ```sql
   CREATE DATABASE laravel_app;
   ```

Make sure the database name matches the `DB_DATABASE` value in your `.env` file (see step 4).

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Seed the database (recommended)

This will create roles, permissions, admin user, sample users, and articles:

```bash
php artisan db:seed
```

Or run migrations and seed together:

```bash
php artisan migrate:fresh --seed
```

**Note**: After seeding, 
you can login with admin account:
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

you can login with Employee account:
- **Email**: `employee@gmail.com`
- **Password**: `12345678`

you can login with Hotel Owner account:
- **Email**: `hotel@gmail.com`
- **Password**: `12345678`

you can login with Delivery Person account:
- **Email**: `delivery@gmail.com`
- **Password**: `12345678`

you can login with User account:
- **Email**: `user@gmail.com`
- **Password**: `12345678`

The seeder creates:
- **Roles**: Admin: Full system access (All permissions), Employee: Manage users, queries, and registered hotels, Hotel Owner: Manage products for their hotels and view sales reports, Delivery Person: Manage and update assigned delivery orders, Customer: Place orders, browse hotels, and manage profile.
- **Permissions**: Comprehensive CRUD permissions for: User Management (Customers, Hotel Owners, Delivery Persons), Hotel Management (Listings and Details), Menu & Product Management (Categories and Food Items), Order Processing (Checkout and Status Tracking), Support & Communication (Customer Queries and Notifications)
- **Sample Data**: Pre-configured dummy accounts for all roles (Admin, Owner, Delivery, Customer) for immediate system testing. Sample hotels, food categories, and initial product listings to visualize the storefront.
## Running the Application

### Start the development server

```bash
php artisan serve
php artisan queue:work 
php artisan schedule:work
```


The application will be available at `http://localhost:8000`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).