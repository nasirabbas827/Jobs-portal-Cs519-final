<?php
session_start();
include('config.php');

// Check if the user is logged in and is a recruiter
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'recruiter') {
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$company_name = $company_address = $company_phone = $company_email = "";
$company_name_err = $company_address_err = $company_phone_err = $company_email_err = "";

// Fetch current recruiter profile data
$id = $_SESSION["id"];
$sql = "SELECT * FROM Recruiters WHERE recruiter_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recruiter = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);


// Fetch user data from users table
$user_sql = "SELECT * FROM users WHERE id = ?";
$user_stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($user_stmt, "i", $id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user_data = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($user_stmt);


// Initialize form fields with fetched data or empty strings
if ($recruiter) {
    $company_name = $recruiter['company_name'];
    $company_address = $recruiter['company_address'];
    $company_phone = $recruiter['company_phone'];
    $company_email = $recruiter['company_email'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and update profile data
    $company_name = trim($_POST["company_name"]);
    $company_address = trim($_POST["company_address"]);
    $company_phone = trim($_POST["company_phone"]);
    $company_email = trim($_POST["company_email"]);

    if (empty($company_name_err) && empty($company_address_err) && empty($company_phone_err) && empty($company_email_err)) {
        if ($recruiter) {
            // Update existing profile
            $sql = "UPDATE Recruiters SET company_name = ?, company_address = ?, company_phone = ?, company_email = ? WHERE recruiter_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi", $company_name, $company_address, $company_phone, $company_email, $id);
        } else {
            // Insert new profile
            $sql = "INSERT INTO Recruiters (recruiter_id, company_name, company_address, company_phone, company_email) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $id, $company_name, $company_address, $company_phone, $company_email);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">Profile ' . ($recruiter ? 'updated' : 'created') . ' successfully.</div>';
        // Refresh the page to fetch the latest profile data
        header("Refresh:0");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Recruiter Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- User Data Column -->
        <?php if ($user_data): ?>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Your Existing Data</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Full Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></li>
                        <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></li>
                        <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></li>
                        <li class="list-group-item"><strong>CNIC:</strong> <?php echo htmlspecialchars($user_data['cnic']); ?></li>
                        <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($user_data['address']); ?></li>
                        <li class="list-group-item"><strong>Town:</strong> <?php echo htmlspecialchars($user_data['town']); ?></li>
                        <li class="list-group-item"><strong>Region:</strong> <?php echo htmlspecialchars($user_data['region']); ?></li>
                        <li class="list-group-item"><strong>Postcode:</strong> <?php echo htmlspecialchars($user_data['postcode']); ?></li>
                        <li class="list-group-item"><strong>Country:</strong> <?php echo htmlspecialchars($user_data['country']); ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Update Form Column -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="text-center mb-4">Update Profile</h4>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($company_name); ?>">
                        </div>
                        <div class="form-group">
                            <label>Company Address</label>
                            <textarea name="company_address" class="form-control"><?php echo htmlspecialchars($company_address); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Company Phone</label>
                            <input type="text" name="company_phone" class="form-control" value="<?php echo htmlspecialchars($company_phone); ?>">
                        </div>
                        <div class="form-group">
                            <label>Company Email</label>
                            <input type="email" name="company_email" class="form-control" value="<?php echo htmlspecialchars($company_email); ?>">
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" value="Update Profile" class="btn btn-primary px-4">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Scripts -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
