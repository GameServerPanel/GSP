<?php
// Admin coupon management page - standalone billing module
require_once(__DIR__ . '/includes/admin_auth.php');
require_once(__DIR__ . '/includes/config_loader.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Start session if not already started by admin_auth
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_csrf'])) {
  // generate a CSRF token with a safe fallback for older PHP builds
  try {
    $token = function_exists('random_bytes') ? bin2hex(random_bytes(16)) : null;
  } catch (Exception $e) {
    $token = null;
  }
  if (empty($token)) {
    if (function_exists('openssl_random_pseudo_bytes')) {
      $token = bin2hex(openssl_random_pseudo_bytes(16));
    } else {
      $token = bin2hex(bin2hex(substr(sha1(uniqid((string)microtime(true), true)), 0, 16)));
    }
  }
  $_SESSION['admin_csrf'] = $token;
}
$csrf = $_SESSION['admin_csrf'];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Connect to database (graceful failure)
$db = false;
try {
  // suppress direct output; we'll log errors and show a friendly message
  $db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
} catch (Throwable $e) {
  error_log('[admin_coupons] mysqli_connect exception: ' . $e->getMessage());
  $db = false;
}
if (!$db) {
  $error = 'Database connection failed. Please check your configuration.';
  error_log('[admin_coupons] DB connect failed for host=' . ($db_host ?? 'unknown') . ' user=' . ($db_user ?? 'unknown') . ' db=' . ($db_name ?? 'unknown') . ' - ' . mysqli_connect_error());
}

$status = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($csrf, (string)$token)) {
        $error = 'Invalid CSRF token.';
    } else {
        // Add new coupon
        if (isset($_POST['add_coupon'])) {
            $code = mysqli_real_escape_string($db, trim($_POST['code']));
            $name = mysqli_real_escape_string($db, trim($_POST['name']));
            $description = mysqli_real_escape_string($db, trim($_POST['description']));
            $discount_percent = floatval($_POST['discount_percent']);
            $usage_type = mysqli_real_escape_string($db, $_POST['usage_type']);
            $game_filter_type = mysqli_real_escape_string($db, $_POST['game_filter_type']);
            $game_filter_list = isset($_POST['game_filter_list']) && $_POST['game_filter_type'] === 'specific_games' 
                ? mysqli_real_escape_string($db, json_encode($_POST['game_filter_list'])) 
                : 'NULL';
            $max_uses = !empty($_POST['max_uses']) ? intval($_POST['max_uses']) : 'NULL';
            $expires = !empty($_POST['expires']) ? "'" . mysqli_real_escape_string($db, $_POST['expires']) . "'" : 'NULL';
            
            // Validate code is unique
            $check = mysqli_query($db, "SELECT coupon_id FROM {$table_prefix}billing_coupons WHERE code = '$code'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Coupon code '$code' already exists.";
            } else {
                $sql = "INSERT INTO {$table_prefix}billing_coupons 
                        (code, name, description, discount_percent, usage_type, game_filter_type, game_filter_list, max_uses, expires, is_active)
                        VALUES ('$code', '$name', '$description', $discount_percent, '$usage_type', '$game_filter_type', " . 
                        ($game_filter_list === 'NULL' ? 'NULL' : "'$game_filter_list'") . ", $max_uses, $expires, 1)";
                
                if (mysqli_query($db, $sql)) {
                    $status = "Coupon '$code' added successfully.";
                } else {
                    $error = "Error adding coupon: " . mysqli_error($db);
                }
            }
        }
        
        // Update existing coupon
        elseif (isset($_POST['update_coupon'])) {
            $coupon_id = intval($_POST['coupon_id']);
            $code = mysqli_real_escape_string($db, trim($_POST['code']));
            $name = mysqli_real_escape_string($db, trim($_POST['name']));
            $description = mysqli_real_escape_string($db, trim($_POST['description']));
            $discount_percent = floatval($_POST['discount_percent']);
            $usage_type = mysqli_real_escape_string($db, $_POST['usage_type']);
            $game_filter_type = mysqli_real_escape_string($db, $_POST['game_filter_type']);
            $game_filter_list = isset($_POST['game_filter_list']) && $_POST['game_filter_type'] === 'specific_games' 
                ? mysqli_real_escape_string($db, json_encode($_POST['game_filter_list'])) 
                : 'NULL';
            $max_uses = !empty($_POST['max_uses']) ? intval($_POST['max_uses']) : 'NULL';
            $expires = !empty($_POST['expires']) ? "'" . mysqli_real_escape_string($db, $_POST['expires']) . "'" : 'NULL';
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $sql = "UPDATE {$table_prefix}billing_coupons SET
                    code = '$code',
                    name = '$name',
                    description = '$description',
                    discount_percent = $discount_percent,
                    usage_type = '$usage_type',
                    game_filter_type = '$game_filter_type',
                    game_filter_list = " . ($game_filter_list === 'NULL' ? 'NULL' : "'$game_filter_list'") . ",
                    max_uses = $max_uses,
                    expires = $expires,
                    is_active = $is_active
                    WHERE coupon_id = $coupon_id";
            
            if (mysqli_query($db, $sql)) {
                $status = "Coupon updated successfully.";
            } else {
                $error = "Error updating coupon: " . mysqli_error($db);
            }
        }
        
        // Delete coupon
        elseif (isset($_POST['delete_coupon'])) {
            $coupon_id = intval($_POST['coupon_id']);
            if (mysqli_query($db, "DELETE FROM {$table_prefix}billing_coupons WHERE coupon_id = $coupon_id")) {
                $status = "Coupon deleted successfully.";
            } else {
                $error = "Error deleting coupon: " . mysqli_error($db);
            }
        }
    }
}

