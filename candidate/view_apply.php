<?php
session_start();
include('config.php');

// Check if the user is logged in and is a candidate
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

$candidate_id = $_SESSION["id"];

// Retrieve applied jobs for the candidate
$sql = "SELECT A.*, J.job_title, J.location, J.industry, J.recruiter_id
        FROM Applications A
        INNER JOIN Jobs J ON A.job_id = J.job_id
        WHERE A.candidate_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $candidate_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$applied_jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Applied Jobs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Your Applied Jobs</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Location</th>
                <th>Industry</th>
                <th>Status</th>
                <th>Applied At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applied_jobs as $job): ?>
                <tr>
                    <td><?php echo $job['job_title']; ?></td>
                    <td><?php echo $job['location']; ?></td>
                    <td><?php echo $job['industry']; ?></td>
                    <td><?php echo $job['status']; ?></td>
                    <td><?php echo $job['applied_at']; ?></td>
                    <td>
                        <a href="view_received_messages.php?recruiter_id=<?php echo $job['recruiter_id']; ?>" class="btn btn-primary">View Messages</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
