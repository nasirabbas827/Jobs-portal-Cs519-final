<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Check if job_id is provided in the URL
if (!isset($_GET['job_id'])) {
    header("location: recruiter_dashboard.php");
    exit;
}

$job_id = $_GET['job_id'];

// Retrieve job applications with additional details
$sql = "SELECT A.application_id, A.candidate_id, A.experience, A.salary_expectation, A.cv_url, A.status, A.applied_at, 
               C.first_name, C.last_name, C.phone_number, C.education, U.email
        FROM Applications A
        INNER JOIN Candidates C ON A.candidate_id = C.candidate_id
        INNER JOIN Users U ON C.candidate_id = U.id
        WHERE A.job_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $job_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$applications = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Define the CSV file headers
$headers = array('Candidate Name', 'Email', 'Phone Number', 'Education', 'Experience', 'Salary Expectation', 'CV URL', 'Status', 'Applied At');

// Open output stream
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="applications_report.csv"');

// Output the headers
$output = fopen('php://output', 'w');
fputcsv($output, $headers);

// Output the data
foreach ($applications as $application) {
    fputcsv($output, array(
        $application['first_name'] . ' ' . $application['last_name'],
        $application['email'],
        $application['phone_number'],
        $application['education'],
        $application['experience'],
        $application['salary_expectation'],
        $application['cv_url'],
        $application['status'],
        $application['applied_at']
    ));
}

fclose($output);
exit;
?>
