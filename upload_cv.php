<?php  
session_start(); // Start the session at the very beginning  
include 'include/header.php';   
?>  

<div class="main-container">  
    <h2 class="upload-title">Upload CV</h2>  
    <div class="home-content">  
        <form action="upload_cv_action.php" method="POST" enctype="multipart/form-data">  
            <label for="cv">Upload your CV:</label>  
            <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>  
            <input type="submit" value="Upload CV">  
        </form>  

        <?php   
        // Display upload message if it exists  
        if (isset($_SESSION['upload_message'])) {  
            echo "<div class='upload-message'>" . htmlspecialchars($_SESSION['upload_message']) . "</div>";  
            unset($_SESSION['upload_message']); // Clear the message after displaying  
        }  
        ?>  
    </div>  
</div>  

<?php include 'include/footer.php'; ?>