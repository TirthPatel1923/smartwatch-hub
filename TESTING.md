# 🧪 SmartWatch Hub - Test Plan

Complete testing checklist to verify all features work correctly before deployment.

---

## 📋 Pre-Test Setup

### Local Setup
1. Start XAMPP (Apache + MySQL)
2. Verify database auto-creates
3. Check no error logs
4. Database: `smartwatch_db` created
5. Tables created: products, user_submissions, orders, cart, order_items

### Docker Setup
```powershell
docker-compose up -d
# Wait 30+ seconds for MySQL to initialize
```

---

## ✅ CRUD Operations Testing

### CREATE - Add Product (Admin)

**Test Case 1: Add Valid Product**
```
URL: http://localhost/SMARTWATCHES/admin.php?tab=products-form&new=1
Steps:
  1. Fill in all fields:
     - Name: "Test Watch Premium"
     - Brand: "TestBrand"
     - Category: "Premium"
     - Price: 299.99
     - Stock: 50
     - Description: "A test smartwatch with excellent features"
     - Features: "AMOLED Display,5-Day Battery,GPS,NFC"
     - Image: https://via.placeholder.com/400x400
     - Colors: "Black,Silver,Gold"
  2. Click "Save Product"
Expected Result: ✅ Success message, product added to list
```

**Test Case 2: Add Product - Validation Error**
```
Steps:
  1. Leave "Product Name" empty
  2. Click "Save Product"
Expected Result: ✅ Error: "Product name is required"
```

**Test Case 3: Duplicate Product Name**
```
Steps:
  1. Try adding product with existing name
Expected Result: ✅ Error: "Product with this name already exists"
```

**Test Case 4: Invalid Price**
```
Steps:
  1. Enter negative price: -100
  2. Click "Save Product"
Expected Result: ✅ Error: "Price must be greater than 0"
```

### READ - View Products

**Test Case 5: Home Page Listing**
```
URL: http://localhost/SMARTWATCHES/
Expected Results:
  ✅ Products displayed in grid
  ✅ Shows 5 products per page
  ✅ Pagination controls visible
  ✅ Product cards show: image, brand, name, price
  ✅ Images display correctly
  ✅ Add to Cart button visible
```

**Test Case 6: Product Details Page**
```
Steps:
  1. Click on any product
Expected Results:
  ✅ Product details page loads
  ✅ Full description displays
  ✅ Features list displays
  ✅ Color options visible (if multiple)
  ✅ Stock status shows
  ✅ Add to Cart button accessible
  ✅ Quantity selector works (1-10)
  ✅ Back button works
```

**Test Case 7: Pagination**
```
Steps:
  1. If products > 5, page 2 button appears
  2. Click page 2
  3. Click Previous
  4. Click page 1
Expected Results:
  ✅ Pagination buttons work correctly
  ✅ Products change per page
  ✅ Current page highlighted
  ✅ URL updates correctly
```

### UPDATE - Edit Product

**Test Case 8: Edit Existing Product**
```
URL: http://localhost/SMARTWATCHES/admin.php?tab=products&edit=1
Steps:
  1. Click Edit on any product
  2. Change price to 199.99
  3. Change stock to 25
  4. Click "Update Product"
Expected Results:
  ✅ Success message displays
  ✅ Product list shows updated values
  ✅ Changes reflect on product page
```

**Test Case 9: Edit - Validation**
```
Steps:
  1. Clear "Product Name"
  2. Click "Update Product"
Expected Results:
  ✅ Error message: "Product name is required"
```

### DELETE - Remove Product

**Test Case 10: Delete Product**
```
Steps:
  1. Go to Admin → Products
  2. Click Delete on a product
  3. Confirm deletion
Expected Results:
  ✅ Product removed from list
  ✅ Success message displays
  ✅ Product no longer appears on home page
  ✅ Database record deleted
```

---

## 📨 Contact Form Testing

### Form Submission

