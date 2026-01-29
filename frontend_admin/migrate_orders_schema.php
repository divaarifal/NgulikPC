<?php
include '../order_service/config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "Migrating Orders Table Schema...<br>";

// We need to expand the ENUM to support all statuses we use
// pending, paid, confirmed, shipping, shipped, completed, cancelled
$query = "ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'paid', 'confirmed', 'shipping', 'shipped', 'completed', 'cancelled') DEFAULT 'pending'";

try {
    $stmt = $db->prepare($query);
    if($stmt->execute()) {
        echo "SUCCESS: Schema Updated.<br>";
    } else {
        echo "FAIL: " . implode(" ", $stmt->errorInfo()) . "<br>";
    }
} catch(Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "<br>";
}

echo "<br>Checking current columns:<br>";
$stmtDesc = $db->prepare("DESCRIBE orders");
$stmtDesc->execute();
while($row = $stmtDesc->fetch(PDO::FETCH_ASSOC)) {
    if($row['Field'] == 'status') {
        echo "Status Type: " . $row['Type'] . "<br>";
    }
}
?>
