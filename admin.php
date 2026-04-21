<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$success = '';
$tab = $_GET['tab'] ?? 'dashboard';
$editingProduct = null;

// ==================== PRODUCT CRUD OPERATIONS ====================

// Handle add/update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_product') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed';
    } else {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $features = trim($_POST['features'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $colors = trim($_POST['colors'] ?? '');

        $fieldErrors = [];
        if (empty($name)) $fieldErrors[] = 'Product name is required';
        if (empty($brand)) $fieldErrors[] = 'Brand is required';
        if (empty($category)) $fieldErrors[] = 'Category is required';
        if ($price <= 0) $fieldErrors[] = 'Price must be greater than 0';
        if ($stock < 0) $fieldErrors[] = 'Stock cannot be negative';
        if (empty($description)) $fieldErrors[] = 'Description is required';

        if (!empty($fieldErrors)) {
            $errors = array_merge($errors, $fieldErrors);
        } else {
            try {
                if ($id > 0) {
                    // Update existing product
                    $stmt = $pdo->prepare("
                        UPDATE products 
                        SET name = ?, brand = ?, category = ?, price = ?, stock = ?, 
                            description = ?, features = ?, image = ?, colors = ?
                        WHERE id = ?
                    ");
                    if ($stmt->execute([$name, $brand, $category, $price, $stock, $description, $features, $image, $colors, $id])) {
                        $success = 'Product updated successfully!';
                        $tab = 'products';
                    } else {
                        $errors[] = 'Failed to update product';
                    }
                } else {
                    // Create new product
                    $stmt = $pdo->prepare("
                        INSERT INTO products (name, brand, category, price, stock, description, features, image, colors) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    if ($stmt->execute([$name, $brand, $category, $price, $stock, $description, $features, $image, $colors])) {
                        $success = 'Product created successfully!';
                        $tab = 'products';
                    } else {
                        $errors[] = 'Failed to create product';
                    }
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false) {
                    $errors[] = 'Product with this name already exists';
                } else {
                    $errors[] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Handle delete product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed';
    } else {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Product deleted successfully!';
                $tab = 'products';
            } else {
                $errors[] = 'Failed to delete product';
            }
        }
    }
}

// Get product for editing if requested
if (isset($_GET['edit']) && $_GET['edit'] > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([intval($_GET['edit'])]);
    $editingProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($editingProduct) {
        $tab = 'products-form';
    }
}

// Handle delete submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_submission') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed';
    } else {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM user_submissions WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Submission deleted successfully!';
            }
        }
    }
}

// Handle delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_order') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'CSRF token validation failed';
    } else {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success = 'Order deleted successfully!';
            }
        }
    }
}

