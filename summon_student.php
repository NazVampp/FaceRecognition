<?php
session_start();

// Check if all required GET parameters are available
if (!isset($_GET['id']) || !isset($_GET['name']) || !isset($_GET['matrixNo']) || !isset($_GET['course']) || !isset($_GET['email'])) {
    header("Location: student_data.php");
    exit();
}

// Retrieve student data from GET parameters
$student = [
    'id' => $_GET['id'],
    'name' => $_GET['name'],
    'matrix_number' => $_GET['matrixNo'],
    'course' => $_GET['course'],
    'email' => $_GET['email']
];

// Database connection
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP is empty
$dbname = "face_recognition";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fault = $conn->real_escape_string($_POST['fault']);
    $name = $conn->real_escape_string($student['name']);
    $matrix_number = $conn->real_escape_string($student['matrix_number']);
    $course = $conn->real_escape_string($student['course']);
    $email = $conn->real_escape_string($student['email']);

    // Insert the fault into the summon table
    $stmt = $conn->prepare("INSERT INTO summon (name, matrix_number, course, email, fault, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $name, $matrix_number, $course, $email, $fault);

    if ($stmt->execute()) {
        echo "<script>alert('Fault submitted successfully.'); window.location.href = 'student_data.php';</script>";
    } else {
        echo "<script>alert('Error submitting fault: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summon Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="summon-page">

<?php include 'navbar.php'; ?>

    <h1>Summon Student</h1>

    <div class="student-details">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
        <p><strong>Matrix Number:</strong> <?php echo htmlspecialchars($student['matrix_number']); ?></p>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    </div>

    <form action="summon_student.php?id=<?php echo htmlspecialchars($student['id']); ?>&name=<?php echo urlencode($student['name']); ?>&matrixNo=<?php echo htmlspecialchars($student['matrix_number']); ?>&course=<?php echo urlencode($student['course']); ?>&email=<?php echo urlencode($student['email']); ?>" method="POST">
        <label for="fault">Select Fault:</label>
        <select name="fault" id="fault" required>
    <option value="Absence">Absence</option>
    <option value="Academic Misconduct">Academic Misconduct</option>
    <option value="Disruptive Behavior">Disruptive Behavior</option>
    <option value="Dress Code Violation">Dress Code Violation</option>
    <option value="Excessive Noise">Excessive Noise</option>
    <option value="Failure to Comply with Authorities">Failure to Comply with Authorities</option>
    <option value="Failure to Present ID">Failure to Present Student ID</option>
    <option value="Fraudulent Activity">Fraudulent Activity</option>
    <option value="Harassment">Harassment</option>
    <option value="Illegal Gathering">Illegal Gathering</option>
    <option value="Late Arrival">Late Arrival</option>
    <option value="Misconduct">Misconduct</option>
    <option value="Parking Violation">Parking Violation</option>
    <option value="Possession of Weapon">Possession of Weapon</option>
    <option value="Smoking in Prohibited Area">Smoking in Prohibited Area</option>
    <option value="Tampering with Safety Equipment">Tampering with Safety Equipment</option>
    <option value="Theft">Theft</option>
    <option value="Unauthorized Entry">Unauthorized Entry</option>
    <option value="Unauthorized Use of Facilities">Unauthorized Use of Facilities</option>
    <option value="Using Fake ID">Using Fake ID</option>
    <option value="Vandalism">Vandalism</option>
</select>

        <br><br>
        <button type="submit">Submit Fault</button>
    </form>

</body>
</html>
