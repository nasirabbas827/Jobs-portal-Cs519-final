<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Get recruiter ID
$recruiter_id = $_SESSION["id"];

$user_id = $_SESSION["id"];
$sql = "SELECT name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $_SESSION["name"] = $row["name"];
}



// Retrieve recruiter's jobs
$sql = "SELECT * FROM jobs WHERE recruiter_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Count total jobs
$total_jobs = count($jobs);

// Count total applications for recruiter's jobs
$sql = "SELECT COUNT(*) as total_applications FROM applications WHERE job_id IN (SELECT job_id FROM jobs WHERE recruiter_id = ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total_applications = mysqli_fetch_assoc($result)['total_applications'];
mysqli_stmt_close($stmt);

// Fetch latest messages for recruiter
$sql = "SELECT * FROM messages WHERE sender_id = ? ORDER BY sent_datetime DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$latest_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);



// Fetch application status data for chart
$sql = "SELECT status, COUNT(*) as count FROM applications WHERE job_id IN (SELECT job_id FROM jobs WHERE recruiter_id = ?) GROUP BY status";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$application_status_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recruiter Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">

    <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h2>
    <p class="text-center">This is your recruiter dashboard.</p>

    <!-- Dashboard Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center text-white bg-primary">
                <div class="card-body">
                    <i class="fas fa-briefcase card-icon"></i>
                    <h5 class="card-title">Total Posted Jobs</h5>
                    <p class="card-text display-4"><?php echo $total_jobs; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center text-white bg-success">
                <div class="card-body">
                    <i class="fas fa-file-alt card-icon"></i>
                    <h5 class="card-title">Total Applications</h5>
                    <p class="card-text display-4"><?php echo $total_applications; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Status Chart -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Application Status Distribution</h5>
                    <div class="chart-container">
                        <canvas id="applicationStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Messages -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Latest Messages</h5>
                    <?php if (!empty($latest_messages)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($latest_messages as $message): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($message['message_text']); ?>
                                    <small class="text-muted d-block">
                                        Sent on: <?php echo $message['sent_datetime']; ?>
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="card-text">No new messages.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- List of Jobs -->
    <h3 class="mb-3">Your Jobs</h3>
    <div class="row">
        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($job['job_title']); ?></h5>
                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars(substr($job['job_description'], 0, 100)) . '...'; ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p class="card-text"><strong>Status:</strong> 
                                <span class="badge badge-<?php echo $job['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars($job['status']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="view_applications.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-primary btn-sm">View Applications</a>
                            <a href="edit_job.php?job_id=<?php echo $job['job_id']; ?>" class="btn btn-secondary btn-sm">Edit Job</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    No jobs posted yet. <a href="add_job.php" class="alert-link">Post a Job</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Application Status Chart
var ctx = document.getElementById('applicationStatusChart').getContext('2d');
var applicationStatusChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($application_status_data, 'status')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($application_status_data, 'count')); ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
            display: true,
            text: 'Application Status Distribution'
        }
    }
});
</script>
</body>
</html>