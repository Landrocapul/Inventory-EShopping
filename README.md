# MALL OF CAP - E-Commerce Platform

A modern, feature-rich e-commerce platform built with PHP, MySQL, Bootstrap 5, and modern web technologies.

## 🚀 Features

- **Beautiful Landing Page** - Modern, responsive design with animations
- **User Authentication** - Separate roles for sellers and consumers
- **Seller Dashboard** - Complete inventory management system
- **Product Management** - Add, edit, delete products with categories and tags
- **Shopping Experience** - Browse products, add to cart, checkout
- **Dark Mode Support** - Built-in theme switching
- **Responsive Design** - Works on all devices

## 🛠️ Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or XAMPP/WAMP

### 1. Database Setup

**Option A: Automatic Setup**
1. Place all files in your web server's root directory
2. Open `http://localhost/setup.php` in your browser
3. The database will be created automatically

**Option B: Manual Setup**
1. Create a MySQL database named `lazada`
2. Import the `database.sql` file into your database
3. Update database credentials in `db.php` if needed

### 2. Configuration

The default database configuration in `db.php` is:
```php
$host = "localhost";
$dbname = "lazada";
$username = "root";
$password = "";
```

Update these values if your database configuration is different.

### 3. Access the Application

- **Landing Page**: `http://localhost/index.php`
- **Seller Dashboard**: `http://localhost/dashboard.php` (after login)
- **Shop**: `http://localhost/shop.php` (after login)

### 4. Test Accounts

After setup, you can use these test accounts:

**Seller Account:**
- Email: `seller1@gmail.com`
- Password: `password123`

**Consumer Account:**
- Email: `consumer1@gmail.com`
- Password: `password123`

## 📁 File Structure

```
├── index.php          # Landing page
├── dashboard.php      # Seller dashboard
├── shop.php           # Shopping interface
├── account.php        # User account management
├── categories.php     # Category management
├── product.php        # Product details
├── db.php             # Database configuration
├── style.css          # Main stylesheet
├── database.sql       # Database schema
├── setup.php          # Database setup script
└── logout.php         # Logout functionality
```

## 🎨 Features Overview

### Landing Page (`index.php`)
- Hero section with call-to-action buttons
- Feature showcase
- Statistics display
- Product preview
- Authentication modal

### Seller Dashboard (`dashboard.php`)
- Product management (CRUD operations)
- Category management
- Sales analytics
- Inventory alerts
- Bulk actions

### Shopping Interface (`shop.php`)
- Product browsing and filtering
- Shopping cart functionality
- User account management

## 🔧 Troubleshooting

### Landing Page Not Working
1. Check if database is set up: Run `setup.php`
2. Verify database connection in `db.php`
3. Check PHP error logs

### Authentication Issues
1. Ensure database tables are created
2. Verify user accounts exist in database
3. Check password hashing (uses PHP's `password_hash()`)

### Permission Issues
- Ensure web server has write permissions for the directory
- Check file permissions for PHP session handling

## 📝 API Endpoints

The application uses standard PHP POST requests for:
- User registration/login
- Product CRUD operations
- Category management
- Cart operations

## 🎯 Development

To contribute or modify:
1. Clone the repository
2. Set up the database using `setup.php`
3. Make changes to PHP files
4. Test thoroughly
5. Update documentation as needed

## 📄 License

This project is open source and available under the MIT License.

---

**Built with ❤️ using PHP, MySQL, Bootstrap 5, and modern web technologies.**