// Get data
$submissions = $pdo->query("SELECT * FROM user_submissions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get orders
$orders = $pdo->query("
    SELECT o.*, COUNT(oi.id) as item_count, SUM(oi.quantity) as total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get products
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats = [
    'total_submissions' => count($submissions),
    'total_orders' => count($orders),
    'total_revenue' => $pdo->query("SELECT SUM(total_price) as total FROM orders")->fetch()['total'] ?? 0,
    'total_products' => count($products),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel - <?php echo esc(SITE_NAME); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        .admin-nav { display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid rgba(0, 212, 255, 0.2); flex-wrap: wrap; }
        .admin-nav a { padding: 1rem; color: #cbd5e1; border-bottom: 3px solid transparent; transition: all 0.3s; cursor: pointer; }
        .admin-nav a.active { color: var(--primary); border-bottom-color: var(--primary); }
        .admin-nav a:hover { color: var(--primary); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 1.5rem; text-align: center; }
        .stat-card h3 { color: var(--primary); margin-bottom: 0.5rem; font-size: 1rem; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: var(--primary); }
        .admin-table { margin-top: 2rem; }
        .admin-form { background: rgba(30, 41, 59, 0.4); border: 2px solid rgba(0, 212, 255, 0.1); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: var(--primary); font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 6px; color: #e2e8f0; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
        .form-actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        table { width: 100%; border-collapse: collapse; }
        table thead { background: rgba(0, 212, 255, 0.1); }
        table th, table td { padding: 1rem; text-align: left; border-bottom: 1px solid rgba(0, 212, 255, 0.2); }
        table tbody tr:hover { background: rgba(0, 212, 255, 0.05); }
        .action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .table-container { overflow-x: auto; }
        .error-summary { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.5); border-radius: 6px; padding: 1rem; margin-bottom: 1rem; color: #fca5a5; }
        .error-summary h4 { margin-top: 0; }
        .error-summary ul { margin: 0; padding-left: 1.5rem; }
    </style>
</head>
<body>
<?php include 'navigation.php'; ?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1 style="margin: 0;">Admin Panel</h1>
        <a href="index.php" class="btn btn-small"><i class="fas fa-arrow-left"></i> Back to Shop</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo esc($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-summary" role="alert" aria-live="polite">
            <h4 style="margin-bottom: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Errors:</h4>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><i class="fas fa-message"></i> Submissions</h3>
            <div class="value"><?php echo intval($stats['total_submissions']); ?></div>
        </div>
        <div class="stat-card">
            <h3><i class="fas fa-shopping-bag"></i> Orders</h3>
            <div class="value"><?php echo intval($stats['total_orders']); ?></div>
        </div>
        <div class="stat-card">
            <h3><i class="fas fa-dollar-sign"></i> Revenue</h3>
            <div class="value"><?php echo formatPrice($stats['total_revenue']); ?></div>
        </div>
        <div class="stat-card">
            <h3><i class="fas fa-watch"></i> Products</h3>
            <div class="value"><?php echo intval($stats['total_products']); ?></div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="admin-nav" role="tablist">
        <a href="?tab=dashboard" role="tab" aria-selected="<?php echo $tab === 'dashboard' ? 'true' : 'false'; ?>" class="<?php echo $tab === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="?tab=products" role="tab" aria-selected="<?php echo $tab === 'products' ? 'true' : 'false'; ?>" class="<?php echo ($tab === 'products' || $tab === 'products-form') && !$editingProduct ? 'active' : ''; ?>">
            <i class="fas fa-watch"></i> Products
        </a>
        <a href="?tab=submissions" role="tab" aria-selected="<?php echo $tab === 'submissions' ? 'true' : 'false'; ?>" class="<?php echo $tab === 'submissions' ? 'active' : ''; ?>">
            <i class="fas fa-message"></i> Submissions
        </a>
        <a href="?tab=orders" role="tab" aria-selected="<?php echo $tab === 'orders' ? 'true' : 'false'; ?>" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i> Orders
        </a>
    </div>

    <!-- Dashboard Tab -->
    <?php if ($tab === 'dashboard'): ?>
        <div class="admin-table">
            <h2 style="color: var(--primary); margin-bottom: 1.5rem;"><i class="fas fa-chart-line"></i> Dashboard Overview</h2>
            <p>Welcome to the Admin Panel. Use the tabs above to manage products, view customer submissions, and check orders.</p>
        </div>
    <?php endif; ?>

    <!-- Products Form Tab -->
    <?php if ($tab === 'products-form' || (isset($_GET['new']) && $_GET['new'] == '1')): ?>
        <div class="admin-form">
            <h2 style="color: var(--primary); margin-bottom: 1.5rem;">
                <i class="fas fa-watch"></i> <?php echo $editingProduct ? 'Edit Product' : 'Add New Product'; ?>
            </h2>
            <form method="post" action="">
                <input type="hidden" name="action" value="save_product" />
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                <?php if ($editingProduct): ?>
                    <input type="hidden" name="id" value="<?php echo intval($editingProduct['id']); ?>" />
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" value="<?php echo $editingProduct ? esc($editingProduct['name']) : ''; ?>" required aria-required="true" />
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand *</label>
                        <input type="text" id="brand" name="brand" value="<?php echo $editingProduct ? esc($editingProduct['brand']) : ''; ?>" required aria-required="true" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <input type="text" id="category" name="category" value="<?php echo $editingProduct ? esc($editingProduct['category']) : ''; ?>" required aria-required="true" />
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $editingProduct ? floatval($editingProduct['price']) : ''; ?>" required aria-required="true" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?php echo $editingProduct ? intval($editingProduct['stock']) : '10'; ?>" required aria-required="true" />
                    </div>
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="text" id="image" name="image" value="<?php echo $editingProduct ? esc($editingProduct['image']) : ''; ?>" placeholder="e.g., images/watch.jpg" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="colors">Colors (comma-separated)</label>
                    <input type="text" id="colors" name="colors" value="<?php echo $editingProduct ? esc($editingProduct['colors']) : ''; ?>" placeholder="e.g., Black, Silver, Gold" />
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required aria-required="true"><?php echo $editingProduct ? esc($editingProduct['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="features">Features (comma-separated)</label>
                    <textarea id="features" name="features"><?php echo $editingProduct ? esc($editingProduct['features']) : ''; ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editingProduct ? 'Update Product' : 'Create Product'; ?>
                    </button>
                    <a href="?tab=products" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Submissions Tab -->
    <?php if ($tab === 'submissions'): ?>
        <div class="admin-table">
            <h2 style="color: var(--primary); margin-bottom: 1.5rem;"><i class="fas fa-message"></i> User Submissions</h2>
            <?php if (empty($submissions)): ?>
                <p style="text-align: center; color: #cbd5e1; padding: 2rem;">No submissions yet</p>
            <?php else: ?>
                <div class="table-container">
                    <table role="table">
                        <thead role="rowgroup">
                            <tr role="row">
                                <th role="columnheader">ID</th>
                                <th role="columnheader">Name</th>
                                <th role="columnheader">Email</th>
                                <th role="columnheader">Phone</th>
                                <th role="columnheader">Model</th>
                                <th role="columnheader">Message</th>
                                <th role="columnheader">Date</th>
                                <th role="columnheader">Actions</th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup">
                            <?php foreach ($submissions as $sub): ?>
                                <tr role="row">
                                    <td><?php echo intval($sub['id']); ?></td>
                                    <td><?php echo esc($sub['name']); ?></td>
                                    <td><a href="mailto:<?php echo esc(urlencode($sub['email'])); ?>"><?php echo esc($sub['email']); ?></a></td>
                                    <td><?php echo esc($sub['phone']); ?></td>
                                    <td><?php echo esc($sub['favorite_model']); ?></td>
                                    <td><?php echo esc(substr($sub['message'], 0, 50)) . '...'; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($sub['created_at'])); ?></td>
                                    <td>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_submission" />
                                            <input type="hidden" name="id" value="<?php echo intval($sub['id']); ?>" />
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                            <button type="submit" class="btn btn-small" style="background: var(--danger); color: white;" onclick="return confirm('Delete this submission?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Orders Tab -->
    <?php if ($tab === 'orders'): ?>
        <div class="admin-table">
            <h2 style="color: var(--primary); margin-bottom: 1.5rem;"><i class="fas fa-shopping-bag"></i> Orders</h2>
            <?php if (empty($orders)): ?>
                <p style="text-align: center; color: #cbd5e1; padding: 2rem;">No orders yet</p>
            <?php else: ?>
                <div class="table-container">
                    <table role="table">
                        <thead role="rowgroup">
                            <tr role="row">
                                <th role="columnheader">Order ID</th>
                                <th role="columnheader">Customer</th>
                                <th role="columnheader">Email</th>
                                <th role="columnheader">Items</th>
                                <th role="columnheader">Total</th>
                                <th role="columnheader">Status</th>
                                <th role="columnheader">Date</th>
                                <th role="columnheader">Actions</th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup">
                            <?php foreach ($orders as $order): ?>
                                <tr role="row">
                                    <td><strong>#<?php echo intval($order['id']); ?></strong></td>
                                    <td><?php echo esc($order['customer_name']); ?></td>
                                    <td><a href="mailto:<?php echo esc(urlencode($order['customer_email'])); ?>"><?php echo esc($order['customer_email']); ?></a></td>
                                    <td><?php echo intval($order['total_items'] ?? 0); ?></td>
                                    <td><strong><?php echo formatPrice($order['total_price']); ?></strong></td>
                                    <td><span style="background: rgba(16, 185, 129, 0.2); color: #a7f3d0; padding: 0.25rem 0.75rem; border-radius: 4px;"><?php echo esc($order['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_order" />
                                            <input type="hidden" name="id" value="<?php echo intval($order['id']); ?>" />
                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                            <button type="submit" class="btn btn-small" style="background: var(--danger); color: white;" onclick="return confirm('Delete this order?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Products Tab -->
    <?php if ($tab === 'products'): ?>
        <div class="admin-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="margin: 0; color: var(--primary);"><i class="fas fa-watch"></i> Products Catalog</h2>
                <a href="?tab=products-form&new=1" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            <?php if (empty($products)): ?>
                <p style="text-align: center; color: #cbd5e1; padding: 2rem;">No products yet. <a href="?tab=products-form&new=1" style="color: var(--primary);">Add one now</a></p>
            <?php else: ?>
                <div class="table-container">
                    <table role="table">
                        <thead role="rowgroup">
                            <tr role="row">
                                <th role="columnheader">ID</th>
                                <th role="columnheader">Name</th>
                                <th role="columnheader">Brand</th>
                                <th role="columnheader">Price</th>
                                <th role="columnheader">Stock</th>
                                <th role="columnheader">Category</th>
                                <th role="columnheader">Actions</th>
                            </tr>
                        </thead>
                        <tbody role="rowgroup">
                            <?php foreach ($products as $product): ?>
                                <tr role="row">
                                    <td><?php echo intval($product['id']); ?></td>
                                    <td><?php echo esc($product['name']); ?></td>
                                    <td><?php echo esc($product['brand']); ?></td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td><?php echo intval($product['stock']); ?></td>
                                    <td><?php echo esc($product['category']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?tab=products-form&edit=<?php echo intval($product['id']); ?>" class="btn btn-small">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_product" />
                                                <input type="hidden" name="id" value="<?php echo intval($product['id']); ?>" />
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>" />
                                                <button type="submit" class="btn btn-small" style="background: var(--danger); color: white;" onclick="return confirm('Delete this product?');">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
                                <td><?php echo esc($product['brand']); ?></td>
                                <td><?php echo formatPrice($product['price']); ?></td>
                                <td><?php echo intval($product['stock']); ?></td>
                                <td><?php echo esc(substr($product['description'], 0, 40)) . '...'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script>
// Keyboard navigation for tabs
document.querySelectorAll('[role="tab"]').forEach(tab => {
    tab.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
});
</script>
</body>
</html>
