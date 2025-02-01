<?php
session_start();

// Get the student data from the POST request
$studentData = json_decode(file_get_contents('php://input'), true);

// Store student data in the session
$_SESSION['student'] = $studentData;

// Return a success response
echo json_encode(['status' => 'success']);
?>
