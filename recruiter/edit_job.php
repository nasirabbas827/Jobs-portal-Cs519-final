<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

$job_id = $_GET['job_id'];
$recruiter_id = $_SESSION["id"];

// Fetch job details
$sql = "SELECT * FROM Jobs WHERE job_id = ? AND recruiter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $job_id, $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$job = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check if job exists
if (!$job) {
    echo '<div class="alert alert-danger" role="alert">Job not found.</div>';
    exit;
}

// Initialize form fields with existing data
$job_title = $job['job_title'];
$job_description = $job['job_description'];
$job_requirements = $job['job_requirements'];
$location = $job['location'];
$industry = $job['industry'];
$keywords = $job['keywords'];
$company_name = $job['company_name'];
$salary_range = $job['salary_range'];
$job_type = $job['job_type'];
$application_deadline = $job['application_deadline'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated values from the form
    $job_title = trim($_POST["job_title"]);
    $job_description = trim($_POST["job_description"]);
    $job_requirements = trim($_POST["job_requirements"]);
    $location = trim($_POST["location"]);
    $industry = trim($_POST["industry"]);
    $keywords = trim($_POST["keywords"]);
    $company_name = trim($_POST["company_name"]);
    $salary_range = trim($_POST["salary_range"]);
    $job_type = trim($_POST["job_type"]);
    $application_deadline = trim($_POST["application_deadline"]);

    // Update job details in the database (excluding the status field)
    $sql = "UPDATE Jobs 
            SET job_title = ?, job_description = ?, job_requirements = ?, location = ?, industry = ?, keywords = ?, 
                company_name = ?, salary_range = ?, job_type = ?, application_deadline = ?
            WHERE job_id = ? AND recruiter_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssss", $job_title, $job_description, $job_requirements, $location, $industry, 
                          $keywords, $company_name, $salary_range, $job_type, $application_deadline, $job_id, $recruiter_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="alert alert-success" role="alert">Job updated successfully.</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating job.</div>';
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Job</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
        <h2 class="text-center">Edit Job</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?job_id=' . $job_id; ?>" method="post">
            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($job_title); ?>">
            </div>
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($company_name); ?>">
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry" class="form-control" value="<?php echo htmlspecialchars($industry); ?>">
            </div>
            <div class="form-group">
                <label>Job Description</label>
                <textarea name="job_description" class="form-control"><?php echo htmlspecialchars($job_description); ?></textarea>
            </div>
            <div class="form-group">
                <label>Job Requirements</label>
                <textarea name="job_requirements" class="form-control"><?php echo htmlspecialchars($job_requirements); ?></textarea>
            </div>
            <div class="form-group">
                <label>Salary Range</label>
                <input type="text" name="salary_range" class="form-control" value="<?php echo htmlspecialchars($salary_range); ?>">
            </div>
            <div class="form-group">
                <label>Job Type</label>
                <select name="job_type" class="form-control">
                    <option value="Full-time" <?php echo ($job_type == 'Full-time') ? 'selected' : ''; ?>>Full-time</option>
                    <option value="Part-time" <?php echo ($job_type == 'Part-time') ? 'selected' : ''; ?>>Part-time</option>
                    <option value="Contract" <?php echo ($job_type == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                </select>
            </div>
            <div class="form-group">
                <label>Keywords</label>
                <input type="text" name="keywords" class="form-control" value="<?php echo htmlspecialchars($keywords); ?>">
            </div>
            <div class="form-group">
                <label>Application Deadline</label>
                <input type="date" name="application_deadline" class="form-control" value="<?php echo htmlspecialchars($application_deadline); ?>"  min="<?= date('Y-m-d'); ?>">
            </div>
            <div class="form-group text-center">
                <input type="submit" value="Update Job" class="mt-2 btn btn-primary">
            </div>
        </form>
    </div>
    </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
