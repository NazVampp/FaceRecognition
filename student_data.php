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

// Database connection
$servername = "localhost";
$username = "root";          // Default XAMPP username
$password = "";              // Default XAMPP password is empty
$dbname = "face_recognition"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $id = $conn->real_escape_string($_POST['id']);
    
    // Fetch image paths to delete from the server
    $query = $conn->query("SELECT picture_front, picture_left, picture_right FROM students WHERE id = '$id'");
    if ($student = $query->fetch_assoc()) {
        // Delete images from the server if they exist
        if (file_exists($student['picture_front']) && !unlink($student['picture_front'])) {
            echo "<script>alert('Error deleting front image file.');</script>";
        }
        if (file_exists($student['picture_left']) && !unlink($student['picture_left'])) {
            echo "<script>alert('Error deleting left image file.');</script>";
        }
        if (file_exists($student['picture_right']) && !unlink($student['picture_right'])) {
            echo "<script>alert('Error deleting right image file.');</script>";
        }
    }

    // Delete the student record from the database
    if ($conn->query("DELETE FROM students WHERE id = '$id'")) {
        echo "<script>alert('Student deleted successfully!'); window.location.href = 'student_data.php';</script>";
    } else {
        echo "<script>alert('Error deleting student: " . $conn->error . "');</script>";
    }
}

