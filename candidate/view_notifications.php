<?php
session_start();
include('config.php');

// Check if the user is logged in and is a candidate
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

$candidate_id = $_SESSION["id"];

// Retrieve notifications for the candidate
$sql = "SELECT N.*, J.job_title 
        FROM Notifications N
        INNER JOIN Jobs J ON N.job_id = J.job_id
        WHERE N.candidate_id = ?
        ORDER BY N.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $candidate_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notifications = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Notifications</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Your Notifications</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Message</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($notification['job_title']); ?></td>
                        <td><?php echo htmlspecialchars($notification['message']); ?></td>
                        <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No notifications found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
