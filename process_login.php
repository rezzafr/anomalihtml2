<?php
session_start();
require 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Prepare the query
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // 2. Store the result 
    // This is required when using num_rows with prepared statements in this style
    $stmt->store_result();

    // 3. Check if user exists using num_rows
    if ($stmt->num_rows > 0) {
        
        // 4. Bind the results to variables
        // CRITICAL: The order here MUST match the SELECT statement above
        // SELECT: id, username, password, role
        // BIND:   $id, $username, $db_password, $role
        $stmt->bind_result($id, $username, $db_password, $role);
        
        // 5. Fetch the data into those variables
        $stmt->fetch();

        // 6. Verify Password
        // We compare the typed $password against the $db_password we just fetched
        if (password_verify($password, $db_password)) {
            
            // Success: Set Session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['last_activity'] = time();

            echo "<script>alert('Login Successful!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href='login.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>