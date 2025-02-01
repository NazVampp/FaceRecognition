<?php
session_start();

// Receive the image data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['image'])) {
    // Strip the base64 prefix if it exists
    $imageData = $data['image'];
    if (strpos($imageData, 'data:image') === 0) {
        $imageData = explode(',', $imageData)[1];
    }

    // Decode the base64 image
    $decodedImage = base64_decode($imageData);

    // Create a unique filename for the image
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the folder if it doesn't exist
    }
    $filename = $uploadDir . 'non_student_' . time() . '.png';

    // Save the image to the server
    if (file_put_contents($filename, $decodedImage)) {
        $_SESSION['captured_image'] = $filename;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save image.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No image data received.']);
}

?>