// Handle edit requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $matrix_number = $conn->real_escape_string($_POST['matrix_number']);
    $course = $conn->real_escape_string($_POST['course']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Check if the matrix number is already taken by another student
    $checkMatrixQuery = $conn->query("SELECT id FROM students WHERE matrix_number = '$matrix_number' AND id != '$id'");
    if ($checkMatrixQuery->num_rows > 0) {
        echo "<script>alert('Matrix number already exists. Please choose a different matrix number.');</script>";
    } else {
        // Update the student data
        $stmt = $conn->prepare("UPDATE students SET name = ?, matrix_number = ?, course = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $matrix_number, $course, $email, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Student data updated successfully!'); window.location.href = 'student_data.php';</script>";
        } else {
            echo "<script>alert('Error updating student data: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Search functionality
$searchQuery = ""; // Default query (show all students)
if (isset($_POST['search_matrix_number']) && !empty($_POST['search_matrix_number'])) {
    $search_matrix_number = $conn->real_escape_string($_POST['search_matrix_number']);
    $searchQuery = "WHERE matrix_number = '$search_matrix_number'"; // Search by matrix number
} else {
    $searchQuery = ""; // This will show all students if input is empty
}


// Fetch all students from the database, regardless of search query
$result = $conn->query("SELECT * FROM students $searchQuery");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data</title>
    <link href="images/PBantuan.png" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css" type="text/css" charset="utf-8">
</head>

<body class="student-data-page">
    <div class="nav-student">
        <?php include 'navbar.php'; ?> <!-- Include the navbar -->
    </div>

    <div class="student-data-content">
        <h1>Manage Student Data</h1>

        <div class="header-actions">
            <a href="add_student.php">
                <button class="go-to-add-btn">Add Student</button>
            </a>

            <!-- Show the Back Button only if the user has searched -->
            <?php if (!empty($_POST['search_matrix_number'])): ?>
                <a href="student_data.php" class="back-btn">
                    <button class="back-button">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </a>
            <?php endif; ?>

            <!-- Search Form -->
            <form method="POST" action="student_data.php" class="search-form">
                <input type="text" id="search_matrix_number" name="search_matrix_number" placeholder="Enter matrix number" />
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Table to display student data -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Matrix Number</th>
                <th>Course</th>
                <th>Email</th>
                <th>Front Picture</th>
                <th>Left Picture</th>
                <th>Right Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Fetch students based on the search query
        $result = $conn->query("SELECT * FROM students $searchQuery");

        if ($result->num_rows > 0) {
            // Display students
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['matrix_number'] . "</td>";
                echo "<td>" . $row['course'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td><img src='" . $row['picture_front'] . "' alt='Front' height='50'></td>";
                echo "<td><img src='" . $row['picture_left'] . "' alt='Left' height='50'></td>";
                echo "<td><img src='" . $row['picture_right'] . "' alt='Right' height='50'></td>";
                echo "<td>
                        <div class='button-group'>
                            <button class='icon-btn view' onclick='viewStudentDetails(" . $row['id'] . ", \"" . $row['name'] . "\", \"" . $row['matrix_number'] . "\", \"" . $row['course'] . "\", \"" . $row['email'] . "\", \"" . $row['picture_front'] . "\", \"" . $row['picture_left'] . "\", \"" . $row['picture_right'] . "\")'>
                                <i class='fa-solid fa-eye'></i>
                            </button>
                            <button class='icon-btn edit' onclick='openEditModal(" . $row['id'] . ", \"" . $row['name'] . "\", \"" . $row['course'] . "\", \"" . $row['email'] . "\", \"" . $row['picture_front'] . "\", \"" . $row['picture_left'] . "\", \"" . $row['picture_right'] . "\", \"" . $row['matrix_number'] . "\")'>
                                <i class='fa-solid fa-pen-to-square'></i>
                            </button>
                            <form action='student_data.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <button type='submit' name='delete' class='icon-btn delete'>
                                    <i class='fa-solid fa-trash'></i>
                                </button>
                            </form>
                        </div>
                    </td>";
                echo "</tr>";
            }
        } else {
            // Display message if no students match the search or if the table is empty
            if ($searchQuery === "") {
                echo "<tr><td colspan='8'>No student data</td></tr>";
            } else {
                echo "<tr><td colspan='8'>No student found with this matrix number.</td></tr>";
            }
        }
        ?>
    </tbody>
    </table>
</div>


        <!-- The View Student Details Modal -->
        <div id="viewStudentModal" class="modal student-details-modal">
            <div class="modal-content">
                <span class="close" onclick="closeViewStudentModal()">&times;</span>
                <h2>Student Details</h2>
                <div class="student-images">
                    <img id="viewPictureFront" src="" alt="Front Picture">
                    <img id="viewPictureLeft" src="" alt="Left Picture">
                    <img id="viewPictureRight" src="" alt="Right Picture">
                </div>
                <p>Name: <span id="viewName"></span></p>
                <p>Matrix Number: <span id="viewMatrixNo"></span></p>
                <p>Course: <span id="viewCourse"></span></p>
                <p>Email: <span id="viewEmail"></span></p>

                <!-- Summon Button -->
<a id="summonButton" href="#">
    Summon
</a>

            </div>
        </div>

        <script>
function viewStudentDetails(id, name, matrixNo, course, email, pictureFront, pictureLeft, pictureRight) {
    // Populate modal data
    document.getElementById("viewStudentModal").style.display = "block";
    document.getElementById("viewName").innerText = name;
    document.getElementById("viewMatrixNo").innerText = matrixNo;
    document.getElementById("viewCourse").innerText = course;
    document.getElementById("viewEmail").innerText = email;
    document.getElementById("viewPictureFront").src = pictureFront;
    document.getElementById("viewPictureLeft").src = pictureLeft;
    document.getElementById("viewPictureRight").src = pictureRight;

    // Update the summon button URL with student data
    const summonButton = document.getElementById("summonButton");
    summonButton.href = `summon_student.php?id=${id}&name=${encodeURIComponent(name)}&matrixNo=${matrixNo}&course=${encodeURIComponent(course)}&email=${encodeURIComponent(email)}`;
}



            function closeViewStudentModal() {
                document.getElementById("viewStudentModal").style.display = "none";
            }
        </script>

        <!-- The Edit Student Modal -->
<div id="editStudentModal" class="modal student-edit-modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditStudentModal()">&times;</span>
        <h2>Edit Student Data</h2>
        <form method="POST" action="student_data.php">
            <input type="hidden" id="editId" name="id">

<!-- Display Pictures (Non-editable) -->
<div class="image-preview-section">
    <img id="editPictureFront" src="" alt="Front Picture" height="100">
    <img id="editPictureLeft" src="" alt="Left Picture" height="100">
    <img id="editPictureRight" src="" alt="Right Picture" height="100">
</div>


            <label for="editName">Name:</label>
            <input type="text" id="editName" name="name" required>

            <label for="editMatrixNumber">Matrix Number:</label>
            <input type="text" id="editMatrixNumber" name="matrix_number" required>

            <label for="editCourse">Course:</label>
            <input type="text" id="editCourse" name="course" required>

            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="email" required>

            <button type="submit" name="edit">Save Changes</button>
        </form>
    </div>
</div>

<script>
    // Function to open the Edit Modal and populate it with student data
// Function to open the Edit Modal and populate it with student data
function openEditModal(id, name, course, email, pictureFront, pictureLeft, pictureRight, matrixNumber) {
    
        // Populate image sources
        document.getElementById("editPictureFront").src = pictureFront;
    document.getElementById("editPictureLeft").src = pictureLeft;
    document.getElementById("editPictureRight").src = pictureRight;
    
    document.getElementById("editId").value = id;
    document.getElementById("editName").value = name;
    document.getElementById("editMatrixNumber").value = matrixNumber;
    document.getElementById("editCourse").value = course;
    document.getElementById("editEmail").value = email;

    document.getElementById("editStudentModal").style.display = "block";
}


    // Function to close the Edit Modal
    function closeEditStudentModal() {
        document.getElementById("editStudentModal").style.display = "none";
    }

    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        const editModal = document.getElementById("editStudentModal");
        if (event.target === editModal) {
            closeEditStudentModal();
        }
    }
</script>



    </div>
    <script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>

</body>
</html>
