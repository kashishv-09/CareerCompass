<?php   
include 'include/header.php';   
include 'include/db.php'; // Include the database connection  

$message = ''; // Initialize a message variable  

// Check if the form has been submitted  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Get the form data  
    $title = $_POST["title"];  
    $description = $_POST["description"];  
    $company = $_POST["company"];  
    $location = $_POST["location"];  
    
    // Prepare and bind  
    $stmt = $conn->prepare("INSERT INTO jobs (title, description, company, location) VALUES (?, ?, ?, ?)");  
    $stmt->bind_param("ssss", $title, $description, $company, $location);  

    // Attempt to execute the statement  
    if ($stmt->execute()) {  
        $message = "Job vacancy updated successfully!";  
    } else {  
        $message = "Error: " . $stmt->error; // Error handling  
    }  

    // Close the statement  
    $stmt->close();  
}  

// Close the database connection  
$conn->close();  
?>   

<!-- Display success message -->  
<?php if (!empty($message)): ?>  
    <div style="text-align:center; color: green;">  
        <strong><?php echo $message; ?></strong>  
    </div>  
<?php endif; ?>  
<h2 style="font-size:xx-large;text-align:center">Create A Job Vacancy</h2> 
<form action="create_job.php" method="POST">  
    <label for="title">Job Title:</label>  
    <input type="text" id="title" name="title" required>  

    <label for="description">Job Description:</label>  
    <textarea id="description" name="description" required></textarea>  

    <label for="company">Company:</label>  
    <input type="text" id="company" name="company" required>  

    <label for="location">Location:</label>  
    <input type="text" id="location" name="location" required>  

    <input type="submit" value="Post Job">  
</form>  

<?php include 'include/footer.php'; ?>