<?php include 'include/header.php'; ?>  

<h2 style="font-size:xx-large;text-align:center">Login</h2>  
<form id="loginForm" action="login_action.php" method="POST" onsubmit="return validateForm()">  
    <label for="email">Email:</label>  
    <input type="email" id="email" name="email" required>  
    
    <label for="password">Password:</label>  
    <input type="password" id="password" name="password" required>  
    
    <input type="submit" value="Login">  
</form>  

<script>  
function validateForm() {  
    const email = document.getElementById('email').value;  
    const password = document.getElementById('password').value;  
    
    // Email validation  
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;  
    if (!emailPattern.test(email)) {  
        alert('Please enter a valid email address.');  
        return false;  
    }  
    
    // Password validation  
    if (password.length < 8) {  
        alert('Password must be at least 8 characters long.');  
        return false;  
    }  

    return true; // Form is valid  
}  
</script>  

<?php include 'include/footer.php'; ?>