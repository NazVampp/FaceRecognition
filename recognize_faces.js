const video = document.getElementById('video');
const status = document.getElementById('status');

// Load face-api models
Promise.all([
    faceapi.nets.ssdMobilenetv1.loadFromUri('models'),
    faceapi.nets.faceRecognitionNet.loadFromUri('models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('models')
]).then(startVideo);

// Start the webcam
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



// Capture and recognize face
document.getElementById('recognizeButton').addEventListener('click', async () => {
    status.textContent = "Recognizing face...";

    // Capture the current frame
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;  // Set canvas width to video width
    canvas.height = video.videoHeight;  // Set canvas height to video height
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const detection = await faceapi.detectSingleFace(canvas).withFaceLandmarks().withFaceDescriptor();

    if (!detection) {
        status.innerHTML = `<span class="error-text">No face detected. Try again.</span>`;
        return;
    }

    // Fetch the face descriptors from the database
    fetch('fetch_faces.php')
        .then(response => response.json())
        .then(async students => {
            const labeledDescriptors = students.map(student => {
                return new faceapi.LabeledFaceDescriptors(student.name, [
                    new Float32Array(JSON.parse(student.face_descriptor_front))
                ]);
            });

            const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors);
            const bestMatch = faceMatcher.findBestMatch(detection.descriptor);

            if (bestMatch.distance < 0.6) {
                const matchedStudent = students.find(student => student.name === bestMatch.label);
                status.innerHTML = `
                    Recognition Result: ${bestMatch.toString()}<br>
                    <p><span class="label">Name:</span> <span class="result">${matchedStudent.name}</span></p>
                    <p><span class="label">Matrix Number:</span> <span class="result">${matchedStudent.matrix_number}</span></p>
                    <p><span class="label">Course:</span> <span class="result">${matchedStudent.course}</span></p>
                    <p><span class="label">Email:</span> <span class="result">${matchedStudent.email}</span></p>
                `;

                // Store student data in session
                fetch('store_student_session.php', {
                    method: 'POST',
                    body: JSON.stringify(matchedStudent),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(response => response.json())
                  .then(data => {
                      // Show the summon button
                      document.getElementById('summonButton').style.display = 'inline-block';
                      document.getElementById('captureFaceButton').style.display = 'none'; // Hide the capture face button
                  })
                  .catch(error => {
                      console.error('Error storing student data:', error);
                      status.innerHTML = `<span class="error-text">Error storing student data.</span>`;
                  });

            } else {
                status.innerHTML = `<span class="error-text">No matching student found.</span>`;

                // Show the capture face button
                document.getElementById('summonButton').style.display = 'none'; // Hide the summon button
                document.getElementById('captureFaceButton').style.display = 'inline-block';
            }
        })
        .catch(error => {
            console.error('Error fetching face data:', error);
            status.innerHTML = `<span class="error-text">Error fetching face data.</span>`;
        });
});

// Capture face for non-student
document.getElementById('captureFaceButton').addEventListener('click', () => {
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;  // Ensure full video width
    canvas.height = video.videoHeight;  // Ensure full video height
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/png');

    // Send the image data to the server to store in session
    fetch('capture_image.php', {
        method: 'POST',
        body: JSON.stringify({ image: imageData }),
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(response => {
        window.location.href = 'not_student.php';
    }).catch(error => {
        console.error('Error capturing image:', error);
        status.innerHTML = `<span class="error-text">Error capturing image.</span>`;
    });
});
