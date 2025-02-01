<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "face_recognition";

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$table = $_POST['table'];

// Sanitize the table name (Allow only 'summon' and 'not_students')
$valid_tables = ['summon', 'not_students'];
if (!in_array($table, $valid_tables)) {
    echo "<p>Invalid table name!</p>";
    exit();
}

// Sanitize the ID to prevent SQL injection
$id = (int) $id; // Cast to integer to prevent SQL injection

// Fetch record based on ID and table
$query = "SELECT * FROM $table WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id); // 'i' for integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Display record details
    echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";  // Use htmlspecialchars to prevent XSS

    // Add more details based on your table structure
    if ($table == 'summon') {
        echo "<p>Matrix Number: " . htmlspecialchars($row['matrix_number']) . "</p>";
        echo "<p>Course: " . htmlspecialchars($row['course']) . "</p>";
        echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
        echo "<p>Fault: " . htmlspecialchars($row['fault']) . "</p>";
        echo "<p>Created at: " . htmlspecialchars($row['created_at']) . "</p>";
    } else if ($table == 'not_students') {
        $picturePath = $row['picture'];
        if (!str_starts_with($picturePath, 'uploads/')) {
            $picturePath = 'uploads/' . $picturePath;
            }

                echo "<p><img src='" . htmlspecialchars($picturePath) . "' alt='Picture' height='150'></p>";
        echo "<p>IC Number: " . htmlspecialchars($row['ic_number']) . "</p>";
        echo "<p>Created at: " . htmlspecialchars($row['created_at']) . "</p>";
        // Ensure the 'picture' field doesn't contain 'uploads/' already


    }
} else {
    echo "<p>No record found!</p>";
}

$stmt->close();
$conn->close();
?>
