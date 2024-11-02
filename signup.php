<?php include 'include/header.php'; ?>   

<h2 style="font-size:xx-large;text-align:center">Sign Up</h2>   

<!-- Display success or error message -->  
<?php if (isset($_GET['message'])): ?>  
    <script>  
        window.onload = function() {  
            // Check the message from the URL parameters  
            var messageType = "<?php echo htmlspecialchars($_GET['message']); ?>";  
            if (messageType === "success") {  
                alert("Successfully signed up! You can now proceed to login.");   
            } else if (messageType === "email_exists") {  
                alert("This email is already registered. Please use a different email.");  
            } else if (messageType === "username_exists") {  
                alert("This username is already taken. Please choose a different username.");  
            } else if (messageType === "error") {  
                alert("An error occurred during signing up. Please try again.");  
            }  
        };  
    </script>  
<?php endif; ?>  

<form id="signupForm" action="signup_action.php" method="POST" onsubmit="return validateForm()">  
    <label for="username">Username:</label>  
    <input type="text" id="username" name="username" required>  
    
    <label for="email">Email:</label>  
    <input type="email" id="email" name="email" required>  
    
    <label for="password">Password:</label>  
    <input type="password" id="password" name="password" required>  
    
    <input type="submit" value="Sign Up">  
</form>  

<script>  
function validateForm() {  
    const username = document.getElementById('username').value.trim();  
    const email = document.getElementById('email').value.trim();  
    const password = document.getElementById('password').value.trim();  

    // Username validation  
    if (username.length < 3) { // Minimum length for username  
        alert('Username must be at least 3 characters long.');  
        return false;  
    }  

    // Email validation  
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  
    if (!emailPattern.test(email)) {  
        alert('Please enter a valid email address.');  
        return false;  
    }  

    // Password validation  
    if (password.length < 8) { // Minimum length for password  
        alert('Password must be at least 8 characters long.');  
        return false;  
    }  

    return true; // Form is valid  
}  
</script>  

<?php include 'include/footer.php'; ?>