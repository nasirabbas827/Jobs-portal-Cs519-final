<?php
session_start();
include('config.php');

// Check if the user is logged in and is a candidate
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recruiter_id']) && isset($_POST['reply_text'])) {
    $candidate_id = $_SESSION["id"];
    $recruiter_id = $_POST['recruiter_id'];
    $reply_text = $_POST['reply_text'];

    // Update the message with the candidate's reply
    $sql = "UPDATE messages SET reply_text = ?, reply_datetime = NOW() WHERE sender_id = ? AND receiver_id = ? AND reply_text IS NULL";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $reply_text, $recruiter_id, $candidate_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect back to the message thread page
    header("location: view_received_messages.php?recruiter_id=" . $recruiter_id);
    exit;
}
?>
