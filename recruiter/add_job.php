<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Fetch recruiter's job posting status
$recruiter_id = $_SESSION["id"];
$sql = "SELECT job_posting_status FROM Recruiters WHERE recruiter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recruiter = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check if the job posting status is deactivated
if ($recruiter['job_posting_status'] == 'deactivated') {
    $status_message = "Your job posting privileges are currently deactivated. Please contact admin for more information.";
} else {
    // Define variables and initialize with empty values
    $job_title = $job_description = $job_requirements = $location = $industry = $keywords = $company_name = $salary_range = $job_type = $application_deadline = "";
    $job_title_err = $job_description_err = $job_requirements_err = $location_err = $industry_err = $keywords_err = $company_name_err = $salary_range_err = $job_type_err = $application_deadline_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate form inputs
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

        // Ensure all fields are filled
        if (empty($job_title_err) && empty($job_description_err) && empty($job_requirements_err) && empty($location_err) && empty($industry_err) && empty($keywords_err) && empty($company_name_err) && empty($salary_range_err) && empty($job_type_err) && empty($application_deadline_err)) {
            // Insert job posting into database
            $sql = "INSERT INTO Jobs (recruiter_id, job_title, job_description, job_requirements, location, industry, keywords, company_name, salary_range, job_type, application_deadline, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issssssssss", $recruiter_id, $job_title, $job_description, $job_requirements, $location, $industry, $keywords, $company_name, $salary_range, $job_type, $application_deadline);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            echo '<div class="alert alert-success" role="alert">Job posted successfully.</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Job</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">

        <?php if (isset($status_message)): ?>
            <div class="alert alert-warning" role="alert">
                <?php echo htmlspecialchars($status_message); ?>
            </div>
        <?php else: ?>
            <h2 class="text-center">Post a Job</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                    <input type="date" name="application_deadline" class="form-control" value="<?php echo htmlspecialchars($application_deadline); ?>" min="<?= date('Y-m-d'); ?>">
                </div>
                <div class="form-group text-center">
                    <input type="submit" value="Post Job" class="btn btn-primary">
                    <a class="btn btn-outline-dark" href="view_jobs.php">View Jobs</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
    </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
