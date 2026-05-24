# ADROX - E-Commerce Website
## Luxury Meets Technology

A complete affiliate e-commerce website built with PHP and MySQL database, featuring 3D animations and product management.

---

## 📋 Project Features

✅ **Complete E-Commerce Platform**
- Affiliate product showcase
- Responsive design
- Product categories and filtering
- Fast and smooth navigation

✅ **3D Animations**
- Three.js powered 3D animations on hero section
- Smooth scroll animations
- Interactive product cards
- Modern animations throughout

✅ **Full Backend**
- PHP with MySQL database
- RESTful API for product management
- Admin panel for managing products
- Contact form with database storage

✅ **Brand Customization**
- Color palette based on Adrox logo
- Luxury-inspired design
- Professional UI/UX
- Mobile responsive

---

## 🚀 Setup Instructions

### Prerequisites
- XAMPP or any local server (Apache, PHP 7.4+, MySQL 5.7+)
- Modern Web Browser

### Step 1: Extract Files
- Extract all files to: `C:\xampp\htdocs\adrox\`

### Step 2: Start Services
1. Open XAMPP Control Panel
2. Start **Apache** module
3. Start **MySQL** module

### Step 3: Access the Website
1. Open your browser
2. Go to: `http://localhost/adrox/`

### Step 4: Initialize Database
The database will be automatically created when you first access the website. It includes:
- Products table with sample data
- Categories table
- Admin users table
- Contact messages table

---

## 🔐 Admin Panel Access

### Login Details
- **URL**: `http://localhost/adrox/admin/`
- **Username**: `admin`
- **Password**: `admin123`

### Admin Functions
- Add new products
- Edit existing products
- Delete products
- Mark products as featured
- View all products
- Set affiliate links

---

## 📁 Project Structure

```
adrox/
├── index.php                 # Main homepage
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet with Adrox brand colors
│   ├── js/
│   │   ├── 3d-animation.js  # Three.js 3D animations
│   │   └── main.js          # Main JavaScript functionality
│   └── images/
│       └── logo.png         # Place your Adrox logo here
├── admin/
│   └── index.php            # Admin dashboard
├── includes/
│   ├── config.php           # Database configuration
│   ├── functions.php        # Database functions
│   ├── api.php              # API endpoints
│   ├── db_init.php          # Database initialization
│   └── database.sql         # SQL schema
└── README.md                # This file
```

---

## 🎨 Color Palette

Based on Adrox Logo:
- **Primary Red**: `#E60012` - Main brand color
- **Dark Gray**: `#2c2c2c` - Text and backgrounds
- **Light Gray**: `#f5f5f5` - Backgrounds
- **Accent Gold**: `#d4a574` - Luxury accents
- **White**: `#ffffff` - Clean backgrounds

---

## 📝 Adding Products

### Via Admin Panel
1. Login to admin panel: `http://localhost/adrox/admin/`
2. Click "Add New Product"
3. Fill in product details:
   - Product Name
   - Description
   - Price
   - Category
   - Image URL
   - Affiliate Link (the link where users will be redirected)
   - Mark as Featured (optional)
4. Click "Save Product"

### Affiliate Link Setup
When users click the "Buy Now" button on any product, they will be redirected to the affiliate link you set. Set it to your affiliate URL.

Example: `https://amazon.in/s?k=mobile+cover&ref_=adrox`

---

## 🌐 Database Schema

### Products Table
```sql
- id (INT, Primary Key)
- name (VARCHAR 255)
- description (LONGTEXT)
- price (DECIMAL 10,2)
- category (VARCHAR 100)
- image_url (VARCHAR 500)
- affiliate_link (VARCHAR 1000) -- The URL to redirect users
- is_featured (BOOLEAN)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Categories Table
```sql
- id (INT, Primary Key)
- name (VARCHAR 100, Unique)
- description (TEXT)
- created_at (TIMESTAMP)
```

### Sample Categories
- Mobile Covers
- Car Key Covers
- Accessories
- Tech Gadgets

---

## 🔗 API Endpoints

### Get Featured Products
```
GET /includes/api.php?action=featured
```

### Get All Products
```
GET /includes/api.php?action=products
```

### Get Products by Category
```
GET /includes/api.php?action=products&category=Mobile+Covers
```

### Get Single Product
```
GET /includes/api.php?action=product&id=1
```

### Get All Categories
```
GET /includes/api.php?action=categories
```

### Contact Form Submission
```
POST /includes/api.php?action=contact
Body: { "name": "", "email": "", "message": "" }
```

---

## 🎭 3D Animation Features

The website includes interactive 3D animations powered by Three.js:
- Rotating 3D cube (Red - Adrox primary color)
- Rotating 3D sphere (Gold - Luxury accent)
- Floating animation effects
- Lighting effects with multiple light sources
- Responsive canvas that adapts to screen size

The 3D animation is displayed in the hero section background.

---

## 📱 Responsive Design

The website is fully responsive and works on:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

All components automatically adapt to screen size.

---

## 🔧 Customization

### Change Logo
1. Replace `assets/images/logo.png` with your Adrox logo
2. Update the logo height in the header CSS if needed

### Change Colors
Edit `assets/css/style.css`:
```css
:root {
  --primary-red: #E60012;
  --dark-gray: #2c2c2c;
  --light-gray: #f5f5f5;
  --accent-gold: #d4a574;
  --white: #ffffff;
}
```

### Change Database Credentials
Edit `includes/config.php`:
```php
define('DB_USER', 'root');        // MySQL username
define('DB_PASSWORD', '');        // MySQL password
define('DB_NAME', 'adrox_ecommerce');  // Database name
```

---

## 🛡️ Security Notes

⚠️ **For Development Only**
- Admin password is currently hardcoded
- SQL queries use basic escaping

🔒 **For Production**
- Use `password_hash()` for admin passwords
- Implement prepared statements
- Add SSL/HTTPS
- Implement CSRF protection
- Add input validation
- Use environment variables for sensitive data

---

## 📞 Support

For any issues or questions:
1. Check the console for JavaScript errors (F12)
2. Check XAMPP logs
3. Verify MySQL is running
4. Verify all files are in correct directories

---

## 📄 License

This project is a custom e-commerce solution for ADROX.

---

## 🎯 Next Steps

1. Replace logo.png with actual Adrox logo
2. Add your affiliate links to products
3. Customize product data
4. Add more categories if needed
5. Test on multiple devices
6. Deploy to production server

---

**Built with ❤️ for ADROX - Luxury Meets Technology**