// Get all available games from server configs
$game_options = [];
$games_dir = __DIR__ . '/../../config_games/server_configs/';
if (is_dir($games_dir)) {
    $files = scandir($games_dir);
    foreach ((array)$files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'xml' && strpos($file, '.bak') === false) {
            $game_key = str_replace('.xml', '', $file);
            $game_options[] = $game_key;
        }
    }
    sort($game_options);
}

// Get all coupons
$coupons_result = mysqli_query($db, "SELECT * FROM {$table_prefix}billing_coupons ORDER BY created_date DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Coupon Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
  <style>
    .coupon-form { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; box-sizing: border-box; }
    .form-group textarea { min-height: 60px; }
    .game-checkboxes { max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: white; }
    .game-checkboxes label { display: block; margin: 5px 0; font-weight: normal; }
    .coupon-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    .coupon-table th, .coupon-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    .coupon-table th { background: #4CAF50; color: white; }
    .coupon-table tr:nth-child(even) { background: #f9f9f9; }
    .btn { padding: 8px 16px; margin: 2px; cursor: pointer; border: none; border-radius: 3px; }
    .btn-primary { background: #4CAF50; color: white; }
    .btn-warning { background: #ff9800; color: white; }
    .btn-danger { background: #f44336; color: white; }
    .status { padding: 10px; margin: 10px 0; border-radius: 3px; }
    .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge { padding: 3px 8px; border-radius: 3px; font-size: 0.85em; }
    .badge-active { background: #28a745; color: white; }
    .badge-inactive { background: #6c757d; color: white; }
    .badge-onetime { background: #17a2b8; color: white; }
    .badge-permanent { background: #ffc107; color: black; }
  </style>
  <script>
    function toggleGameFilter(selectEl) {
        const gameList = document.getElementById('game_filter_list_container');
        if (selectEl.value === 'specific_games') {
            gameList.style.display = 'block';
        } else {
            gameList.style.display = 'none';
        }
    }
    
    function editCoupon(couponId) {
        document.getElementById('edit-form-' + couponId).style.display = 'block';
        document.getElementById('view-row-' + couponId).style.display = 'none';
    }
    
    function cancelEdit(couponId) {
        document.getElementById('edit-form-' + couponId).style.display = 'none';
        document.getElementById('view-row-' + couponId).style.display = 'table-row';
    }
  </script>
</head>
<body>
<?php
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');
?>
<div class="container-wide panel">
  <h1>Coupon Management</h1>
  
  <?php if ($status): ?>
    <div class="status success"><?php echo h($status); ?></div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="status error"><?php echo h($error); ?></div>
  <?php endif; ?>
  
  <!-- Add New Coupon Form -->
  <h2>Add New Coupon</h2>
  <form method="POST" class="coupon-form">
    <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
    
    <div class="form-group">
      <label for="code">Coupon Code *</label>
      <input type="text" id="code" name="code" required maxlength="50" placeholder="e.g., SUMMER2025">
    </div>
    
    <div class="form-group">
      <label for="name">Display Name *</label>
      <input type="text" id="name" name="name" required maxlength="255" placeholder="e.g., Summer Sale 2025">
    </div>
    
    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Optional description for internal use"></textarea>
    </div>
    
    <div class="form-group">
      <label for="discount_percent">Discount Percentage * (0-100)</label>
      <input type="number" id="discount_percent" name="discount_percent" required min="0" max="100" step="0.01" value="10">
    </div>
    
    <div class="form-group">
      <label for="usage_type">Usage Type *</label>
      <select id="usage_type" name="usage_type" required>
        <option value="one_time">One Time (applies to first invoice only)</option>
        <option value="permanent">Permanent (applies to all renewals)</option>
      </select>
    </div>
    
    <div class="form-group">
      <label for="game_filter_type">Apply To *</label>
      <select id="game_filter_type" name="game_filter_type" required onchange="toggleGameFilter(this)">
        <option value="all_games">All Games</option>
        <option value="specific_games">Specific Games</option>
      </select>
    </div>
    
    <div id="game_filter_list_container" class="form-group" style="display:none;">
      <label>Select Games</label>
      <div class="game-checkboxes">
        <?php foreach ((array)$game_options as $game): ?>
          <label>
            <input type="checkbox" name="game_filter_list[]" value="<?php echo h($game); ?>">
            <?php echo h($game); ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="form-group">
      <label for="max_uses">Maximum Uses (leave empty for unlimited)</label>
      <input type="number" id="max_uses" name="max_uses" min="1" placeholder="Unlimited">
    </div>
    
    <div class="form-group">
      <label for="expires">Expiration Date (leave empty for no expiration)</label>
      <input type="datetime-local" id="expires" name="expires">
    </div>
    
    <button type="submit" name="add_coupon" class="btn btn-primary">Add Coupon</button>
  </form>
  
  <!-- Existing Coupons Table -->
  <h2>Existing Coupons</h2>
  
  <?php if ($coupons_result && mysqli_num_rows($coupons_result) > 0): ?>
    <table class="coupon-table">
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Discount</th>
          <th>Type</th>
          <th>Game Filter</th>
          <th>Uses</th>
          <th>Expires</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($coupon = mysqli_fetch_assoc($coupons_result)): 
          $games_filtered = $coupon['game_filter_type'] === 'specific_games' 
            ? json_decode($coupon['game_filter_list'], true) 
            : [];
        ?>
          <!-- View Row -->
          <tr id="view-row-<?php echo $coupon['coupon_id']; ?>">
            <td><strong><?php echo h($coupon['code']); ?></strong></td>
            <td><?php echo h($coupon['name']); ?></td>
            <td><?php echo h($coupon['discount_percent']); ?>%</td>
            <td>
              <span class="badge badge-<?php echo $coupon['usage_type'] === 'permanent' ? 'permanent' : 'onetime'; ?>">
                <?php echo h(ucfirst(str_replace('_', ' ', $coupon['usage_type']))); ?>
              </span>
            </td>
            <td>
              <?php if ($coupon['game_filter_type'] === 'all_games'): ?>
                All Games
              <?php else: ?>
                <?php echo count((array)$games_filtered); ?> specific games
              <?php endif; ?>
            </td>
            <td>
              <?php if ($coupon['max_uses']): ?>
                <?php echo h($coupon['current_uses']); ?> / <?php echo h($coupon['max_uses']); ?>
              <?php else: ?>
                <?php echo h($coupon['current_uses']); ?> (unlimited)
              <?php endif; ?>
            </td>
            <td><?php echo $coupon['expires'] ? h($coupon['expires']) : 'Never'; ?></td>
            <td>
              <span class="badge badge-<?php echo $coupon['is_active'] ? 'active' : 'inactive'; ?>">
                <?php echo $coupon['is_active'] ? 'Active' : 'Inactive'; ?>
              </span>
            </td>
            <td>
              <button onclick="editCoupon(<?php echo $coupon['coupon_id']; ?>)" class="btn btn-warning">Edit</button>
              <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this coupon?');">
                <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                <input type="hidden" name="coupon_id" value="<?php echo $coupon['coupon_id']; ?>">
                <button type="submit" name="delete_coupon" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          
          <!-- Edit Form Row (hidden by default) -->
          <tr id="edit-form-<?php echo $coupon['coupon_id']; ?>" style="display:none;">
            <td colspan="9">
              <form method="POST" class="coupon-form">
                <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
                <input type="hidden" name="coupon_id" value="<?php echo $coupon['coupon_id']; ?>">
                
                <div class="form-group">
                  <label>Coupon Code</label>
                  <input type="text" name="code" required value="<?php echo h($coupon['code']); ?>">
                </div>
                
                <div class="form-group">
                  <label>Display Name</label>
                  <input type="text" name="name" required value="<?php echo h($coupon['name']); ?>">
                </div>
                
                <div class="form-group">
                  <label>Description</label>
                  <textarea name="description"><?php echo h($coupon['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                  <label>Discount Percentage</label>
                  <input type="number" name="discount_percent" required min="0" max="100" step="0.01" value="<?php echo h($coupon['discount_percent']); ?>">
                </div>
                
                <div class="form-group">
                  <label>Usage Type</label>
                  <select name="usage_type" required>
                    <option value="one_time" <?php echo $coupon['usage_type'] === 'one_time' ? 'selected' : ''; ?>>One Time</option>
                    <option value="permanent" <?php echo $coupon['usage_type'] === 'permanent' ? 'selected' : ''; ?>>Permanent</option>
                  </select>
                </div>
                
                <div class="form-group">
                  <label>Apply To</label>
                  <select name="game_filter_type" required onchange="toggleGameFilter(this)">
                    <option value="all_games" <?php echo $coupon['game_filter_type'] === 'all_games' ? 'selected' : ''; ?>>All Games</option>
                    <option value="specific_games" <?php echo $coupon['game_filter_type'] === 'specific_games' ? 'selected' : ''; ?>>Specific Games</option>
                  </select>
                </div>
                
                <div class="form-group" style="display:<?php echo $coupon['game_filter_type'] === 'specific_games' ? 'block' : 'none'; ?>;">
                  <label>Select Games</label>
                  <div class="game-checkboxes">
                    <?php foreach ((array)$game_options as $game): ?>
                      <label>
                        <input type="checkbox" name="game_filter_list[]" value="<?php echo h($game); ?>"
                          <?php echo in_array($game, $games_filtered) ? 'checked' : ''; ?>>
                        <?php echo h($game); ?>
                      </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                
                <div class="form-group">
                  <label>Maximum Uses</label>
                  <input type="number" name="max_uses" min="1" value="<?php echo h($coupon['max_uses']); ?>" placeholder="Unlimited">
                </div>
                
                <div class="form-group">
                  <label>Expiration Date</label>
                  <input type="datetime-local" name="expires" value="<?php echo $coupon['expires'] ? date('Y-m-d\TH:i', strtotime($coupon['expires'])) : ''; ?>">
                </div>
                
                <div class="form-group">
                  <label>
                    <input type="checkbox" name="is_active" <?php echo $coupon['is_active'] ? 'checked' : ''; ?>>
                    Active
                  </label>
                </div>
                
                <button type="submit" name="update_coupon" class="btn btn-primary">Save Changes</button>
                <button type="button" onclick="cancelEdit(<?php echo $coupon['coupon_id']; ?>)" class="btn">Cancel</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No coupons found. Add your first coupon above.</p>
  <?php endif; ?>
  
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>

<?php
if ($db) mysqli_close($db);
?>