**Test Case 11: Valid Contact Form**
```
URL: http://localhost/SMARTWATCHES/contact.php
Steps:
  1. Fill all fields:
     - Name: "John Doe"
     - Email: "john@example.com"
     - Phone: "+1-234-567-8900"
     - Favorite Model: "Samsung Galaxy Watch"
     - Message: "I'm interested in purchasing this watch"
  2. Click Submit
Expected Results:
  ✅ Success message displays
  ✅ Form fields clear
  ✅ Data saved in database
  ✅ Visible in Admin → Submissions
```

**Test Case 12: Form Validation Errors**
```
Scenario A - Empty Name:
  1. Leave "Name" empty
  2. Click Submit
  Expected: ✅ Error: "Name must be at least 2 characters long"

Scenario B - Invalid Email:
  1. Email: "invalid.email"
  2. Click Submit
  Expected: ✅ Error: "Please enter a valid email address"

Scenario C - Invalid Phone:
  1. Phone: "123"
  2. Click Submit
  Expected: ✅ Error: "Phone number must be at least 10 digits"

Scenario D - Short Message:
  1. Message: "Hi"
  2. Click Submit
  Expected: ✅ Error: "Message must be at least 10 characters long"
```

**Test Case 13: Error Summary Display**
```
Steps:
  1. Leave all fields empty
  2. Click Submit
Expected Results:
  ✅ Error summary box displays
  ✅ All 5 errors listed
  ✅ aria-live="polite" for screen readers
  ✅ Errors are clear and actionable
```

**Test Case 14: Form Data Persistence**
```
Steps:
  1. Fill Name: "Jane"
  2. Leave Email empty
  3. Click Submit
Expected Results:
  ✅ Error message shows
  ✅ "Jane" still in Name field
  ✅ Other fields retain values
```

---

## 🛒 Shopping Cart Testing

**Test Case 15: Add to Cart**
```
Steps:
  1. Click on a product
  2. Change quantity to 3
  3. Click "Add to Cart"
Expected Results:
  ✅ Redirects to cart page
  ✅ Product appears in cart
  ✅ Quantity shows 3
  ✅ Price calculated correctly (product_price × 3)
  ✅ Cart badge updated with count
```

**Test Case 16: Update Quantity**
```
Steps:
  1. In cart, change quantity to 5
  2. Click Update
Expected Results:
  ✅ Total price recalculates
  ✅ Cart subtotal updates
  ✅ No page refresh needed (smooth update)
```

**Test Case 17: Remove from Cart**
```
Steps:
  1. Click Remove button on any item
Expected Results:
  ✅ Item removed from cart
  ✅ Cart total recalculates
  ✅ Empty cart shows message
```

**Test Case 18: Add Multiple Products**
```
Steps:
  1. Add Product A (qty 2)
  2. Add Product B (qty 1)
  3. View cart
Expected Results:
  ✅ Both products displayed
  ✅ Individual prices correct
  ✅ Total price = (A × 2) + B
  ✅ Can modify each independently
```

---

## 📱 Responsive Design Testing

**Test Case 19: Mobile View (480px)**
```
Steps:
  1. Open browser DevTools (F12)
  2. Set device: iPhone 12 (390px width)
  3. Test all pages
Expected Results:
  ✅ Navigation menu responsive
  ✅ Products stack vertically
  ✅ Buttons full width or appropriately sized
  ✅ Touch-friendly (44px minimum)
  ✅ Text readable
  ✅ No horizontal scrolling
  ✅ Images scale properly
```

**Test Case 20: Tablet View (768px)**
```
Steps:
  1. Set device: iPad (768px width)
  2. Test layout
Expected Results:
  ✅ 2-3 column product grid
  ✅ Sidebar visible/hidden appropriately
  ✅ All elements readable
  ✅ Forms properly sized
```

**Test Case 21: Desktop View (1200px+)**
```
Steps:
  1. Resize to full screen (1920px+)
Expected Results:
  ✅ 4-5 column product grid
  ✅ Full navigation visible
  ✅ Optimal spacing
  ✅ No text overflow
```

