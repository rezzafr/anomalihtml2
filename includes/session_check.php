<?php
session_start();

// 1. Check if user is logged in
if (isset($_SESSION['user_id'])) {
    
    // 2. Set timeout duration (e.g., 30 minutes = 1800 seconds)
    $timeout_duration = 1800; 

    // 3. Check if "last_activity" is set and if timeout has expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        
        // Session expired: Unset and destroy
        session_unset();
        session_destroy();
        
        // Redirect to login with a message
        echo "<script>alert('Your session has expired due to inactivity. Please login again.'); window.location.href='login.php';</script>";
        exit();
    }

    // 4. Update last activity time stamp
    $_SESSION['last_activity'] = time();
}
?>