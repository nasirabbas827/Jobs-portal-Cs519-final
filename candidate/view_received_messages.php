<?php
session_start();
include('config.php');

// Check if the user is logged in and is a candidate
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

$candidate_id = $_SESSION["id"];

// Fetch messages between candidate and recruiter
$recruiter_id = isset($_GET['recruiter_id']) ? $_GET['recruiter_id'] : 0;

$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY sent_datetime";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiii", $recruiter_id, $candidate_id, $candidate_id, $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Fetch recruiter details
$sql = "SELECT company_name FROM Recruiters WHERE recruiter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $recruiter_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recruiter = mysqli_fetch_assoc($result);
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
    <h2>Message Thread with <?php echo $recruiter['company_name']; ?></h2>
    <div class="message-thread">
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['sender_id'] == $candidate_id ? 'candidate-message' : 'recruiter-message'; ?>">
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
    <form action="reply_message.php" method="post">
        <input type="hidden" name="recruiter_id" value="<?php echo $recruiter_id; ?>">
        <div class="form-group">
            <label for="reply_text">Reply:</label>
            <textarea name="reply_text" class="form-control" placeholder="Write your reply here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Reply</button>
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
.candidate-message {
    text-align: right;
    background-color: #e7f3ff;
}
.recruiter-message {
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
