<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Check if candidate_id and message_text are provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_id']) && isset($_POST['message_text'])) {
    $sender_id = $_SESSION["id"];
    $receiver_id = $_POST['candidate_id'];
    $message_text = $_POST['message_text'];

    // Insert the message into the messages table
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $sender_id, $receiver_id, $message_text);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect back to the applications page
    header("location: view_applications.php?job_id=" . $_POST['job_id']);
    exit;
}

// Fetch candidate details
$candidate_id = $_GET['candidate_id'];
$sql = "SELECT first_name, last_name FROM Candidates WHERE candidate_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $candidate_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$candidate = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Message</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <h2>Send Message to <?php echo $candidate['first_name'] . ' ' . $candidate['last_name']; ?></h2>
    <form action="send_message.php" method="post">
        <input type="hidden" name="candidate_id" value="<?php echo $candidate_id; ?>">
        <input type="hidden" name="job_id" value="<?php echo $_GET['job_id']; ?>">
        <div class="form-group">
            <label for="message_text">Message:</label>
            <textarea name="message_text" class="form-control" placeholder="Write your message here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
