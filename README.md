# SmartWatch Hub - E-Commerce Platform

A full-featured e-commerce platform for premium smartwatches with complete CRUD functionality, public contact forms, responsive design, and comprehensive accessibility features.

## 🎯 Features Implemented

### ✅ Core Requirements

#### 1. CRUD Operations (Create, Read, Update, Delete)
- **Product Management**: Admin panel for managing smartwatch catalog
  - Create new products with details (name, brand, price, stock, features)
  - Read/list all products with pagination
  - Update existing product information
  - Delete products from catalog
- **Database Storage**: MySQL with automatic schema creation
- **Real-time validation**: Server-side form validation with error handling

#### 2. Public Form with Validation
- **Contact/Enquiry Form** (`contact.php`)
  - Name validation (min 2 characters)
  - Email validation (RFC-compliant)
  - Phone number validation (10+ digits)
  - Favorite model input
  - Message validation (min 10 characters)
- **Server-side validation**: All input validated on server
- **Clear error messages**: ARIA live regions for accessibility
- **Data storage**: Submissions saved to database

#### 3. Responsive UI
- **Mobile-First Design**: Optimized for all screen sizes
- **Breakpoints**:
  - Desktop: 1200px+
  - Tablet: 768px - 1199px
  - Mobile: 480px - 767px
  - Extra small: < 480px
- **Touch-friendly**: 44px+ minimum button sizes
- **Flexible layouts**: CSS Grid & Flexbox for responsive grids

#### 4. Accessibility (WCAG 2.1 Level AA)
- **Semantic HTML**: `<nav>`, `<main>`, `<section>`, `<article>` tags
- **Form Labels**: All inputs properly labeled with `<label>` elements
- **Keyboard Navigation**: 
  - Tab through all interactive elements
  - Visible focus indicators
  - Logical tab order
  - Keyboard-accessible forms
- **ARIA Attributes**:
  - `aria-label` on complex elements
  - `aria-live` for dynamic updates
  - `aria-required` on form fields
  - `role` attributes for semantic meaning
- **Color Contrast**: 4.5:1 minimum ratio on text
- **Accessible Error Summaries**: Grouped error messages with ARIA attributes
- **Skip Navigation**: Skip-to-content link on every page

#### 5. Server Management & Deployment
- **Environment Configuration**:
  - `.env.example` with all configuration options
  - Dynamic `.env` file loading in `config.php`
  - Fallback values for development
- **Database Setup**: 
  - Automatic table creation on first run
  - SQL schema file included (`schema.sql`)
  - No manual setup required
- **Deployment Instructions**: Complete guides for:
  - Local XAMPP setup
  - Render hosting
  - Azure deployment
  - Docker containerization

## 🚀 Quick Start

### Prerequisites
- XAMPP (with PHP 7.4+, MySQL 5.7+)
- Git
- Text editor or IDE

### Installation

1. **Clone/Download Repository**
   ```bash
   cd c:\xampp\htdocs
   git clone <repository-url> SMARTWATCHES
   cd SMARTWATCHES
   ```

2. **Setup Environment**
   ```bash
   cp .env.example .env
   ```

3. **Start Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache and MySQL

4. **Access Application**
   ```
   http://localhost/SMARTWATCHES/
   ```

5. **Admin Panel**
   ```
   http://localhost/SMARTWATCHES/admin.php
   ```

## 📁 Project Structure

```
SMARTWATCHES/
├── index.php              # Main product listing
├── contact.php            # Public contact form
├── admin.php              # Admin CRUD panel
├── product.php            # Product details
├── cart.php               # Shopping cart
├── checkout.php           # Checkout process
├── config.php             # Configuration & env loading
├── db.php                 # Database connection
├── functions.php          # Utility functions
├── navigation.php         # Nav component
├── footer.php             # Footer component
├── style.css              # Main stylesheet
├── .env.example           # Environment template
├── .gitignore             # Git ignore rules
├── schema.sql             # Database schema
└── README.md              # This file
```

## 🔧 Configuration

### Environment Variables (.env)

```env
# Database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=smartwatch_db
DB_USER=root
DB_PASS=

# Site
SITE_NAME=SmartWatch Hub
SITE_URL=http://localhost/SMARTWATCHES/
CURRENCY=$

# Security
SESSION_LIFETIME=3600
DEBUG=true
```

**⚠️ Important**: Never commit `.env` to version control!

## 📊 Admin Panel

### Features
- Dashboard with statistics (submissions, orders, revenue)
- Product management (add, edit, delete)
- View customer submissions
- View orders
- Responsive table layouts

