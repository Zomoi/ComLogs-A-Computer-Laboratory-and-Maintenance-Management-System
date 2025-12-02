<?php
// Redirect to login if not authenticated
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
//}
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Optional: Confirm deletion (you already have JS confirm in index.php)
    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_logs WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Maintenance log deleted successfully.";
    } catch (Exception $e) {
        $message = "Error deleting maintenance log.";
    }
}

// Redirect back to index with message (optional)
header("Location: index.php");
exit();
?>
