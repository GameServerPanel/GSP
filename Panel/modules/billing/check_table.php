<?php
/**
 * Check {table_prefix}billing_invoices table structure
 */

require_once(__DIR__ . '/bootstrap.php');
require_once('../../includes/database_mysqli.php');

$db = createDatabaseConnection($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>{$table_prefix}billing_invoices Table Structure</h2>\n";

$result = mysqli_query($db, "DESCRIBE {$table_prefix}billing_invoices");

if (!$result) {
    die("Table doesn't exist or query failed: " . mysqli_error($db));
}

echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>\n";
}

echo "</table>\n";

// Count existing invoices
$count_result = mysqli_query($db, "SELECT COUNT(*) as cnt FROM {$table_prefix}billing_invoices");
$count = mysqli_fetch_assoc($count_result);
echo "<p><strong>Total invoices in table:</strong> {$count['cnt']}</p>\n";

// Show last 5 invoices
echo "<h2>Last 5 Invoices</h2>\n";
$last_result = mysqli_query($db, "SELECT * FROM {$table_prefix}billing_invoices ORDER BY invoice_id DESC LIMIT 5");

if (mysqli_num_rows($last_result) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr>";
    $first = true;
    while ($row = mysqli_fetch_assoc($last_result)) {
        if ($first) {
            foreach (array_keys($row) as $col) {
                echo "<th>{$col}</th>";
            }
            echo "</tr>\n";
            $first = false;
            mysqli_data_seek($last_result, 0);
        }
    }
    
    while ($row = mysqli_fetch_assoc($last_result)) {
        echo "<tr>";
        foreach ((array)$row as $val) {
            echo "<td>" . htmlspecialchars($val ?? 'NULL') . "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "<p>No invoices found.</p>\n";
}

    billing_maybe_close_db($db);
?>
