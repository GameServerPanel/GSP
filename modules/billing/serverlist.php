<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server List - GameServers.World</title>
</head>
<body>
<?php
// Include database configuration
require_once(__DIR__ . '/bootstrap.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Save new description if admin
if (isset($_POST['save']) && !empty($_POST['description'])) {
    $new_description = str_replace("\\r\\n", "<br>", $_POST['description']);
    $service = intval($_POST['service_id']);
    $stmt = $db->prepare("UPDATE {$table_prefix}billing_services SET description = ? WHERE service_id = ?");
    $stmt->bind_param("si", $new_description, $service);
    $stmt->execute();
    $stmt->close();
}

// Fetch services, joining config_homes to get canonical game_name and game_key for OS detection.
// LEFT JOIN so services without a linked config_homes entry still appear.
$service_id = isset($_REQUEST['service_id']) ? intval($_REQUEST['service_id']) : 0;
if ($service_id !== 0) {
    $where_clause = "WHERE bs.enabled = 1 AND bs.service_id = {$service_id} AND bs.remote_server_id != '' AND bs.remote_server_id IS NOT NULL";
} else {
    $where_clause = "WHERE bs.enabled = 1 AND bs.remote_server_id != '' AND bs.remote_server_id IS NOT NULL";
}
$qry_services = "SELECT bs.*, ch.game_name AS cfg_game_name, ch.game_key AS cfg_game_key
                 FROM {$table_prefix}billing_services bs
                 LEFT JOIN {$table_prefix}config_homes ch ON ch.home_cfg_id = bs.home_cfg_id
                 {$where_clause}
                 ORDER BY bs.service_name";
$result_services = $db->query($qry_services);

if (!$result_services) {
    // config_homes join may not exist on all installs; fall back to services-only query
    $where_clause_fallback = str_replace('bs.', '', $where_clause);
    $qry_services_fallback = "SELECT service_id, home_cfg_id, enabled, service_name, description,
                                      img_url, price_monthly, slot_min_qty, slot_max_qty,
                                      remote_server_id,
                                      NULL AS cfg_game_name, NULL AS cfg_game_key
                               FROM {$table_prefix}billing_services
                               {$where_clause_fallback}
                               ORDER BY service_name";
    $result_services = $db->query($qry_services_fallback);
}

if (!$result_services) {
    echo "<meta http-equiv='refresh' content='1'>";
    billing_maybe_close_db($db);
    return;
}

// Fetch all service rows and deduplicate by canonical game name so that
// arma3_linux64 and arma3_win64 (both named "Arma 3") appear only once.
// When a specific service_id is requested we skip deduplication.
$serviceRows = [];
$seenCanonical = [];
while ($row = $result_services->fetch_assoc()) {
    if ($service_id !== 0) {
        // Single-service detail view: always include without deduplication
        $serviceRows[] = $row;
        continue;
    }
    // Derive canonical display name: prefer config_homes game_name (consistent across OS
    // variants), fall back to service_name.
    $canonicalName = !empty($row['cfg_game_name'])
        ? $row['cfg_game_name']
        : $row['service_name'];

    if (isset($seenCanonical[$canonicalName])) {
        // Already have this game — skip the duplicate OS variant
        continue;
    }
    $seenCanonical[$canonicalName] = true;
    $serviceRows[] = $row;
}
$result_services->free();

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');
?>

<!-- Services container: clearfix to contain floated service cards so footer clears correctly -->
<div class="clearfix container-wide">
<?php foreach ($serviceRows as $row): ?>
    <?php if (!isset($_REQUEST['service_id'])): ?>
        <!-- Service listing (all) -->
    <div class="float-left p-30-20">
            <?php
            $imgSrc = billing_image_url((string)($row['img_url'] ?? ''));
            // Use a generic fallback image when the service has no image configured
            if ($imgSrc === '') {
                $imgSrc = '/images/games/default_server.png';
            }
            ?>
            <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="460" height="225"
                 onerror="this.src='/images/games/default_server.png'; this.onerror=null;"><br>
            <strong><?php echo htmlspecialchars((string)($row['cfg_game_name'] ?? $row['service_name']), ENT_QUOTES, 'UTF-8'); ?></strong><br>
            <?php
            echo (floatval($row['price_monthly']) == 0.0) ? "FREE" : "$" . number_format(floatval($row['price_monthly']), 2) . " Monthly";
            ?>
            <br>
                        
            <a href="order.php?service_id=<?php echo urlencode($row['service_id']); ?>" class="gsw-btn">Order Now</a>
        </div>
    <?php else: ?>
        <!-- Single service detail view -->
    <div class="float-left decorative-bottom">
            <?php
            $imgSrc = billing_image_url((string)($row['img_url'] ?? ''));
            if ($imgSrc === '') {
                $imgSrc = '/images/games/default_server.png';
            }
            ?>
            <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="230" height="112"
                 onerror="this.src='/images/games/default_server.png'; this.onerror=null;"><br>
            <center><b><?php echo htmlspecialchars((string)($row['cfg_game_name'] ?? $row['service_name']), ENT_QUOTES, 'UTF-8'); ?></b></center>

            <?php
            $isAdmin = false;
            if ($isAdmin) {
                if (!isset($_POST['edit'])) {
                    echo "<p style='color:gray;width:230px;'>" . htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "<form method='post'>
                            <input type='hidden' name='service_id' value='" . intval($row['service_id']) . "'>
                            <input type='submit' name='edit' value='Edit'>
                          </form>";
                } else {
                    $desc = htmlspecialchars(str_replace("<br>", "\r\n", (string)($row['description'] ?? '')), ENT_QUOTES, 'UTF-8');
                    echo "<form method='post'>
                            <textarea style='resize:none;width:230px;height:132px;' name='description'>{$desc}</textarea><br>
                            <input type='hidden' name='service_id' value='" . intval($row['service_id']) . "'>
                            <input type='submit' name='save' value='Save'>
                          </form>";
                }
            } else {
                echo "<p style='color:gray;width:280px;'>" . htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>";
            }
            ?>
        </div>

        <!-- Order Form -->
        <form method="post" action="order_server.php">
            <input type="hidden" name="service_id" value="<?php echo intval($row['service_id']); ?>">
            <input type="hidden" name="remote_control_password" value="">
            <input type="hidden" name="ftp_password" value="">
            <table class="float-left">
                <tr>
                    <td align="right"><b>Game Server Name</b></td>
                    <td><input type="text" name="home_name" size="40" value="<?php echo htmlspecialchars((string)($row['service_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"></td>
                </tr>
                <!-- Add other form fields as needed -->
                <tr>
                    <td colspan="2">
                        <?php
                        // Only show Add to Cart when the user is logged in
                        $is_logged_in = (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) || (isset($_SESSION['website_username']) && !empty($_SESSION['website_username']));
                        if ($is_logged_in):
                        ?>
                            <button type="submit" class="gsw-btn">Add to Cart</button>
                        <?php else: ?>
                            <div class="login-placeholder">Please <a href="login.php">login</a> to order</div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </form>
    <?php endif; ?>
<?php endforeach; ?>
</div>

<?php
// Close database connection
billing_maybe_close_db($db);
?>

<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
