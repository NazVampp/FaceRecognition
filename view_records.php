<?php
session_start();

// Set session timeout to 15 minutes (900 seconds)
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Check for admin login session
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "face_recognition";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete requests for summon or not_students
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $table = $_POST['table']; // Determine which table to delete from
    $id = $conn->real_escape_string($_POST['id']);

    // Perform the delete
    $query = "DELETE FROM $table WHERE id = '$id'";
    if ($conn->query($query)) {
        echo "<script>alert('Record deleted successfully!'); window.location.href = 'view_records.php';</script>";
    } else {
        echo "<script>alert('Error deleting record: " . $conn->error . "');</script>";
    }
}

// Fetch summon records with a limit of 5
$summon_result = $conn->query("SELECT * FROM summon");

// Fetch not_students records with a limit of 5
$not_students_result = $conn->query("SELECT * FROM not_students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Records</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
</head>

<body class="view-records-page">
    <div class="nav-student">
        <?php include 'navbar.php'; ?>
    </div>

    <div class="view-records-content">
        <h1>View Records</h1>

        <!-- Table for Summon Records -->
        <h2>Student Summons</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Matrix Number</th>
                        <th>Course</th>
                        <th>Email</th>
                        <th>Fault</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $summon_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['matrix_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['course']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['fault']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <!-- View Button - Open Modal -->
                                <button class="icon-btn" onclick="openModal('<?php echo $row['id']; ?>', 'summon')">
                                    <i class="fa-solid fa-eye"></i> <!-- View Icon Only -->
                                </button>

                                <!-- Delete Button -->
                                <form action="view_records.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="table" value="summon">
                                    <button type="submit" name="delete" class="icon-btn delete">
                                        <i class="fa-solid fa-trash"></i> <!-- Trash Icon Only -->
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Table for Non-Students Records -->
        <h2>Not Students</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>IC Number</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $not_students_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['ic_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <!-- View Button - Open Modal -->
                                <button class="icon-btn" onclick="openModal('<?php echo $row['id']; ?>', 'not_students')">
                                    <i class="fa-solid fa-eye"></i> <!-- View Icon Only -->
                                </button>

                                <!-- Delete Button -->
                                <form action="view_records.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="table" value="not_students">
                                    <button type="submit" name="delete" class="icon-btn delete">
                                        <i class="fa-solid fa-trash"></i> <!-- Trash Icon Only -->
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Viewing Record Details -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        // Open Modal with Record Details
        function openModal(id, table) {
            var modal = document.getElementById("myModal");
            var modalBody = document.getElementById("modal-body");

            // Fetch data based on ID and table
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "get_record.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    modalBody.innerHTML = xhr.responseText;
                    modal.style.display = "block";
                }
            };
            xhr.send("id=" + id + "&table=" + table);
        }

        // Close Modal
        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById("navLinks");
            navLinks.classList.toggle("active");
        }
    </script>

</body>
</html>
