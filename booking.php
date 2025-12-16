<?php
session_start();
require_once 'includes/db_connect.php'; 

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect helper (fallback safe)
function redirectWithError($target, $message) {
    header("Location: " . $target . "?error=" . urlencode($message));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Get Form Data
    $user_id     = $_SESSION['user_id'];
    $service_id  = $_POST['service_id'];
    $date        = $_POST['booking_date'];
    $time_slot   = $_POST['time_slot']; // "08:00 AM - 10:00 AM"
    $cust_name   = $_POST['customer_name'];
    $cust_phone  = $_POST['customer_phone'];

    // 3. Parse Time Slot
    $times = explode(' - ', $time_slot);
    if (count($times) !== 2) {
        redirectWithError("services.php", "Invalid time slot.");
    }

    $start_time = date("H:i:s", strtotime($times[0]));
    $end_time   = date("H:i:s", strtotime($times[1]));

    if ($start_time >= $end_time) {
        redirectWithError("services.php", "Invalid time range.");
    }

    // 4. Determine redirect page based on service type
    $redirectTarget = "services.php";

    $serviceTypeMap = [
        'futsal'      => 'futsalservices.php',
        'badminton'   => 'badmintonservices.php',
        'basketball'  => 'basketballservices.php',
        'tennis'      => 'tennisservices.php',
        'pickleball'  => 'pickelballservices.php'
    ];

    $serviceSql = "SELECT type FROM services WHERE id = ?";
    if ($serviceStmt = $conn->prepare($serviceSql)) {
        $serviceStmt->bind_param("i", $service_id);
        $serviceStmt->execute();
        $result = $serviceStmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $type = strtolower($row['type']);
            if (isset($serviceTypeMap[$type])) {
                $redirectTarget = $serviceTypeMap[$type];
            }
        }
        $serviceStmt->close();
    }

    // 5. Check for overlapping bookings (IMPORTANT)
    $conflictSql = "
        SELECT id FROM bookings
        WHERE service_id = ?
          AND booking_date = ?
          AND status <> 'cancelled'
          AND NOT (end_time <= ? OR start_time >= ?)
    ";

    if ($conflictStmt = $conn->prepare($conflictSql)) {
        $conflictStmt->bind_param("isss", $service_id, $date, $start_time, $end_time);
        $conflictStmt->execute();
        $conflictStmt->store_result();

        if ($conflictStmt->num_rows > 0) {
            $conflictStmt->close();
            redirectWithError($redirectTarget, "Selected time slot is unavailable.");
        }

        $conflictStmt->close();
    } else {
        redirectWithError($redirectTarget, "Database Error.");
    }

    // 6. Insert Booking
    $insertSql = "
        INSERT INTO bookings 
        (user_id, service_id, booking_date, start_time, end_time, customer_name, customer_phone, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')
    ";

    if ($stmt = $conn->prepare($insertSql)) {
        $stmt->bind_param(
            "iisssss",
            $user_id,
            $service_id,
            $date,
            $start_time,
            $end_time,
            $cust_name,
            $cust_phone
        );

        if ($stmt->execute()) {
            header("Location: bookings.php?msg=Booking+Confirmed+Successfully");
            exit();
        } else {
            redirectWithError($redirectTarget, "Database Error.");
        }
    } else {
        redirectWithError($redirectTarget, "SQL Error.");
    }

} else {
    header("Location: services.php");
}
?>
