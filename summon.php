<?php
session_start();

// Check if session exists
if (!isset($_SESSION['student'])) {
    header("Location: index.php"); // Redirect to login page if no session data is found
    exit();
}

// Retrieve student data from the session
$student = $_SESSION['student'];

// Ensure buttons are displayed in the navbar
$showButtons = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Summon Student</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
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

    <form action="handle_fault.php" method="POST">
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
    <script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>

</body>
</html>
