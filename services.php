<?php
session_start();
// Ensure the path to db_connect.php is correct
require_once 'includes/db_connect.php'; 

// Use Object-Oriented style to fetch services
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Services List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sports Booking</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link text-danger fw-bold" href="services.php">Manage Services</a></li>
                        <li class="nav-item"><a class="nav-link text-danger fw-bold" href="bookings.php">All Bookings</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="services.php">Our Courts</a></li>
                        <li class="nav-item"><a class="nav-link" href="bookings.php">My Bookings</a></li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-danger ms-2" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<body class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Available Services</h2>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="add_service.php" class="btn btn-success">+ Add New Service</a>
    <?php endif; ?>
</div>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Type</th> <th>Price (RM)</th>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <th>Actions</th>
    <?php endif; ?>
</tr>
</thead>
<tbody>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td> 
        <td><?= htmlspecialchars($row['type']) ?></td>
        <td><?= number_format($row['price_per_hour'], 2) ?></td>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <td>
            <a href="edit_service.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete_service.php?id=<?= $row['id'] ?>" 
               class="btn btn-sm btn-danger"
               onclick="return confirm('Are you sure?');">Delete</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php } ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center">No services found.</td></tr>
<?php endif; ?>

</tbody>
</table>

</body>
</html>