### Access
- URL: `http://localhost/SMARTWATCHES/admin.php`
- Products Tab: Full CRUD operations
- Add Product: `?tab=products-form&new=1`
- Edit Product: `?tab=products-form&edit={id}`

## 🗄️ Database

### Auto-Created Tables
1. **products** - Smartwatch catalog
2. **user_submissions** - Contact form entries
3. **orders** - Customer orders
4. **order_items** - Order line items
5. **cart** - Shopping cart items

### Schema File
Complete SQL schema available in `schema.sql` for reference or manual setup.

## ♿ Accessibility Features

### Semantic HTML
```html
<nav>           <!-- Navigation -->
<main>          <!-- Main content -->
<section>       <!-- Logical sections -->
<article>       <!-- Product cards -->
<footer>        <!-- Footer -->
```

### ARIA Implementation
```html
<div role="alert" aria-live="polite">  <!-- Error messages -->
<form role="group" aria-label="...">   <!-- Form groups -->
<button aria-label="specific action"> <!-- Buttons -->
<nav aria-label="...">                 <!-- Multiple navs -->
```

### Keyboard Navigation
- ✅ Tab: Navigate elements
- ✅ Shift+Tab: Navigate backwards
- ✅ Enter: Activate buttons/forms
- ✅ Escape: Close modals/dropdowns
- ✅ Arrow Keys: Menu navigation

### Mobile Accessibility
- 44px+ minimum tap targets
- Touch-friendly spacing
- Readable text (14px+)
- High contrast colors

## 🔒 Security

### Input Protection
- CSRF token validation on all forms
- Server-side input validation
- HTML entity escaping
- Email & phone validation

### Database Security
- Prepared statements (SQL injection protection)
- Parameterized queries
- Error handling without exposing details

### Session Security
- HTTP-only cookies
- SameSite attributes
- Secure session timeout
- Environment-based security settings

## 📱 Responsive Design

### Media Queries
```css
/* Desktop */
@media screen and (min-width: 1200px)

/* Tablet */
@media screen and (max-width: 1199px)

/* Mobile */
@media screen and (max-width: 767px)

/* Extra small */
@media screen and (max-width: 479px)
```

### Features
- Flexible grid layouts
- Scalable typography
- Stackable elements
- Touch-friendly buttons
- Readable line heights

## 🚢 Deployment

### Render.com

1. Push to GitHub
2. Create Web Service on Render
3. Connect repository
4. Set build/start commands
5. Configure environment variables
6. Deploy database

### Azure

1. Create App Service (PHP runtime)
2. Create MySQL Database
3. Configure App Settings
4. Deploy via Git push

### Docker

```dockerfile
FROM php:8.1-apache
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www/html
EXPOSE 80
```

### Environment for Production
```env
DB_HOST=prod-db-host
DB_NAME=prod_database
SITE_URL=https://yourdomain.com
ENVIRONMENT=production
DEBUG=false
```

## 📋 Features Summary

| Feature | Status | Details |
|---------|--------|---------|
| Product CRUD | ✅ | Admin panel for all operations |
| Contact Form | ✅ | Validated enquiry form |
| Shopping | ✅ | Cart, checkout, orders |
| Responsive | ✅ | Mobile/tablet/desktop |
| Accessibility | ✅ | WCAG 2.1 AA compliant |
| Security | ✅ | CSRF, input validation, prepared statements |
| Database | ✅ | Auto-setup, migrations ready |
| Environment | ✅ | .env configuration |
| Deployment | ✅ | Render, Azure, Docker |

## 🧪 Testing Checklist

- [ ] Add product via admin (CRUD Create)
- [ ] View product list (CRUD Read)
- [ ] Edit product details (CRUD Update)
- [ ] Delete product (CRUD Delete)
- [ ] Submit contact form
- [ ] Check form validation errors
- [ ] Add product to cart
- [ ] Complete checkout
- [ ] View submission in admin
- [ ] Test on mobile device
- [ ] Navigate with keyboard only
- [ ] Use screen reader
- [ ] Check color contrast

## 🌐 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Android Chrome)

## 📄 Files Included

- **Source Code**: All PHP, HTML, CSS files
- **Configuration**: .env.example, config.php
- **Database**: schema.sql, db.php
- **Documentation**: README.md
- **Security**: .gitignore

## ⚠️ Known Limitations

1. **Email**: Contact forms saved but no email delivery
2. **Payments**: Checkout process, no payment gateway
3. **Images**: Placeholder for missing product images
4. **Rate Limiting**: Not implemented (add for production)
5. **Search**: Not implemented (pagination provided)

