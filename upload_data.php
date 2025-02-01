<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'face_recognition');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (
    isset($data['name']) &&
    isset($data['matrix_number']) &&
    isset($data['course']) &&
    isset($data['email']) &&
    isset($data['images']) &&
    isset($data['descriptors'])
) {
    // Sanitize inputs
    $name = htmlspecialchars($data['name']);
    $matrix_number = htmlspecialchars($data['matrix_number']);
    $course = htmlspecialchars($data['course']);
    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    $images = $data['images'];
    $descriptors = $data['descriptors'];

    if (
        !isset($images['front']) ||
        !isset($images['left']) ||
        !isset($images['right'])
    ) {
        echo "Error: Please capture the front, left, and right face images before uploading.";
        exit();
    }

    $picture_front = $images['front'];
    $picture_left = $images['left'];
    $picture_right = $images['right'];

    $face_descriptor_front = json_encode($descriptors['front']);
    $face_descriptor_left = json_encode($descriptors['left']);
    $face_descriptor_right = json_encode($descriptors['right']);

    // Check for empty face descriptors
    if (empty($face_descriptor_front) || empty($face_descriptor_left) || empty($face_descriptor_right)) {
        echo "Error: Face descriptors are missing!";
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("
        INSERT INTO students 
        (name, matrix_number, course, email, picture_front, picture_left, picture_right, 
        face_descriptor_front, face_descriptor_left, face_descriptor_right) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        'ssssssssss', 
        $name, 
        $matrix_number, 
        $course, 
        $email, 
        $picture_front, 
        $picture_left, 
        $picture_right, 
        $face_descriptor_front, 
        $face_descriptor_left, 
        $face_descriptor_right
    );

    // Execute the statement with error handling
    try {
        if ($stmt->execute()) {
            echo "Faces and student data uploaded successfully!";
        } else {
            echo "Error: Failed to execute the statement.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo "Error: Matrix number already exists!";
        } else {
            echo "Database error: " . $e->getMessage();
        }
    }

    $stmt->close();
} else {
    echo "Invalid data received!";
}

$conn->close();
?>
