<?php require 'includes/session_check.php'; ?>
<html>
    <head>
        <title>Court Booking</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="style.css">
    </head>
<body>

    <header>
       <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
          <div class="container">
            <a class="navbar-brand" href="index.php">Sports Booking</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                  <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="services.php">Our Courts</a>
                </li>

                <?php if (isset($_SESSION['username'])): ?>
                    
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-black ms-2" href="logout.php">Logout</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>

                <?php endif; ?>
                </ul>
            </div>
          </div>
        </nav>
    </header>

    <div class="bg-light p-5 rounded-3 mb-4 text-center">
        <div class="container-fluid py-5">
          <h1 class="display-5 fw-bold">Welcome to Our Sports Complex</h1>
          <p class="col-md-8 fs-4 mx-auto">Book your Futsal, Badminton, or Pickleball court online today!</p>
          <a href="register.php" class="btn btn-primary btn-lg" type="button">Get Started</a>
        </div>
    </div>

    <div class="container">
        <h2 class="text-center mb-4">Our Sports</h2>
        
        <div class="row justify-content-center">
            
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h3 class="card-title">Futsal</h3>
                        <p class="card-text">3 international standard courts suitable for tournaments and casual play.</p>
                        <a href="futsalservices.php" class="btn btn-outline-primary">View Courts</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h3 class="card-title">Badminton</h3>
                        <p class="card-text">3 professional indoor courts with high-quality flooring and lighting.</p>
                        <a href="badmintonservices.php" class="btn btn-outline-primary">View Courts</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h3 class="card-title">Pickleball</h3>
                        <p class="card-text">3 brand new courts ready for the fastest-growing sport in the world.</p>
                        <a href="pickelballservices.php" class="btn btn-outline-primary">View Courts</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h3 class="card-title">Tennis</h3>
                        <p class="card-text">3 brand new courts ready for the fastest-growing sport in the world.</p>
                        <a href="tennisservices.php" class="btn btn-outline-primary">View Courts</a>
                    </div>
                </div>
            </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <h3 class="card-title">Basketball</h3>
                    <p class="card-text">3 brand new courts ready for the fastest-growing sport in the world.</p>
                    <a href="basketballservices.php" class="btn btn-outline-primary">View Courts</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Sports Court Booking System</p>
    </footer>

    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>