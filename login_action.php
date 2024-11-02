<?php  
session_start(); // Start a session  
include 'include/db.php'; // Include the database connection  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $email = trim($_POST['email']);  
    $password = trim($_POST['password']);  

    // Server-side validation  
    $errors = [];  

    // Validate email  
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
        $errors[] = "Invalid email format.";  
    }  

    // Validate password  
    if (strlen($password) < 8) {  
        $errors[] = "Password must be at least 8 characters long.";  
    }  

    if (empty($errors)) {  
        // Prepare the SQL statement to prevent SQL injection  
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");  
        $stmt->bind_param("s", $email);  

        // Execute the statement  
        if ($stmt->execute()) {  
            $result = $stmt->get_result();  
            if ($result->num_rows === 1) {  
                $user = $result->fetch_assoc();  
                // Verify password  
                if (password_verify($password, $user['password'])) {  
                    // Store user information in session  
                    $_SESSION['user_id'] = $user['id'];  
                    $_SESSION['username'] = $user['username'];  

                    // Redirect to home page after successful login  
                    header("Location: index.php");  
                    exit();  
                } else {  
                    $errors[] = "Invalid password. Please try again.";  
                }  
            } else {  
                $errors[] = "No user found with that email.";  
            }  
        } else {  
            $errors[] = "Error: " . $stmt->error; // Display error message  
        }  

        // Close the statement  
        $stmt->close();  
    } else {  
        // Display errors to the user if any  
        foreach ($errors as $error) {  
            echo "<p style='color:red;'>$error</p>";  
        }  
    }  
}  

// Close the database connection  
$conn->close();  
?>