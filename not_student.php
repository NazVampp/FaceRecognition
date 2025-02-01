<?php
session_start();

// Check if image data exists in session
if (!isset($_SESSION['captured_image'])) {
    header("Location: scan_face.php"); // Redirect if no image data
    exit();
}

$capturedImage = $_SESSION['captured_image'];

// Ensure buttons are displayed in the navbar
$showButtons = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Student Face</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Ensure this path is correct -->
</head>
<body class="not-student-page"> <!-- Ensure the class is applied -->
    <?php include 'navbar.php'; ?>

    <h1>Captured Face - Non-Student</h1>

    <div class="capture-container">
    <img src="<?php echo htmlspecialchars($capturedImage); ?>" alt="Captured Face" style="width: 300px; height: auto; border: 1px solid #ccc;">


        <form action="handle_not_student.php" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="ic_number">IC Number:</label>
            <input type="text" id="ic_number" name="ic_number" required><br><br>

            <button type="submit">Submit</button>
        </form>
    </div>
    <script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>

</body>
</html>
