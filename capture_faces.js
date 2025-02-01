const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const context = canvas.getContext('2d');
const status = document.getElementById('status');

let images = {};
let descriptors = {};

// Load face-api models
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('models')
]).then(startVideo).catch(err => {
    console.error("Error loading models:", err);
    status.textContent = "Error loading models.";
});

// Start webcam
function startVideo(cameraFacing = "user") {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: cameraFacing } })
        .then(stream => {
            video.srcObject = stream;
            status.textContent = "Camera is on.";

            // Apply mirror effect for front-facing camera (user camera)
            if (cameraFacing === "user") {
                video.style.transform = "scaleX(-1)";  // Flip the front camera
            } else {
                video.style.transform = "none";  // Don't flip the rear camera
            }
        })
        .catch(err => {
            console.error("Error accessing webcam:", err);
            if (err.name === 'NotAllowedError') {
                status.textContent = "Permission denied. Please enable camera access.";
            } else if (err.name === 'NotFoundError') {
                status.textContent = "No camera found on this device.";
            } else {
                status.textContent = "Error accessing webcam. Check your device settings.";
            }
        });
}


// Example usage:
// For front camera
startVideo("user");

// For rear camera
startVideo("environment");


// Capture face and extract descriptor
async function captureFace(position) {
    try {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imgData = canvas.toDataURL('image/png');

        const detections = await faceapi.detectSingleFace(canvas, new faceapi.SsdMobilenetv1Options())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detections) {
            images[position] = imgData;
            descriptors[position] = detections.descriptor;
            status.textContent = `${position} face captured successfully!`;

            const imagePreview = document.getElementById(`${position}ImagePreview`);
            imagePreview.src = imgData;
            imagePreview.style.display = 'block';
        } else {
            status.textContent = `No face detected for ${position} face!`;
        }
    } catch (error) {
        console.error(`Error capturing ${position} face:`, error);
        status.textContent = `Error capturing ${position} face. Please try again.`;
    }
}


document.getElementById('captureFront').addEventListener('click', () => captureFace('front'));
document.getElementById('captureLeft').addEventListener('click', () => captureFace('left'));
document.getElementById('captureRight').addEventListener('click', () => captureFace('right'));

// Upload data to PHP script
document.getElementById('upload').addEventListener('click', () => {
    const name = document.getElementById('name').value;
    const matrixNumber = document.getElementById('matrixNumber').value;
    const course = document.getElementById('course').value;
    const email = document.getElementById('email').value;

    if (!name || !matrixNumber || !course || !email) {
        status.textContent = 'Please fill in all student details.';
        return;
    }

    const data = {
        name,
        matrix_number: matrixNumber,
        course,
        email,
        images,
        descriptors
    };

    fetch('upload_data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(result => {
        status.textContent = result;
        if (result.includes('successfully')) {
            document.getElementById('studentForm').reset();  // Reset the form
            document.getElementById('frontImagePreview').style.display = 'none';
            document.getElementById('leftImagePreview').style.display = 'none';
            document.getElementById('rightImagePreview').style.display = 'none';
            images = {};
            descriptors = {};
        }
    })
    .catch(error => {
        console.error('Error:', error);
        status.textContent = 'Error uploading data.';
    });
});
