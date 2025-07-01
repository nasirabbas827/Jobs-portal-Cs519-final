<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

// Check if job_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Job ID.");
}

$job_id = intval($_GET['id']);

// Fetch job details
$sql = "SELECT * FROM Jobs WHERE job_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();

// Deadline calculation
$deadline = strtotime($job['application_deadline']);
$current_date = strtotime(date("Y-m-d"));
$days_left = ($deadline - $current_date) / (60 * 60 * 24);

if ($days_left < 0) {
    $deadline_class = "deadline-passed";
    $deadline_text = "Deadline Passed";
    $apply_disabled = true;
} elseif ($days_left <= 5) {
    $deadline_class = "deadline-soon";
    $deadline_text = "Deadline Approaching";
    $apply_disabled = false;
} else {
    $deadline_class = "deadline-safe";
    $deadline_text = "Deadline: " . htmlspecialchars($job['application_deadline']);
    $apply_disabled = false;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <style>
        .job-details-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .deadline-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .deadline-soon { background-color: #ffcc00; color: black; }
        .deadline-passed { background-color: #ff4444; color: white; }
        .deadline-safe { background-color: #28a745; color: white; }
        .apply-disabled {
            pointer-events: none;
            background-color: gray;
            border-color: gray;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <div class="job-details-container bg-white p-4">
            <h2><?php echo htmlspecialchars($job['job_title']); ?></h2>
            <p class="text-muted"><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
            <p><strong>Industry:</strong> <?php echo htmlspecialchars($job['industry']); ?></p>
            <p><strong>Job Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
            <p><strong>Salary Range:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
            <p><strong>Keywords:</strong> <?php echo htmlspecialchars($job['keywords']); ?></p>

            <h4>Job Description</h4>
            <p><?php echo nl2br(htmlspecialchars($job['job_description'])); ?></p>

            <h4>Job Requirements</h4>
            <p><?php echo nl2br(htmlspecialchars($job['job_requirements'])); ?></p>

            <h4>Application Deadline</h4>
            <span class="badge <?php echo $deadline_class; ?>"><?php echo $deadline_text; ?></span>

            <div class="mt-4">
                <a href="apply_job.php?id=<?php echo $job['job_id']; ?>" 
                   class="btn btn-primary <?php echo $apply_disabled ? 'apply-disabled' : ''; ?>" 
                   <?php echo $apply_disabled ? 'disabled' : ''; ?>>
                   Apply Now
                </a>
                <a href="candidate_dashboard.php" class="btn btn-secondary">Back to Jobs</a>
            </div>
        </div>
    </div>
</body>
</html>
