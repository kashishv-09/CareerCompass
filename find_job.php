<?php   
include 'include/header.php';   
include 'include/db.php';  

session_start(); // Start a session to access user data  
$user_id = $_SESSION['user_id']; // Assume user_id is stored in session when user logs in  

// Variable to hold success message  
$message = '';  
$already_applied = false; // Flag to check if already applied  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {  
    $job_id = $_POST['job_id'];  

    // Fetch job details  
    $job_result = $conn->query("SELECT company FROM jobs WHERE id = '$job_id'");  
    $job_data = $job_result->fetch_assoc();  
    $company_name = $job_data['company'];  

    // Check if the user already applied for this job  
    $check_application_stmt = $conn->prepare("SELECT applied_in FROM users WHERE id = ?");  
    $check_application_stmt->bind_param("i", $user_id);  
    $check_application_stmt->execute();  
    $check_application_stmt->store_result();  
    $check_application_stmt->bind_result($applied_in);  
    $check_application_stmt->fetch();  
    
    if ($check_application_stmt->num_rows > 0 && !empty($applied_in)) {  
        // Decode the existing applied_in JSON data to array  
        $applied_companies = json_decode($applied_in, true);  
        
        // Check if the company is already in applied_companies  
        if (in_array($company_name, $applied_companies)) {  
            $already_applied = true; // Set flag if already applied  
        } else {  
            // Apply to the job  
            $applied_companies[] = $company_name;  
            $encoded_applied_in = json_encode($applied_companies);  
            
            // Update the applied_in column for the user  
            $update_stmt = $conn->prepare("UPDATE users SET applied_in = ? WHERE id = ?");  
            $update_stmt->bind_param("si", $encoded_applied_in, $user_id);  
            
            if ($update_stmt->execute()) {  
                $message = "You have successfully applied to $company_name!";  
            } else {  
                $message = "Error applying to the job. Please try again.";  
            }  
            
            // Close update statement  
            $update_stmt->close();  
        }  
    } else {  
        // User hasn't applied yet, so we create the array for the first time  
        $applied_companies = [$company_name];  
        $encoded_applied_in = json_encode($applied_companies);  
        
        // Update the applied_in column for the user  
        $update_stmt = $conn->prepare("UPDATE users SET applied_in = ? WHERE id = ?");  
        $update_stmt->bind_param("si", $encoded_applied_in, $user_id);  
        
        if ($update_stmt->execute()) {  
            $message = "You have successfully applied to $company_name!";  
        } else {  
            $message = "Error applying to the job. Please try again.";  
        }  
        
        // Close update statement  
        $update_stmt->close();  
    }  

    // Close check application statement   
    $check_application_stmt->close();  
}  

// Pagination variables  
$limit = 6; // Number of entries to show on each page  
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page  
$offset = ($page - 1) * $limit; // Calculate the offset for the SQL query   

// Fetch jobs from the database with pagination  
$sql = "SELECT id, title, description, company, location FROM jobs LIMIT $limit OFFSET $offset";  
$result = $conn->query($sql);  

// Count total job postings for pagination  
$total_jobs_result = $conn->query("SELECT COUNT(*) as total FROM jobs");  
$total_jobs = $total_jobs_result->fetch_assoc()['total'];  
$total_pages = ceil($total_jobs / $limit); // Total number of pages  

?>  

<h2 style="font-size:xx-large;text-align:center">Find A Job Here</h2>   
<div class="job-postings">  
    <?php   
    if ($result->num_rows > 0) {  
        // Output data of each row  
        while ($row = $result->fetch_assoc()) {  
            ?>  
            <div class="job-card">  
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>  
                <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?></p>  
                <p><?php echo htmlspecialchars($row['description']); ?></p>  
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>  
                
                <form method="POST">  
                    <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">  
                    <button type="submit" name="apply" class="apply-button">Apply Now</button>  
                </form>  
            </div>  
            <?php  
        }  
    } else {  
        echo "<p>No job postings available at this time.</p>";  
    }  
    ?>  
</div>  

<script>  
    // Show alert if the user has already applied  
    <?php if ($already_applied): ?>  
        alert("You have already applied for this role at <?php echo htmlspecialchars($company_name); ?>.");  
    <?php elseif ($message): ?>  
        alert("<?php echo htmlspecialchars($message); ?>");  
    <?php endif; ?>  
</script>  

<!-- Pagination control -->  
<div class="pagination-container">  
    <div class="pagination">  
        <?php if($page > 1): ?>  
            <a href="?page=<?php echo $page - 1; ?>" class="pagination-button">Previous</a>  
        <?php endif; ?>  

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>  
            <a href="?page=<?php echo $i; ?>" class="pagination-button <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>  
        <?php endfor; ?>  

        <?php if($page < $total_pages): ?>  
            <a href="?page=<?php echo $page + 1; ?>" class="pagination-button">Next</a>  
        <?php endif; ?>  
    </div>  
</div>
<style>  
/* Add styles here */  
.job-postings {  
    display: flex;  
    flex-wrap: wrap;  
    justify-content: center;  
    gap: 20px;  
    margin-top: 20px;  
}  

.job-card {  
    border: 1px solid #444;  
    border-radius: 8px;  
    padding: 15px;  
    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);  
    width: 300px;  
    background-color: #35424a;  
    color: white;  
}  

.success-message {  
    margin-top: 20px;  
    padding: 10px;  
    background-color: #d4edda;  
    border: 1px solid #c3e6cb;  
    color: #155724;  
    border-radius: 5px;  
    font-size: 16px;  
    text-align: center;  
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  
}  

.pagination-container {  
    display: flex;  
    justify-content: center;  
    margin-top: 20px;  
}  

.pagination {  
    display: inline-flex;  
    margin: 0;  
    padding: 0;  
}  

.pagination-button {  
    margin: 0 5px;  
    padding: 10px 15px;  
    background-color: #35424a;  
    color: white;  
    text-decoration: none;  
    border-radius: 5px;  
    transition: background-color 0.3s;  
    border: 1px solid transparent; /* Make border transparent for a cleaner look */  
}  


.active {  
    background-color: #35424a;  
}
</style>  

<br><br><br>  
<?php include 'include/footer.php'; ?>