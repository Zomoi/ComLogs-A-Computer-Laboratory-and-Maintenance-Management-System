<?php
// Redirect to login if not authenticated
//if (!isset($_SESSION['user_id'])) {
//    header("Location: login.php");
//    exit();
//}
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Prevent deleting the current user or admin users
    $currentUserId = $_SESSION['user_id'] ?? null;
    $currentRole = $_SESSION['user_role'] ?? 'guest';

    if ($currentRole !== 'admin') {
        $message = "Access denied.";
    } elseif ($id == $currentUserId) {
        $message = "Cannot delete your own account.";
    } else {
        // Check if the user to delete is not an admin
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user && $user['role'] !== 'admin') {
            try {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $message = "User deleted successfully.";
            } catch (Exception $e) {
                $message = "Error deleting user.";
            }
        } else {
            $message = "Cannot delete admin users.";
        }
    }
}

// Redirect back to index with message (optional)
header("Location: index.php");
exit();
?>
