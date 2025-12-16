<?php
// 1. SESSION & SECURITY CHECK
session_start();
// Ensure only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // If not admin, kick them out
    header("Location: index.php");
    exit();
}

// 2. INCLUDE DATABASE
require_once 'includes/db_connect.php';

// 3. CHECK FOR ID
// We check if 'id' exists in the URL (e.g., delete_service.php?id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 4. SECURE DELETE (Prepared Statement)
    // using '?' prevents SQL injection
    $sql = "DELETE FROM services WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind the ID (Assuming ID is an integer 'i')
        $stmt->bind_param("i", $id);

        // Execute the delete
        if ($stmt->execute()) {
            // Success: Redirect with a success message
            header("Location: services.php?msg=Service+Deleted+Successfully");
            exit();
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    // If no ID was provided in the URL
    header("Location: services.php?msg=Error:+No+ID+Provided");
    exit();
}

$conn->close();
?>