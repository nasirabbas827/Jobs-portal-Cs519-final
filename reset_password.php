<?php
// Include the database configuration file
include('config.php');

// Define variables and initialize with empty values
$email = "";
$email_err = "";

// Check if form is submitted for password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);

        // Check if the email exists in the users table
        $sql = "SELECT id, email FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Email exists in the users table, proceed with password reset
            // Redirect to a page where you handle the password reset process
            header("location: handle_reset_password.php?email=$email");
            exit;
        } else {
            // Email does not exist in the users table
            $email_err = "No account found with that email.";
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
<?php
include('navbar.php');
?>
    <div class="container">
        <h2>Reset Password</h2>
        <p>Please enter the email associated with your account and we'll send you instructions on how to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Reset Password">
            </div>
        </form>
    </div>
</body>

</html>
