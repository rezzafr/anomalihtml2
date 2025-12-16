<?php
require 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email,);
    $checkEmail->execute();
    $checkEmail->store_result();

     if ($checkEmail->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.php';</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $checkEmail->close();
    $conn->close();
}
?>