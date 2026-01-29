<?php
include '../order_service/config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "Checking for orders with empty or null status...<br>";

// Select
$query = "SELECT id FROM orders WHERE status IS NULL OR status = ''";
$stmt = $db->prepare($query);
$stmt->execute();

$ids = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $ids[] = $row['id'];
}

if(count($ids) > 0) {
    echo "Found " . count($ids) . " orders with invalid status: " . implode(", ", $ids) . "<br>";
    
    // Update
    $updateQuery = "UPDATE orders SET status = 'pending' WHERE status IS NULL OR status = ''";
    $stmtUpdate = $db->prepare($updateQuery);
    if($stmtUpdate->execute()) {
        echo "Successfully updated them to 'pending'.";
    } else {
        echo "Failed to update.";
    }
} else {
    echo "No invalid orders found.";
}
?>
