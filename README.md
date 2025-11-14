# 🛍️ Lazada E-Commerce Platform

A complete PHP/MySQL e-commerce platform built from scratch with shopping cart, checkout system, and seller dashboard.

## ✨ Features

- 🛒 **Shopping Cart** - Add/remove items, quantity management
- 💳 **Complete Checkout** - Order processing with shipping & payment
- 👤 **User Authentication** - Login/register for buyers and sellers
- 📊 **Seller Dashboard** - Product management, order tracking
- 🔍 **Product Search** - Advanced filtering by category, price, tags
- 📱 **Responsive Design** - Works on desktop and mobile
- 🌙 **Dark/Light Theme** - Theme toggle functionality

## 🚀 Quick Deploy to Railway (Recommended)

### Step 1: Connect Repository
1. Go to [Railway.app](https://railway.app) and sign up with GitHub
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your `Landrocapul/Products` repository

### Step 2: Add Database
1. In your Railway project, click "Add Plugin"
2. Choose "Database" → "MySQL"
3. Railway will automatically create and configure your database

### Step 3: Set Environment Variables
Railway automatically provides `DATABASE_URL`. No manual setup needed!

### Step 4: Deploy
1. Railway will automatically build and deploy your app
2. Once deployed, click on your app URL
3. Import the database schema

## 📊 Database Setup

### Option 1: Railway Database (Recommended)
1. Open Railway project → Database → "Connect" tab
2. Copy the connection details
3. Use phpMyAdmin or command line to import `database` file

### Option 2: Local Development
```bash
# Install XAMPP/WAMP/MAMP
# Create database 'lazada'
# Import database file via phpMyAdmin
```

## 🔧 Environment Variables

Copy `.env.example` to `.env` and configure:

```env
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASS=your-database-password

# Or just set DATABASE_URL for Railway:
DATABASE_URL=mysql://username:password@host:port/database
```

## 🏗️ Project Structure

```
├── index.php          # Landing page
├── login.php          # User authentication
├── register.php       # User registration
├── shop.php           # Product browsing & cart
├── dashboard.php      # Seller dashboard
├── account.php        # User account management
├── db.php            # Database configuration
├── style.css         # Main stylesheet
├── database          # Database schema & sample data
└── README.md         # This file
```

## 🎯 Sample Data Included

- **Users**: seller1@gmail.com, consumer1@gmail.com (password: 123)
- **Categories**: Electronics, Clothing, Home & Garden, etc.
- **Products**: 10 sample products with full details
- **Orders**: Ready for testing checkout flow

## 🔐 Default Login Credentials

- **Seller**: seller1@gmail.com / 123
- **Consumer**: consumer1@gmail.com / 123

## 🌐 Alternative Deployment Options

### Heroku
```bash
heroku create your-app-name
heroku addons:create jawsdb:kitefin
git push heroku master
```

### Render
1. Connect GitHub repo
2. Choose "Web Service" + PHP
3. Add MySQL database
4. Deploy

### DigitalOcean App Platform
1. Connect GitHub repo
2. Auto-detect PHP
3. Add managed database
4. Deploy

## 🛠️ Development Setup

```bash
# Clone repository
git clone https://github.com/Landrocapul/Products.git
cd Products

# Install PHP dependencies (if any)
composer install

# Set up local database
# - Create database 'lazada'
# - Import database file
# - Update db.php with your credentials

# Start development server
php -S localhost:8000
```

## 📝 API Endpoints

- `GET /` - Landing page
- `GET /shop.php` - Product catalog
- `GET /dashboard.php` - Seller dashboard
- `POST /login.php` - User login
- `POST /register.php` - User registration

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 📞 Support

For questions or issues:
- Create an issue on GitHub
- Check the documentation
- Review the code comments

---

**Happy Shopping! 🛍️✨**

Built with ❤️ using PHP, MySQL, HTML, CSS, and JavaScript.
