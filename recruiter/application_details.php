<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Check if application_id is provided in the URL
if (!isset($_GET['application_id'])) {
    header("location: recruiter_dashboard.php");
    exit;
}

$application_id = $_GET['application_id'];

// Retrieve application details
$sql = "SELECT A.application_id, A.candidate_id, A.experience, A.salary_expectation, A.cv_url, A.status, A.applied_at,
               C.first_name, C.last_name, C.phone_number, C.address, C.education, C.skills, C.resume_url
        FROM Applications A
        INNER JOIN Candidates C ON A.candidate_id = C.candidate_id
        WHERE A.application_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$application = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check if application exists
if (!$application) {
    header("location: recruiter_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Application Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>Application Details for <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h2>

    <div class="card">
        <div class="card-body">
            <h4>Candidate Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Candidate Name</th>
                    <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td><?php echo htmlspecialchars($application['phone_number']); ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo htmlspecialchars($application['address']); ?></td>
                </tr>
                <tr>
                    <th>Education</th>
                    <td><?php echo htmlspecialchars($application['education']); ?></td>
                </tr>
                <tr>
                    <th>Skills</th>
                    <td><?php echo htmlspecialchars($application['skills']); ?></td>
                </tr>
                <tr>
                    <th>Resume</th>
                    <td><a href="../candidate/<?php echo htmlspecialchars($application['resume_url']); ?>" target="_blank">View Resume</a></td>
                </tr>
            </table>

            <h4>Application Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Experience</th>
                    <td><?php echo htmlspecialchars($application['experience']); ?> years</td>
                </tr>
                <tr>
                    <th>Salary Expectation</th>
                    <td><?php echo htmlspecialchars($application['salary_expectation']); ?></td>
                </tr>
                <tr>
                    <th>CV</th>
                    <td><a href="../candidate/<?php echo htmlspecialchars($application['cv_url']); ?>" target="_blank">View CV</a></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                </tr>
                <tr>
                    <th>Applied At</th>
                    <td><?php echo htmlspecialchars($application['applied_at']); ?></td>
                </tr>
            </table>

            <a href="recruiter_dashboard.php" class="btn btn-secondary">Back to Applications</a>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
