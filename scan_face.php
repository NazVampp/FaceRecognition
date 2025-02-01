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
<html lang="en" class="scan-page">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Face</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
    <script defer src="js/face-api.min.js"></script>
    <script defer src="recognize_faces.js"></script>

    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['message'])): ?>
                alert("<?php echo addslashes($_SESSION['message']); ?>");
                <?php unset($_SESSION['message']); // Clear the message after showing ?>
            <?php endif; ?>
        };
    </script>
    
</head>

<body class="scan-face-page">
    <?php include 'navbar.php'; ?>

    <div class="scan-container">
    <h1>Face Recognition</h1>
    <video id="video" autoplay playsinline></video><br>
    <button id="recognizeButton">Recognize Face</button>
    <p id="status">Status: Waiting for recognition...</p>
    <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>

    <!-- Dynamically added buttons -->
    <button id="summonButton" style="display:none;" onclick="window.location.href='summon.php'">Go to Summon</button>
    <button id="captureFaceButton" type="button" style="display:none;">Capture Face</button>
    
    </div>
    <script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>


</body>
</html>
