<?php
// 1. START SESSION
session_start();

// 2. SECURITY: Only Admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 3. DATABASE CONNECTION
require_once 'includes/db_connect.php'; 

$error_msg = "";

// 4. CHECK IF FORM WAS SUBMITTED (Crucial Fix)
// This 'if' ensures we only run the saving logic on POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 5. VALIDATE INPUTS (Defensive Coding)
    // We check if the keys exist to prevent "Undefined array key" errors
    if (isset($_POST['name']) && isset($_POST['type']) && isset($_POST['price_per_hour'])) {
        
        $name = $_POST['name'];
        $type = $_POST['type'];
        $price = $_POST['price_per_hour'];

        // 6. SQL INSERT
        $sql = "INSERT INTO services (name, type, price_per_hour) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssd", $name, $type, $price);
            
            if ($stmt->execute()) {
                // Success: Redirect to list
                header("Location: services.php?msg=Service+Added");
                exit();
            } else {
                $error_msg = "Error Saving: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Database Error: " . $conn->error;
        }

    } else {
        $error_msg = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'template.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Add New Service</h4>
                </div>
                <div class="card-body">
                    
                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>

                    <form action="add_service.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Service Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Futsal Court 1">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Service Type</label>
                            <select class="form-select" name="type" required>
                                <option value="Futsal">Futsal</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Basketball">Basketball</option>
                                <option value="Tennis">Tennis</option>
                                <option value="Pickleball">Pickleball</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price per Hour (RM)</label>
                            <input type="number" step="0.01" class="form-control" name="price_per_hour" required placeholder="0.00">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Save Service</button>
                            <a href="services.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>