**Test Case 22: Responsive Typography**
```
Steps:
  1. Test on multiple screen sizes
Expected Results:
  ✅ Headings scale appropriately
  ✅ Body text readable (14px+ mobile, 16px+ desktop)
  ✅ Line height appropriate for readability
  ✅ No text too large or small
```

---

## ♿ Accessibility Testing

### Keyboard Navigation

**Test Case 23: Tab Navigation**
```
Steps:
  1. Open any page
  2. Press Tab repeatedly
Expected Results:
  ✅ Focus moves through all interactive elements
  ✅ Visible focus indicator (cyan outline)
  ✅ Tab order is logical (left to right, top to bottom)
  ✅ No focus trap
  ✅ Can reach all buttons, links, forms
```

**Test Case 24: Form Navigation**
```
Steps:
  1. Go to contact form
  2. Use only Tab and Shift+Tab to navigate
  3. Use Enter to submit
Expected Results:
  ✅ All form fields accessible
  ✅ Can see which field is focused
  ✅ Can fill form with keyboard only
  ✅ Can submit with Enter key
```

**Test Case 25: Escape Key**
```
Steps:
  1. If modals/dropdowns exist, test Escape
Expected Results:
  ✅ Closes popups/modals
  ✅ Returns focus to trigger button
```

### Screen Reader Testing

**Test Case 26: Semantic HTML**
```
Tools: NVDA (free screen reader for Windows)
Steps:
  1. Start NVDA
  2. Navigate page
Expected Results:
  ✅ "Navigation" announced
  ✅ "Main" content announced
  ✅ Headings announced with level
  ✅ Links announced as links
  ✅ Form fields announced with labels
  ✅ Buttons announced as buttons
```

**Test Case 27: ARIA Labels**
```
Steps:
  1. Check shopping cart icon
Expected Results:
  ✅ "Shopping cart" announced
  ✅ Item count announced: "3 items"
```

**Test Case 28: Error Messages**
```
Steps:
  1. Submit contact form empty
  2. Use screen reader
Expected Results:
  ✅ "Form errors" section announced
  ✅ Each error read aloud
  ✅ aria-live="polite" works
  ✅ Errors announced dynamically
```

### Color Contrast

**Test Case 29: Color Contrast**
```
Tools: WebAIM Contrast Checker
Steps:
  1. Check text on background
  2. Check links color
Expected Results:
  ✅ 4.5:1 contrast ratio minimum (text)
  ✅ 3:1 contrast ratio minimum (large text/buttons)
  ✅ Links distinguishable from text (not color alone)
```

**Test Case 30: Focus Indicators**
```
Steps:
  1. Tab through page
  2. Check focus indicator visibility
Expected Results:
  ✅ Visible focus indicator on all interactive elements
  ✅ High contrast focus outline
  ✅ Clear where focus is
```

---

## 🔐 Security Testing

**Test Case 31: CSRF Protection**
```
Steps:
  1. Try to submit form without csrf_token
Expected Results:
  ✅ Form doesn't submit
  ✅ Error: "CSRF token validation failed"
```

**Test Case 32: Input Validation**
```
Steps:
  1. Try SQL injection in form:
     Name: "'; DROP TABLE users; --"
  2. Submit
Expected Results:
  ✅ Input escaped and saved safely
  ✅ Data displays correctly
  ✅ Database not affected
```

**Test Case 33: XSS Prevention**
```
Steps:
  1. Try script injection in name:
     "<script>alert('XSS')</script>"
  2. Submit contact form
  3. Check admin panel
Expected Results:
  ✅ No alert appears
  ✅ Script stored as text
  ✅ Displayed safely in admin
```

---

## 🗄️ Database Testing

**Test Case 34: Auto Database Creation**
```
Steps:
  1. Delete smartwatch_db (if exists)
  2. Access application
  3. Database should auto-create
Expected Results:
  ✅ Database created
  ✅ All tables created
  ✅ Schema correct
  ✅ Indexes created
  ✅ Sample data inserted (if configured)
```

