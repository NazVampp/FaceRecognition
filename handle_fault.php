<?php
session_start();

// Check if student data exists in session
if (!isset($_SESSION['student'])) {
    header("Location: index.php"); // Redirect if no session data
    exit();
}

// Get the fault selected by the admin
$fault = $_POST['fault'];
$student = $_SESSION['student'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'face_recognition');

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch full student data from the students table using the matrix number
$matrix_number = $student['matrix_number']; // Get the matrix number from session
$sql = "SELECT name, matrix_number, course, email FROM students WHERE matrix_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matrix_number);
$stmt->execute();
$result = $stmt->get_result();

// Check if the student exists in the database
if ($result->num_rows > 0) {
    $studentData = $result->fetch_assoc();
    
    // Prepare the SQL query to insert the student data with the fault
    $stmt2 = $conn->prepare("INSERT INTO summon (name, matrix_number, course, email, fault) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssss", $studentData['name'], $studentData['matrix_number'], $studentData['course'], $studentData['email'], $fault);
    
    // Execute the insert query
    if ($stmt2->execute()) {
        // Set a session message on success
        $_SESSION['message'] = "Fault for student " . htmlspecialchars($studentData['name']) . " (" . htmlspecialchars($studentData['matrix_number']) . ") has been recorded as: " . htmlspecialchars($fault);
    } else {
        // Set a session message on error
        $_SESSION['message'] = "Error: " . $stmt2->error;
    }
    
    // Close the second statement
    $stmt2->close();
} else {
    $_SESSION['message'] = "Student not found in the database.";
}

// Close the first statement and the connection
$stmt->close();
$conn->close();

// Redirect back to scan_face.php
header("Location: scan_face.php");
exit();
?>
