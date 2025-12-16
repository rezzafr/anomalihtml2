<?php
// 1. SESSION & SECURITY
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. CONNECT TO DATABASE
require_once 'includes/db_connect.php'; 

// 3. FETCH EXISTING DATA (To fill the form)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prepare statement to fetch service details
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // NOW WE HAVE THE DATA in $row['name'], $row['type'], etc.
    } else {
        echo "Service not found.";
        exit();
    }
} else {
    header("Location: services.php");
    exit();
}

// 4. HANDLE UPDATE (When button is clicked)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price_per_hour']; // Must match form input name

    $update_sql = "UPDATE services SET name=?, type=?, price_per_hour=? WHERE id=?";
    
    if ($stmt = $conn->prepare($update_sql)) {
        // "ssdi" = String, String, Double, Integer
        $stmt->bind_param("ssdi", $name, $type, $price, $id);
        
        if ($stmt->execute()) {
            header("Location: services.php?msg=Service+Updated");
            exit();
        } else {
            $error = "Error updating: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<div class="card shadow col-md-6 mx-auto">
    <div class="card-header bg-warning text-dark">
        <h4>Edit Service</h4>
    </div>
    <div class="card-body">
        
        <form action="edit_service.php?id=<?= $id ?>" method="POST">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <div class="mb-3">
                <label>Service Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Type</label>
                <select name="type" class="form-select">
                    <option value="Futsal" <?= ($row['type'] == 'Futsal') ? 'selected' : '' ?>>Futsal</option>
                    <option value="Badminton" <?= ($row['type'] == 'Badminton') ? 'selected' : '' ?>>Badminton</option>
                    <option value="Basketball" <?= ($row['type'] == 'Basketball') ? 'selected' : '' ?>>Basketball</option>
                    <option value="Tennis" <?= ($row['type'] == 'Tennis') ? 'selected' : '' ?>>Tennis</option>
                    <option value="Pickleball" <?= ($row['type'] == 'Pickleball') ? 'selected' : '' ?>>Pickleball</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Price (RM)</label>
                <input type="number" step="0.01" name="price_per_hour" class="form-control" value="<?= $row['price_per_hour'] ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Service</button>
            <a href="services.php" class="btn btn-secondary">Cancel</a>
        </form>
        
    </div>
</div>

</body>
</html>