**Test Case 35: Data Persistence**
```
Steps:
  1. Add product via admin
  2. Restart XAMPP/containers
  3. Check if product still there
Expected Results:
  ✅ Data persists
  ✅ No data loss
  ✅ Database maintains integrity
```

---

## 🚀 Performance Testing

**Test Case 36: Page Load Speed**
```
Steps:
  1. Open DevTools → Network
  2. Load home page
  3. Check metrics
Expected Results:
  ✅ Home page < 2 seconds
  ✅ Product page < 1 second
  ✅ Admin panel < 3 seconds
```

**Test Case 37: Database Query Performance**
```
Steps:
  1. Add 100+ products
  2. Check pagination performance
Expected Results:
  ✅ Pages load quickly
  ✅ No noticeable lag
  ✅ Pagination smooth
```

---

## 📊 Admin Panel Testing

**Test Case 38: Dashboard Statistics**
```
URL: http://localhost/SMARTWATCHES/admin.php
Expected Results:
  ✅ Shows total submissions count
  ✅ Shows total orders count
  ✅ Shows total revenue
  ✅ Shows total products
  ✅ Numbers update when data changes
```

**Test Case 39: Submissions Tab**
```
Steps:
  1. Submit contact form
  2. Go to Admin → Submissions
Expected Results:
  ✅ Contact form data displayed
  ✅ Shows name, email, phone, message, date
  ✅ Can delete submission
  ✅ Table sortable/searchable (if implemented)
```

**Test Case 40: Orders Tab**
```
Expected Results:
  ✅ Shows all orders
  ✅ Order details accessible
  ✅ Can view order items
  ✅ Can delete orders
```

---

## 🌐 Browser Compatibility

**Test Case 41: Chrome 90+**
```
Expected Results: ✅ All features work
```

**Test Case 42: Firefox 88+**
```
Expected Results: ✅ All features work
```

**Test Case 43: Safari 14+**
```
Expected Results: ✅ All features work
```

**Test Case 44: Edge 90+**
```
Expected Results: ✅ All features work
```

---

## 🐳 Docker Deployment Testing

**Test Case 45: Docker Build**
```bash
docker-compose up -d
# Wait 30+ seconds
```
Expected Results:
- ✅ Web service starts
- ✅ MySQL service starts
- ✅ phpMyAdmin accessible at http://localhost:8081
- ✅ Application accessible at http://localhost:8080

**Test Case 46: Docker Database**
```bash
# Access MySQL
docker exec -it smartwatch-hub-mysql mysql -u smartwatch_user -p smartwatch_db

# Check tables
SHOW TABLES;
```
Expected Results:
- ✅ All tables present
- ✅ Data persists between restarts

---

## ✅ Submission Testing Checklist

Before final deployment:

- [ ] All CRUD operations working (Create, Read, Update, Delete)
- [ ] Contact form validates and submits
- [ ] Shopping cart functions
- [ ] Products display with pagination
- [ ] Admin panel accessible
- [ ] Forms have CSRF protection
- [ ] Input validation working
- [ ] Error messages clear and helpful
- [ ] Responsive on mobile/tablet/desktop
- [ ] Keyboard navigation working
- [ ] Screen reader compatible (basic test)
- [ ] Color contrast adequate
- [ ] Database auto-creates
- [ ] Data persists
- [ ] No sensitive data in code
- [ ] `.env` file not committed
- [ ] `.env.example` present
- [ ] README complete
- [ ] DEPLOYMENT.md complete
- [ ] Code committed to Git
- [ ] Ready for cloud deployment

---

## 📝 Test Results

| Test # | Description | Status | Notes |
|--------|-------------|--------|-------|
| 1 | Add Product | ✅ PASS | |
| 2 | Product Validation | ✅ PASS | |
| 3 | Duplicate Prevention | ✅ PASS | |
| 4 | Price Validation | ✅ PASS | |
| ... | ... | ... | |

---

**Last Updated**: April 2026  
**Total Test Cases**: 46  
**Estimated Time**: 2-3 hours (comprehensive testing)
