<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Fetch messages between recruiter and candidate
$candidate_id = $_GET['candidate_id'];
$recruiter_id = $_SESSION["id"];

$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_datetime";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiii", $recruiter_id, $candidate_id, $candidate_id, $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Fetch candidate details
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
    <title>Message Thread</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5 mb-5">
    <h2>Message Thread with <?php echo $candidate['first_name'] . ' ' . $candidate['last_name']; ?></h2>
    <div class="message-thread">
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['sender_id'] == $recruiter_id ? 'recruiter-message' : 'candidate-message'; ?>">
                <p><?php echo $message['message_text']; ?></p>
                <small><?php echo $message['sent_datetime']; ?></small>
                <?php if ($message['reply_text']): ?>
                    <div class="reply">
                        <p><?php echo $message['reply_text']; ?></p>
                        <small><?php echo $message['reply_datetime']; ?></small>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
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
<style>
.message-thread {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 20px;
}
.message {
    border-bottom: 1px solid #ccc;
    padding: 5px;
}
.recruiter-message {
    text-align: right;
    background-color: #e7f3ff;
}
.candidate-message {
    text-align: left;
    background-color: #f8f9fa;
}
.reply {
    margin-top: 10px;
    padding-left: 20px;
    border-left: 2px solid #ccc;
}
</style>
</body>
</html>
