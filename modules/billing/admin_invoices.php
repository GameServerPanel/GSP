<?php
// Admin invoices viewer and editor
$session_name = session_name(); session_start();
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/includes/admin_auth.php');

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) die('DB connection failed');

// Handle POST requests for invoice updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_invoice'])) {
        $orderId = intval($_POST['order_id']);
        $newStatus = mysqli_real_escape_string($db, $_POST['status']);
        $newPrice = floatval($_POST['price']);
        
        $sql = "UPDATE {$table_prefix}billing_orders SET status = '$newStatus', price = $newPrice WHERE order_id = $orderId LIMIT 1";
        mysqli_query($db, $sql);
        header('Location: admin_invoices.php?updated=' . $orderId);
        exit;
    }
}

// Fetch all orders with coupon information
$orders = mysqli_query($db, "SELECT o.*, u.user_name, c.code AS coupon_code, c.discount_percent AS coupon_discount 
    FROM {$table_prefix}billing_orders o 
    LEFT JOIN {$table_prefix}users u ON o.user_id = u.user_id 
    LEFT JOIN {$table_prefix}billing_coupons c ON o.coupon_id = c.coupon_id
    ORDER BY o.order_id DESC");

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Invoices</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
  <style>
    .edit-row { background: #f9f9f9; }
    .edit-input { width: 80px; padding: 4px; border: 1px solid #ccc; border-radius: 3px; }
    .edit-select { padding: 4px; border: 1px solid #ccc; border-radius: 3px; }
    .btn-save { background: #28a745; color: white; border: none; padding: 5px 12px; border-radius: 3px; cursor: pointer; }
    .btn-save:hover { background: #218838; }
    .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; }
    .status-paid { background: #d4edda; color: #155724; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-in-cart { background: #d1ecf1; color: #0c5460; }
    .status-expired { background: #f8d7da; color: #721c24; }
    .status-renew { background: #cce5ff; color: #004085; }
    .status-installed { background: #d4edda; color: #155724; }
  </style>
</head>
<body>
<?php include(__DIR__ . '/includes/top.php'); include(__DIR__ . '/includes/menu.php'); ?>
<div class="container-wide panel">
  <h1>Admin — All Invoices</h1>
  <?php if (isset($_GET['updated'])): ?>
    <div style="background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 3px; color: #155724;">
      ✓ Invoice #<?php echo h($_GET['updated']); ?> updated successfully.
    </div>
  <?php endif; ?>
  
  <?php if (!$orders || mysqli_num_rows($orders) === 0): ?>
    <p>No invoices found.</p>
  <?php else: ?>
    <table class="cart-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>User</th>
          <th>Home ID</th>
          <th>Home Name</th>
          <th>IP</th>
          <th>Price</th>
          <th>Duration</th>
          <th>Status</th>
          <th>Created</th>
          <th>Finish Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = mysqli_fetch_assoc($orders)): ?>
        <tr id="row-<?php echo $row['order_id']; ?>">
          <td><?php echo h($row['order_id']); ?></td>
          <td><?php echo h($row['user_name'] ?? 'N/A'); ?></td>
          <td><?php echo h($row['home_id'] ?? 'N/A'); ?></td>
          <td><?php echo h($row['home_name']); ?></td>
          <td><?php echo h($row['ip']); ?></td>
          <td>
            <?php 
            $price = floatval($row['price']);
            $discount = floatval($row['discount_amount'] ?? 0);
            
            if ($discount > 0 && !empty($row['coupon_code'])) {
                echo '<span style="text-decoration: line-through; color: #999;">$' . number_format($price + $discount, 2) . '</span><br>';
                echo '<strong>$' . number_format($price, 2) . '</strong>';
                echo '<br><small style="color: #28a745;">(' . h($row['coupon_code']) . ' -' . number_format($row['coupon_discount'], 0) . '%)</small>';
            } else {
                echo '$' . number_format($price, 2);
            }
            ?>
          </td>
          <td><?php echo h($row['invoice_duration']); ?></td>
          <td>
            <span class="status-badge status-<?php echo h($row['status']); ?>">
              <?php echo strtoupper(h($row['status'])); ?>
            </span>
          </td>
          <td><?php echo h($row['order_date']); ?></td>
          <td><?php echo h($row['end_date'] ?? 'N/A'); ?></td>
          <td>
            <button onclick="editRow(<?php echo $row['order_id']; ?>)" class="gsw-btn" style="padding: 4px 10px; font-size: 12px;">Edit</button>
          </td>
        </tr>
        <tr id="edit-<?php echo $row['order_id']; ?>" class="edit-row" style="display: none;">
          <td colspan="11">
            <form method="post" action="" style="padding: 10px;">
              <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
              <strong>Edit Invoice #<?php echo $row['order_id']; ?></strong>
              <div style="margin-top: 10px;">
                <label style="margin-right: 15px;">
                  <strong>Price:</strong> 
                  <input type="number" name="price" value="<?php echo $row['price']; ?>" step="0.01" class="edit-input" required>
                </label>
                <label style="margin-right: 15px;">
                  <strong>Status:</strong>
                  <select name="status" class="edit-select" required>
                    <option value="in-cart" <?php echo $row['status'] === 'in-cart' ? 'selected' : ''; ?>>IN-CART</option>
                    <option value="paid" <?php echo $row['status'] === 'paid' ? 'selected' : ''; ?>>PAID</option>
                    <option value="installed" <?php echo $row['status'] === 'installed' ? 'selected' : ''; ?>>INSTALLED</option>
                    <option value="renew" <?php echo $row['status'] === 'renew' ? 'selected' : ''; ?>>RENEW</option>
                    <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>PENDING</option>
                    <option value="expired" <?php echo $row['status'] === 'expired' ? 'selected' : ''; ?>>EXPIRED</option>
                  </select>
                </label>
                <button type="submit" name="update_invoice" class="btn-save">Save Changes</button>
                <button type="button" onclick="cancelEdit(<?php echo $row['order_id']; ?>)" class="gsw-btn" style="padding: 5px 12px; margin-left: 5px;">Cancel</button>
              </div>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script>
function editRow(orderId) {
  document.getElementById('row-' + orderId).style.display = 'none';
  document.getElementById('edit-' + orderId).style.display = 'table-row';
}

function cancelEdit(orderId) {
  document.getElementById('row-' + orderId).style.display = 'table-row';
  document.getElementById('edit-' + orderId).style.display = 'none';
}
</script>

<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>

