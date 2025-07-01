<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

// Get candidate ID from session
$candidate_id = $_SESSION["id"];

$user_id = $_SESSION["id"];
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $_SESSION["name"] = $row["name"];
}


// Fetch recent notifications
$sql_notifications = "SELECT * FROM notifications WHERE candidate_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->bind_param("i", $candidate_id);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->get_result();

// Fetch all active jobs
$sql = "SELECT J.*, R.company_name FROM Jobs J INNER JOIN Recruiters R ON J.recruiter_id = R.recruiter_id WHERE J.status = 'active'";

// Initialize search flag
$searchActive = false;
$conditions = [];

// Handle search filters
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $conditions[] = "(J.job_title LIKE '%$search%' OR J.location LIKE '%$search%' OR J.industry LIKE '%$search%' OR J.keywords LIKE '%$search%')";
    $searchActive = true;
}
if (!empty($_GET['industry'])) {
    $industry = mysqli_real_escape_string($conn, $_GET['industry']);
    $conditions[] = "J.industry = '$industry'";
    $searchActive = true;
}
if (!empty($_GET['location'])) {
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $conditions[] = "J.location LIKE '%$location%'";
    $searchActive = true;
}
if (!empty($_GET['job_type'])) {
    $job_type = mysqli_real_escape_string($conn, $_GET['job_type']);
    $conditions[] = "J.job_type = '$job_type'";
    $searchActive = true;
}

// Apply search conditions
if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

// Execute query
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Active Jobs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">

    <style>
        .job-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .job-card .card-body {
            flex-grow: 1;
        }
        .deadline-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .deadline-soon { background-color: #ffcc00; color: black; } /* Yellow */
        .deadline-passed { background-color: #ff4444; color: white; } /* Red */
        .deadline-safe { background-color: #28a745; color: white; } /* Green */
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <!-- Dashboard Jumbotron -->
<div class="jumbotron jumbotron-fluid text-white" style="background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1400&q=80') no-repeat center center; background-size: cover;">
    <div class="container py-5" style="background-color: rgba(0,0,0,0.6); border-radius: 8px;">

        <h1 class="display-4">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>
        <p class="lead">Explore active job opportunities tailored to your profile. Use the search tools below to filter jobs by industry, location, or type.</p>
        <hr class="my-4 bg-light">
        <p>Apply early to increase your chances and keep an eye on approaching deadlines.</p>
    </div>
</div>


    <div class="container mt-5">
        <!-- Recent Notifications -->
        <?php if ($result_notifications->num_rows > 0): ?>
            <div class="alert alert-info">
                <h4>Recent Notifications</h4>
                <ul class="mb-0">
                    <?php while ($notification = $result_notifications->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($notification['message']); ?> <small>(<?php echo htmlspecialchars($notification['created_at']); ?>)</small></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Active Jobs</h2>
        
        <!-- Search Filters -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search jobs...">
                </div>
                <div class="col-md-3">
                    <select name="industry" class="form-control">
                        <option value="">Select Industry</option>
                        <option value="IT">IT</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="location" class="form-control" placeholder="Location">
                </div>
                <div class="col-md-3">
                    <select name="job_type" class="form-control">
                        <option value="">Select Job Type</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Contract">Contract</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Search</button>
        </form>

        <?php if (mysqli_num_rows($result) > 0) : ?>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result)) : 
                    $deadline = strtotime($row['application_deadline']);
                    $current_date = strtotime(date("Y-m-d"));
                    $days_left = ($deadline - $current_date) / (60 * 60 * 24);

                    if ($days_left < 0) {
                        $deadline_class = "deadline-passed";
                        $deadline_text = "Deadline Passed";
                    } elseif ($days_left <= 5) {
                        $deadline_class = "deadline-soon";
                        $deadline_text = "Deadline Approaching";
                    } else {
                        $deadline_class = "deadline-safe";
                        $deadline_text = "Deadline: " . htmlspecialchars($row['application_deadline']);
                    }
                ?>
                    <div class="col-md-4 mb-4">
                        <div class="card job-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['job_title']); ?></h5>
                                <p class="card-text"><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="card-text"><strong>Industry:</strong> <?php echo htmlspecialchars($row['industry']); ?></p>
                                <p class="card-text"><strong>Job Type:</strong> <?php echo htmlspecialchars($row['job_type']); ?></p>
                                <p class="card-text"><strong>Salary Range:</strong> <?php echo htmlspecialchars($row['salary_range']); ?></p>
                                <span class="badge <?php echo $deadline_class; ?>"><?php echo $deadline_text; ?></span>
                            </div>
                            <div class="card-footer">
    <a href="apply_job.php?id=<?php echo $row['job_id']; ?>" 
       class="btn btn-primary <?php echo ($days_left < 0) ? 'disabled' : ''; ?>" 
       <?php echo ($days_left < 0) ? 'disabled' : ''; ?>>
       Apply
    </a>
    <a href="job_details.php?id=<?php echo $row['job_id']; ?>" class="btn btn-info">View Details</a>
</div>

                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info">No jobs found matching your search criteria.</div>
        <?php endif; ?>
    </div>
</body>
</html>
