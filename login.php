<?php
session_start();

// Database connection parameters
$servername = "localhost";
$dbUsername = "root";      // Default username for XAMPP
$dbPassword = "";          // Default password for XAMPP is empty
$dbName = "face_recognition"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM auxiliary_police WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (hash('sha256', $password) === $hashedPassword) {
            $_SESSION['username'] = $username;
            echo 'success';
        } else {
            echo 'failure';
        }
    } else {
        echo 'failure';
    }

    $stmt->close();
}

$conn->close();
?>
