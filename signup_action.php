<?php  
include 'include/db.php'; // Include the database connection  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $username = trim($_POST['username']);  
    $email = trim($_POST['email']);  
    $password = $_POST['password']; // Password will be hashed later  

    // Server-side validation  
    $errors = [];  

    // Validate username  
    if (strlen($username) < 3) {  
        $errors[] = "Username must be at least 3 characters long.";  
    }  

    // Validate email  
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
        $errors[] = "Invalid email format.";  
    }  

    // Validate password  
    if (strlen($password) < 8) {  
        $errors[] = "Password must be at least 8 characters long.";  
    }  

    // If there are any validation errors, redirect back to signup page with errors  
    if (!empty($errors)) {  
        header("Location: signup.php?message=error");  
        exit();  
    }  

    // Check if the email already exists  
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");  
    $stmt->bind_param("s", $email);  
    $stmt->execute();  
    $stmt->store_result();  

    if ($stmt->num_rows > 0) {  
        // Email already exists, redirect to signup page with an error message  
        header("Location: signup.php?message=email_exists");  
        exit();  
    }  

    // Prepare the SQL statement to insert the new user  
    $passwordHash = password_hash($password, PASSWORD_BCRYPT); // Hash the password  
    $stmt->close(); // Close the previous statement  

    // Prepare the insert statement  
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");  
    $stmt->bind_param("sss", $username, $email, $passwordHash);  
    
    // Execute the statement and check for success  
    if ($stmt->execute()) {  
        // Redirect to the login page with a success message  
        header("Location: login.php?message=success");  
        exit();  
    } else {  
        // Redirect to the signup page with a generic error message  
        header("Location: signup.php?message=error");  
        exit();  
    }  

    // Close the statement  
    $stmt->close();  
}  

// Close the database connection  
$conn->close();  
?>