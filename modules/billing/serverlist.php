<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server List - GameServers.World</title>
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Fetch services
$service_id = isset($_REQUEST['service_id']) ? intval($_REQUEST['service_id']) : 0;
$where_service_id = $service_id !== 0
    ? "WHERE enabled = 1 AND service_id = $service_id AND remote_server_id != '' AND remote_server_id IS NOT NULL"
    : "WHERE enabled = 1 AND remote_server_id != '' AND remote_server_id IS NOT NULL";
$qry_services = "SELECT * FROM {$table_prefix}billing_services $where_service_id ORDER BY service_name";
$result_services = $db->query($qry_services);

if (!$result_services) {
    echo "<meta http-equiv='refresh' content='1'>";
    billing_maybe_close_db($db);
    return;
}

// Fetch all service rows into an array so the template foreach works correctly
$serviceRows = [];
while ($row = $result_services->fetch_assoc()) {
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
            <?php $imgSrc = billing_image_url((string)($row['img_url'] ?? '')); ?>
            <?php if ($imgSrc !== ''): ?>
            <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="460" height="225"><br>
            <?php endif; ?>
            <strong><?php echo htmlspecialchars((string)$row['service_name'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
            <?php
            echo ($row['price_monthly'] == 0.0) ? "FREE" : "$" . number_format(floatval($row['price_monthly']), 2) . " Monthly";
            ?>
            <br>
                        
            <a href="order.php?service_id=<?php echo urlencode($row['service_id']); ?>" class="gsw-btn">Order Now</a>
        </div>
    <?php else: ?>
        <!-- Single service detail view -->
    <div class="float-left decorative-bottom">
            <?php $imgSrc = billing_image_url((string)($row['img_url'] ?? '')); ?>
            <?php if ($imgSrc !== ''): ?>
            <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="230" height="112"><br>
            <?php endif; ?>
            <center><b><?php echo htmlspecialchars((string)$row['service_name'], ENT_QUOTES, 'UTF-8'); ?></b></center>

            <?php
            $isAdmin = false; // change to actual check, e.g. current_user_can('administrator')
            if ($isAdmin) {
                if (!isset($_POST['edit'])) {
                    echo "<p style='color:gray;width:230px;'>{$row['description']}</p>";
                    echo "<form method='post'>
                            <input type='hidden' name='service_id' value='{$row['servioce_id']}'>
                            <input type='submit' name='edit' value='Edit'>
                          </form>";
                } else {
                    $desc = str_replace("<br>", "\r\n", $row['description']);
                    echo "<form method='post'>
                            <textarea style='resize:none;width:230px;height:132px;' name='description'>$desc</textarea><br>
                            <input type='hidden' name='service_id' value='{$row['service_id']}'>
                            <input type='submit' name='save' value='Save'>
                          </form>";
                }
            } else {
                echo "<p style='color:gray;width:280px;'>{$row['description']}</p>";
            }
            ?>
        </div>

        <!-- Order Form -->
        <form method="post" action="order_server.php">
            <input type="hidden" name="service_id" value="<?php echo $row['service_id']; ?>">
            <input type="hidden" name="remote_control_password" value="">
            <input type="hidden" name="ftp_password" value="">
            <table class="float-left">
                <tr>
                    <td align="right"><b>Game Server Name</b></td>
                    <td><input type="text" name="home_name" size="40" value="<?php echo $row['service_name']; ?>"></td>
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