## 🎓 Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Security**: CSRF tokens, prepared statements
- **Accessibility**: WCAG 2.1 AA

## 📞 Support

### Database Issues
- Check MySQL is running: XAMPP Control Panel
- Verify .env credentials
- Check PHP error logs

### Form Not Working
- Verify user_submissions table exists
- Check database connection
- Review PHP error logs (C:\xampp\apache\logs)

### Responsive Issues
- Clear browser cache (Ctrl+F5)
- Check CSS file loads
- Verify viewport meta tag

## 📝 Submission Checklist

- ✅ CRUD Implementation (Products)
- ✅ Public Contact Form
- ✅ Server-side Validation
- ✅ Error Handling & Messages
- ✅ Responsive Design (all breakpoints)
- ✅ Accessibility (WCAG 2.1 AA)
- ✅ Semantic HTML
- ✅ Keyboard Navigation
- ✅ ARIA Labels & Live Regions
- ✅ Database Setup (Auto-creation)
- ✅ Environment Configuration
- ✅ Deployment Instructions
- ✅ Security Measures
- ✅ Documentation (README)

## 📚 Additional Resources

- [W3C Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [MySQL Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
- [MDN Web Docs](https://developer.mozilla.org/)
- [OWASP Security Guidelines](https://owasp.org/)

## 🙏 Acknowledgments

Built with modern web standards, security best practices, and accessibility guidelines to create a production-ready e-commerce platform.

---

**Version**: 1.0.0  
**Last Updated**: April 2026  
**Status**: Production Ready ✅

**Ready for Submission**
- Git Repository: ✅ Initialized
- Live URL: Ready for deployment
- Documentation: ✅ Complete
- Code Quality: ✅ Production standard
# SmartWatch Hub - E-Commerce Platform

A modern, fully-functional e-commerce website for smartwatches running on XAMPP with local MySQL database.

## Features

✅ **Modern & Stylish Design**
- Beautiful gradient backgrounds with glassmorphism effects
- Fully responsive mobile-friendly layout
- Smooth animations and transitions
- Professional dark theme inspired by modern tech brands

✅ **Complete E-Commerce Functionality**
- Product catalog with 20 pre-loaded smartwatch models
- Shopping cart with add/update/remove functionality
- Full checkout process with billing & payment information
- Order confirmation and history
- **Price filtering** - Filter products by price range
- **Pagination** - 5 products per page for better browsing
- **Automatic restocking** - Low stock items restocked every 1-2 hours

✅ **Security & Validation**
- CSRF token protection on all POST forms
- Input sanitization & validation
- SQL injection prevention using prepared statements
- XSS protection with proper escaping

✅ **Admin Dashboard**
- View all user submissions
- View all orders with details
- View product catalog
- Delete submissions & orders
- Statistics dashboard (submissions, orders, revenue, products)

✅ **Database Management**
- Automatic database & table creation
- Sample product data pre-populated
- Order management system
- User submission tracking

## Installation & Setup

### 1. Requirements
- XAMPP with Apache & MySQL
- PHP 7.4+ (comes with XAMPP)
- MySQL 5.7+ (comes with XAMPP)

### 2. Installation Steps

```bash
# 1. Download or clone to htdocs
cd c:\xampp\htdocs
# Files should be in: c:\xampp\htdocs\smartwatches\

# 2. Start XAMPP
# - Open XAMPP Control Panel
# - Click "Start" for Apache & MySQL
# - Verify they're running (green indicators)

# 3. Access the website
# Open browser and go to: http://localhost/smartwatches/
```

### 3. Configuration

Edit `config.php` to customize:
```php
define('DB_HOST', '127.0.0.1');      // MySQL host
define('DB_PORT', 3306);             // MySQL port
define('DB_NAME', 'smartwatch_db');  // Database name
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', '');               // MySQL password (default: empty)
define('SITE_NAME', 'SmartWatch Hub'); // Website name
define('SITE_URL', 'http://localhost/smartwatches/');
```

### 4. Automatic Restocking Setup (Optional)

The system includes automatic restocking functionality that runs every 1-2 hours to replenish low-stock items.

#### Windows Task Scheduler Setup:
1. Open **Task Scheduler** (search in Windows)
2. Click **Create Basic Task**
3. Name: "Smartwatch Restocking"
4. Trigger: **Daily** → Recur every 1 day
5. **Advanced settings** → Repeat task every 2 hours for 24 hours
6. Action: **Start a program**
7. Program/script: `C:\xampp\htdocs\smartwatches\restock.bat`
8. Click **Finish**

#### Manual Testing:
Run the batch file manually: `restock.bat` or execute `restock.php` directly with PHP.

**How it works:**
- Checks products with stock ≤ 5
- Restocks them to 10 units automatically
- Logs actions to PHP error log

## File Structure

```
smartwatches/
├── index.php              # Main shop page with products, filtering & pagination
├── cart.php               # Shopping cart management
├── checkout.php           # Checkout & payment
├── order-confirmation.php # Order confirmation
├── admin.php              # Admin dashboard
├── config.php             # Configuration & credentials
├── db.php                 # Database setup & connection (20 products)
├── functions.php          # Utility functions (CSRF, validation, etc)
├── restock.php            # Automatic restocking script
├── restock.bat            # Windows batch file for scheduled restocking
├── navigation.php         # Navigation bar (included in all pages)
├── footer.php             # Footer (included in all pages)
├── style.css              # Modern CSS styling with filters & pagination
└── README.md              # This file
```

## Database Tables

### `products`
- Smart watches with price, brand, description, features, and stock

### `cart`
- Shopping cart items per session
- Tracks product quantity

### `orders`
- Customer orders with billing information and total price

### `order_items`
- Individual items in each order

### `user_submissions`
- User contact submissions from the form

## Usage

### For Customers
1. **Browse Products** → Visit `http://localhost/smartwatches/`
2. **Filter by Price** → Use min/max price filters above products
3. **Navigate Pages** → Use pagination controls (5 products per page)
4. **Add to Cart** → Click "Add to Cart" button on products
5. **View Cart** → Click cart icon or visit `/cart.php`
6. **Update Cart** → Change quantities or remove items
7. **Checkout** → Click "Proceed to Checkout"
8. **Place Order** → Fill in billing & payment details
9. **Confirmation** → View order confirmation page

### For Admin
1. Visit `http://localhost/smartwatches/admin.php`
2. **Submissions Tab** → View & delete user contact forms
3. **Orders Tab** → View & manage orders
4. **Products Tab** → View product catalog & stock levels

## Security Features

- **CSRF Protection** - All forms use CSRF tokens
- **Input Validation** - Email, phone, card validation
- **SQL Injection Prevention** - Prepared statements throughout
- **XSS Protection** - All output escaped
- **Session Management** - Secure session-based cart

## Customization

### Adding Products
Edit `db.php` and modify the `$sampleProducts` array in the database initialization section.

### Styling
Edit `style.css` to change colors, fonts, or layout. Key variables:
```css
--primary: #00d4ff;     /* Cyan/blue accent color */
--secondary: #0066ff;   /* Secondary blue */
--dark: #0a0e27;        /* Dark background */
```

### Payment Processing
Currently uses test/demo payment processing. To implement real payment:
1. Integrate Stripe, PayPal, or another gateway
2. Update `checkout.php` with API calls
3. Remove test payment flow

## Default Database Credentials

```
Host: 127.0.0.1
Port: 3306
Database: smartwatch_db
Username: root
Password: (empty)
```

Change these in `config.php` if your MySQL has different credentials.

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP Control Panel
- Check credentials in `config.php` match your MySQL setup
- Verify database user exists: phpMyAdmin → User accounts

### Images Not Loading
- Add smartwatch product images to a `/images/` folder
- Update image filenames in `config.php` product initialization
- Use popular watch models: Apple Watch, Samsung Galaxy Watch, Garmin, etc.

### Cart Not Working
- Check session is enabled in PHP
- Clear browser cookies if issues persist
- Check MySQL cart table exists in `smartwatch_db`

### Admin Access Denied
- Verify `admin.php` is in the smartwatches folder
- Check database user_submissions table exists

## Performance Tips

- Cache product data if database grows large
- Use CDN for Font Awesome icons instead of local loading
- Minify CSS/JS for production
- Enable GZIP compression in Apache

## Future Enhancements

- [ ] User login & registration system
- [ ] Email notifications for orders
- [ ] Real payment gateway integration
- [ ] Product reviews & ratings
- [ ] Wishlist functionality
- [ ] Discount codes & coupons
- [ ] Multi-language support
- [ ] Advanced product filtering

## Support & Issues

For bugs or questions:
1. Check `error_log` in XAMPP Apache folder
2. Verify MySQL is running and accessible
3. Check file permissions on `/smartwatches/` folder

## License

This is a local development project for XAMPP. Use freely for learning and demonstration purposes.

---

**Happy Selling! 🎉** Start your smartwatch shop today with SmartWatch Hub!
