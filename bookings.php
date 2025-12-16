<?php
// 1. START SESSION
session_start();

// 2. CHECK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 3. CONNECT TO DATABASE
// Try to find the file in includes/ or the main folder
if (file_exists('includes/db_connect.php')) {
    require_once 'includes/db_connect.php';
} else {
    require_once 'db_connect.php';
}

$user_id = $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer';

// 4. PREPARE QUERY (Using 'services.name', NOT 'service_name')
if ($role === 'admin') {
    // ADMIN: See ALL bookings
    $sql = "SELECT bookings.*, users.username, services.name as court_name 
            FROM bookings 
            JOIN users ON bookings.user_id = users.id 
            JOIN services ON bookings.service_id = services.id 
            ORDER BY bookings.booking_date DESC";
    $stmt = $conn->prepare($sql);
} else {
    // CUSTOMER: See ONLY MINE
    $sql = "SELECT bookings.*, services.name as court_name 
            FROM bookings 
            JOIN services ON bookings.service_id = services.id 
            WHERE bookings.user_id = ? 
            ORDER BY bookings.booking_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

// 5. EXECUTE
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    die("Error executing query: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'template.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">
        <?php echo ($role === 'admin') ? "All Customer Bookings" : "My Booking History"; ?>
    </h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <?php if ($role === 'admin'): ?>
                            <th>Customer</th>
                        <?php endif; ?>
                        <th>Court</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                
                                <?php if ($role === 'admin'): ?>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <?php endif; ?>

                                <td><?php echo htmlspecialchars($row['court_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                                
                                <td>
                                    <?php 
                                        // Display Time Range nicely
                                        echo date("g:i A", strtotime($row['start_time'])) . " - " . 
                                             date("g:i A", strtotime($row['end_time'])); 
                                    ?>
                                </td>
                                
                                <td>
                                    <span class="badge bg-success">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No bookings found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer class="bg-light text-center py-3 mt-4">
    <p class="mb-0">&copy; 2024 Sports Court Booking System</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
</body>
</html>