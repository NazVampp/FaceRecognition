<!-- navbar.php -->
<nav class="navbar">
    <div class="logo">
        <img src="images/PBantuan.png" alt="PBantuan Logo" class="nav-logo">
        <img src="images/uitm.png" alt="UiTM Logo" class="nav-logo">
    </div>

    <!-- Hamburger Icon for Small Screens -->
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Navbar Links (Initially hidden on mobile) -->
    <?php if (!isset($showButtons) || $showButtons): ?>
        <ul class="nav-links" id="navLinks">
            <li><a href="scan_face.php">Scan Face</a></li>
            <li><a href="student_data.php">Student Data</a></li>
            <li><a href="view_records.php">Records</a></li>
            <li><a href="logout.php" class="logout-link" aria-label="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a></li>
        </ul>
    <?php endif; ?>
</nav>

<script>
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        navLinks.classList.toggle("active");
    }
</script>
