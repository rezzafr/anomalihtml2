<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Court Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Optional: Add space so navbar doesn't cover content */
        body { padding-top: 20px; } 
    </style>
</head>
<body>

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