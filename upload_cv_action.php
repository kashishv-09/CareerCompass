<?php  
session_start();  
include 'include/db.php'; // Include the database connection  

// Check if the user is logged in  
if (!isset($_SESSION['user_id'])) {  
    header("Location: login.php"); // Redirect to login if not logged in  
    exit();  
}  

// Initialize upload message variable  
$upload_message = "";   

// Check if the form was submitted  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {  
        $cv_file = $_FILES['cv'];  

        // Check if the file type is allowed  
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];  
        if (in_array($cv_file['type'], $allowed_types)) {  
            // Read file contents into a variable  
            $file_data = file_get_contents($cv_file['tmp_name']);  

            // Prepare the SQL statement to update the user's CV in the database  
            $stmt = $conn->prepare("UPDATE users SET cv = ? WHERE id = ?");  
            $stmt->bind_param("bi", $file_data, $_SESSION['user_id']); // 'bi' = blob and integer  

            // Execute the statement and set the session message  
            if ($stmt->execute()) {  
                $_SESSION['upload_message'] = "CV uploaded successfully!"; // Store message in session  
            } else {  
                $_SESSION['upload_message'] = "Error updating CV record: " . $stmt->error; // Store error message  
            }  

            // Close the statement  
            $stmt->close();  
        } else {  
            $_SESSION['upload_message'] = "Invalid file type. Only PDF, DOC, and DOCX files are allowed."; // Store invalid type message  
        }  
    } else {  
        $_SESSION['upload_message'] = "Error uploading file."; // Store upload error message  
    }  
}  

// Close the database connection  
$conn->close();  

// Redirect back to the upload page  
header("Location: upload_cv.php");   
exit();  
?>