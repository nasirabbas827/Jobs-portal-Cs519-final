<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
        $job_id = trim($_GET['id']);

        // Fetch job details
        $sql_job = "SELECT * FROM Jobs WHERE job_id = ?";
        $stmt_job = mysqli_prepare($conn, $sql_job);
        mysqli_stmt_bind_param($stmt_job, "i", $job_id);
        mysqli_stmt_execute($stmt_job);
        $result_job = mysqli_stmt_get_result($stmt_job);
        $job = mysqli_fetch_assoc($result_job);
    } else {
        header("location: candidate_dashboard.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['apply'])) {
        // Process application form data
        $candidate_id = $_SESSION["id"];
        $job_id = $_POST['job_id'];
        $experience = $_POST['experience'];
        $salary_expectation = $_POST['salary_expectation'];
        
        // Handle CV upload
        $cv_url = '';
        if (isset($_FILES['cv']) && !empty($_FILES['cv']['name'])) {
            // Check for file upload errors
            if ($_FILES['cv']['error'] != UPLOAD_ERR_OK) {
                echo '<div class="alert alert-danger" role="alert">Error uploading file.</div>';
                exit;
            }
            
            // Define the target directory and file path
            $target_dir = 'uploads/';
            $sanitized_filename = preg_replace('/[^A-Za-z0-9.\-_]/', '_', basename($_FILES['cv']['name']));
            $target_file = $target_dir . $sanitized_filename;
            
            // Check if the file is a valid PDF
            $file_type = pathinfo($target_file, PATHINFO_EXTENSION);
            if ($file_type != 'pdf') {
                echo '<div class="alert alert-danger" role="alert">Only PDF files are allowed.</div>';
                exit;
            }
            
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $target_file)) {
                $cv_url = $target_file;  // Store the file path in the database
            } else {
                echo '<div class="alert alert-danger" role="alert">There was an error uploading your file.</div>';
                exit;
            }
        }

        // Get current date and time for applying
        $applied_at = date('Y-m-d H:i:s');
        $updated_at = $applied_at;

        // Insert application into database
        $sql_apply = "INSERT INTO Applications (candidate_id, job_id, experience, salary_expectation, cv_url, status, applied_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, 'applied', ?, ?)";
        $stmt_apply = mysqli_prepare($conn, $sql_apply);
        mysqli_stmt_bind_param($stmt_apply, "iiissss", $candidate_id, $job_id, $experience, $salary_expectation, $cv_url, $applied_at, $updated_at);
        mysqli_stmt_execute($stmt_apply);
        mysqli_stmt_close($stmt_apply);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">Application submitted successfully.</div>';
        header("location: view_apply.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield; /* Firefox */
}
    </style>
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5 mb-5">
        <h2>Apply for Job</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $job['job_title']; ?></h5>
                <p class="card-text"><?php echo $job['job_description']; ?></p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                    <div class="form-group">
                        <label for="experience">Years of Experience:</label>
                        <input type="number" class="form-control" id="experience" name="experience" required>
                    </div>
                    <div class="form-group">
                        <label for="salary_expectation">Salary Expectation:</label>
                        <input type="number" class="form-control" id="salary_expectation" name="salary_expectation" required>
                    </div>
                    <div class="form-group">
                        <label for="cv">Upload CV (PDF only):</label>
                        <input type="file" class="form-control-file" id="cv" name="cv" accept="application/pdf">
                    </div>
                    <button type="submit" name="apply" class="btn btn-primary">Apply Now</button>
                    <a href="candidate_dashboard.php" class="btn btn-secondary">Back to Jobs</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
