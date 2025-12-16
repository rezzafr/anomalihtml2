<?php
session_start();
require_once 'includes/db_connect.php'; 

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Get Data from the Futsal Page Form
    $user_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'];
    $date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot']; // format: "08:00 AM - 10:00 AM"
    $cust_name = $_POST['customer_name'];
    $cust_phone = $_POST['customer_phone'];

    // 3. Parse the Time Slot
    // We need to split "08:00 AM - 10:00 AM" into "08:00:00" and "10:00:00"
    $times = explode(' - ', $time_slot);
    $start_time = date("H:i:s", strtotime($times[0])); // Convert to 24-hour format
    $end_time = date("H:i:s", strtotime($times[1]));

    // 4. Insert into Database
    $sql = "INSERT INTO bookings (user_id, service_id, booking_date, start_time, end_time, customer_name, customer_phone, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iisssss", $user_id, $service_id, $date, $start_time, $end_time, $cust_name, $cust_phone);
        
        if ($stmt->execute()) {
            // Success! Send them back to 'my bookings' or the service page
            header("Location: bookings.php?msg=Booking+Confirmed+Successfully");
            exit();
        } else {
            // Error
            header("Location: futsalservices.php?error=Database+Error");
        }
    } else {
         header("Location: futsalservices.php?error=SQL+Error");
    }

} else {
    // If someone tries to open booking.php directly
    header("Location: services.php");
}
?>