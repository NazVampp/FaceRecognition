<?php 
session_start();

// Set session timeout to 15 minutes (900 seconds)
$timeout = 900;  // 15 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();     // unset $_SESSION variable for the runtime
    session_destroy();   // destroy session data in storage
    header("Location: index.php");  // redirect to login page
    exit();
}
$_SESSION['last_activity'] = time(); // update last activity time

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="add-student">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Data</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="add_style.css">
    <link rel="stylesheet" href="style.css">
    <script defer src="js/face-api.min.js"></script>
    <script defer src="capture_faces.js"></script>
</head>
<body class="add-student">
<?php include 'navbar.php'; ?>

    <div id="add-container">
        <h1>Add New Student</h1>

        <form id="studentForm">
    <input type="text" id="name" placeholder="Enter Student Name" required>
    <input type="text" id="matrixNumber" placeholder="Enter Matrix Number" required>
    <input type="text" id="course" placeholder="Enter Course" required>
    <input type="email" id="email" placeholder="Enter Student Email" required>

    <video id="video" autoplay playsinline></video><br>

    <p id="status"></p>

    <h2>Captured Images</h2>
    <div class="capture-buttons-container">
        <button type="button" id="captureFront">Capture Front Face</button>
        <button type="button" id="captureLeft">Capture Left Face</button>
        <button type="button" id="captureRight">Capture Right Face</button>
    </div>

    <div class="captured-images-container">
        <div class="image-block">
            <h3>Front Face</h3>
            <img id="frontImagePreview" src="" alt="Front face preview" style="display: none;">
        </div>
        <div class="image-block">
            <h3>Left Face</h3>
            <img id="leftImagePreview" src="" alt="Left face preview" style="display: none;">
        </div>
        <div class="image-block">
            <h3>Right Face</h3>
            <img id="rightImagePreview" src="" alt="Right face preview" style="display: none;">
        </div>
    </div>

    <button type="button" id="upload">Add Student Data</button>
    <button type="button" id="clear">Clear</button>
</form>

        <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>
    </div>

    <script>
        // Clear button functionality
        document.getElementById('clear').addEventListener('click', function() {
            // Clear input fields
            document.getElementById('name').value = '';
            document.getElementById('matrixNumber').value = '';
            document.getElementById('course').value = '';
            document.getElementById('email').value = '';

            // Clear captured images
            document.getElementById('frontImagePreview').src = '';
            document.getElementById('frontImagePreview').style.display = 'none';
            document.getElementById('leftImagePreview').src = '';
            document.getElementById('leftImagePreview').style.display = 'none';
            document.getElementById('rightImagePreview').src = '';
            document.getElementById('rightImagePreview').style.display = 'none';

            // Reset any other fields or states as needed
            document.getElementById('status').textContent = '';
        });
    </script>
    <script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>

</body>
</html>