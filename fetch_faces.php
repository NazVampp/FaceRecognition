<?php
header('Content-Type: application/json');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'face_recognition');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$result = $conn->query("SELECT name, matrix_number, course, email, face_descriptor_front FROM students");

if ($result->num_rows > 0) {
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'name' => $row['name'],
            'matrix_number' => $row['matrix_number'],
            'course' => $row['course'],
            'email' => $row['email'],
            'face_descriptor_front' => $row['face_descriptor_front']
        ];
    }
    echo json_encode($students);
} else {
    echo json_encode([]);
}

$conn->close();
?>
