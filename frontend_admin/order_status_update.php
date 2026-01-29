<?php
session_start();
// Direct Database Connection to Bypass API Gateway Issues
include '../order_service/config/database.php';

$logFile = 'order_debug.log';

if(isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = $_GET['id'];
    $status = $_GET['status'];
    
    // LOGGING START
    $logMsg = date('Y-m-d H:i:s') . " - ATTEMPT Direct Update Order #$order_id to $status\n";
    file_put_contents($logFile, $logMsg, FILE_APPEND);

    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $order_id);
        
        if($stmt->execute()) {
             $logMsg = date('Y-m-d H:i:s') . " - SUCCESS Direct Update\n";
             file_put_contents($logFile, $logMsg, FILE_APPEND);
             
             // Redirect with Cache Bust
             header('Location: orders.php?t=' . time() . '&success=Status+Updated');
             exit;
        } else {
             $err = implode(" ", $stmt->errorInfo());
             $logMsg = date('Y-m-d H:i:s') . " - FAIL Execute: $err\n";
             file_put_contents($logFile, $logMsg, FILE_APPEND);
             echo "Database Execute Error: $err <br><a href='orders.php'>Back</a>";
             exit;
        }
    } catch(Exception $e) {
        $logMsg = date('Y-m-d H:i:s') . " - EXCEPTION: " . $e->getMessage() . "\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);
        echo "Exception: " . $e->getMessage() . "<br><a href='orders.php'>Back</a>";
        exit;
    }
}

header('Location: orders.php?error=invalid_request');
?>
