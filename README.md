# Alif-Aura - Luxury Modest Fashion E-Commerce

**Elegance in Every Layer**

A complete high-end professional e-commerce website for a luxury modest fashion brand built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- **VIP Premium Loader** - Golden door animation on page load
- **Two Collections** - Abaya & Kids
- **Full Cart System** - Add, update, remove items
- **Wishlist** - User wishlist with MySQL persistence
- **User Auth** - Login/Register with bcrypt password hashing
- **Admin Panel** - Dashboard, Products, Orders, Users
- **Responsive Design** - Desktop, Tablet, Mobile
- **PKR Currency** - Pakistani Rupee (₨)
- **WhatsApp Integration** - Floating chat button

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

## Setup

1. **Database**
   - Create MySQL database
   - Import `database/alif_aura.sql`
   ```bash
   mysql -u root -p < database/alif_aura.sql
   ```

2. **Configuration**
   - Edit `includes/db.php` with your MySQL credentials (host, dbname, username, password)

3. **Create Admin & Seed Products**
   ```bash
   php database/create_admin.php
   php seed_products.php
   ```

4. **Run**
   - Place in web root or use PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   - Visit http://localhost:8000

## Admin Login

- **Email:** admin@alifaura.com
- **Password:** admin123

## Project Structure

```
alif-aura/
├── index.php          # Home
├── shop.php           # Shop with filters
├── product.php        # Product detail
├── cart.php           # Shopping cart
├── checkout.php       # Checkout
├── login.php          # Login
├── register.php       # Register
├── wishlist.php       # User wishlist
├── about.php          # About page
├── contact.php        # Contact form
├── admin/
│   ├── dashboard.php  # Admin dashboard
│   ├── products.php   # Add/Edit/Delete products
│   ├── orders.php     # Order management
│   └── users.php      # User management
├── includes/
│   ├── config.php     # Configuration
│   ├── db.php         # Database connection
│   ├── functions.php  # Helper functions
│   ├── header.php     # Site header
│   └── footer.php     # Site footer
├── api/
│   ├── cart.php       # Cart API
│   └── wishlist.php   # Wishlist API
├── css/style.css      # Main styles
├── js/main.js         # Main scripts
└── database/
    ├── alif_aura.sql  # Database schema
    └── create_admin.php
```

## Theme

- **Primary:** Black (#0F0F0F)
- **Accent:** Gold (#D4AF37)
- **Fonts:** Playfair Display, Poppins
