<?php
session_start();

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $ic_number = $_POST['ic_number'];
    $capturedImage = $_SESSION['captured_image'];

    // Strip the base64 prefix if it exists
    if (strpos($capturedImage, 'data:image') === 0) {
        $capturedImageData = explode(',', $capturedImage)[1];
    } else {
        $capturedImageData = $capturedImage;
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'face_recognition');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Insert captured face data into the 'not_students' table
    $stmt = $conn->prepare("INSERT INTO not_students (name, ic_number, picture) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $ic_number, $capturedImageData);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Non-student details recorded successfully.";
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Clear the captured image from session
    unset($_SESSION['captured_image']);

    // Redirect back to the scan page
    header("Location: scan_face.php");
    exit();
} else {
    header("Location: scan_face.php");
    exit();
}
?>
