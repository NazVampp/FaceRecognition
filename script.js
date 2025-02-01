document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent form submission

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    // Simple client-side validation for empty fields
    if (!username || !password) {
        alert('Please enter both username and password.');
        return;
    }

    const formData = new FormData(this);  // Collect form data

    // Show loading message or disable the form
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Logging in...';

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Get the server response
    .then(data => {
        // Enable the submit button again and change text back
        submitButton.disabled = false;
        submitButton.textContent = 'Login';

        if (data.trim() === 'success') {
            window.location.href = 'scan_face.php';  // Redirect on success
        } else {
            alert('Invalid username or password!');  // Show alert on failure
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        // Enable the submit button again in case of error
        submitButton.disabled = false;
        submitButton.textContent = 'Login';
    });
});
