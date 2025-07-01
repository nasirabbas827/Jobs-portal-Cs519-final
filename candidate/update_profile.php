<?php
session_start();
include('config.php');

// Check if the user is logged in and is a candidate
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != 'candidate') {
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$first_name = $last_name = $address = $phone_number = $resume_url = $skills = $experience = $education = "";
$first_name_err = $last_name_err = $address_err = $phone_number_err = $resume_err = $skills_err = $experience_err = $education_err = "";

// Fetch current candidate profile data
$id = $_SESSION["id"];
$user_data = [];

// Fetch candidate's data from `users` table
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result && mysqli_num_rows($result) > 0) {
    $user_data = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($stmt);

$sql = "SELECT * FROM Candidates WHERE candidate_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$candidate = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Initialize form fields with fetched data or empty strings
if ($candidate) {
    $first_name = $candidate['first_name'];
    $last_name = $candidate['last_name'];
    $address = $candidate['address'];
    $phone_number = $candidate['phone_number'];
    $resume_url = $candidate['resume_url'];
    $skills = $candidate['skills'];
    $experience = $candidate['experience'];
    $education = $candidate['education'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and update profile data
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $address = trim($_POST["address"]);
    $phone_number = trim($_POST["phone_number"]);
    $skills = trim($_POST["skills"]);
    $experience = trim($_POST["experience"]);
    $education = trim($_POST["education"]);

    // Handle resume file upload
    if ($_FILES['resume']['name']) {
        $resume_url = 'uploads/' . basename($_FILES['resume']['name']);
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_url);
    }

    if (empty($first_name_err) && empty($last_name_err) && empty($address_err) && empty($phone_number_err) && empty($resume_err) && empty($skills_err) && empty($experience_err) && empty($education_err)) {
        if ($candidate) {
            // Update existing profile
            $sql = "UPDATE Candidates SET first_name = ?, last_name = ?, address = ?, phone_number = ?, resume_url = ?, skills = ?, experience = ?, education = ? WHERE candidate_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssssssssi", $first_name, $last_name, $address, $phone_number, $resume_url, $skills, $experience, $education, $id);
        } else {
            // Insert new profile
            $sql = "INSERT INTO Candidates (candidate_id, first_name, last_name, address, phone_number, resume_url, skills, experience, education) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issssssss", $id, $first_name, $last_name, $address, $phone_number, $resume_url, $skills, $experience, $education);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">Profile ' . ($candidate ? 'updated' : 'created') . ' successfully.</div>';
        // Refresh the page to fetch the latest profile data
        header("Refresh:0");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Candidate Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
    <div class="container mt-5">
    <div class="row">
        <!-- Left Side: Candidate Info from users table -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Your Profile Information</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>ID:</strong> <?php echo htmlspecialchars($user_data['id']); ?></li>
                    <li class="list-group-item"><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></li>
                    <li class="list-group-item"><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></li>
                    <li class="list-group-item"><strong>Age:</strong> <?php echo htmlspecialchars($user_data['age']); ?></li>
                    <li class="list-group-item"><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></li>
                    <li class="list-group-item"><strong>CNIC:</strong> <?php echo htmlspecialchars($user_data['cnic']); ?></li>
                    <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($user_data['address']); ?></li>
                    <li class="list-group-item"><strong>Town:</strong> <?php echo htmlspecialchars($user_data['town']); ?></li>
                    <li class="list-group-item"><strong>Region:</strong> <?php echo htmlspecialchars($user_data['region']); ?></li>
                    <li class="list-group-item"><strong>Postcode:</strong> <?php echo htmlspecialchars($user_data['postcode']); ?></li>
                    <li class="list-group-item"><strong>Country:</strong> <?php echo htmlspecialchars($user_data['country']); ?></li>
                </ul>
            </div>
        </div>

        <!-- Right Side: Profile Update Form -->
        <div class="col-md-6">
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body">
                    <h2 class="text-center">Update Profile</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control"><?php echo htmlspecialchars($address); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($phone_number); ?>">
                        </div>
                        <div class="form-group">
                            <label>Resume (PDF only)</label>
                            <input type="file" name="resume" class="form-control">
                            <?php if ($resume_url): ?>
                                <a href="<?php echo htmlspecialchars($resume_url); ?>" target="_blank">View Current Resume</a>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label>Skills</label>
                            <textarea name="skills" class="form-control"><?php echo htmlspecialchars($skills); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Experience</label>
                            <textarea name="experience" class="form-control"><?php echo htmlspecialchars($experience); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Education</label>
                            <textarea name="education" class="form-control"><?php echo htmlspecialchars($education); ?></textarea>
                        </div>
                        <div class="form-group text-center">
                            <input type="submit" value="Update Profile" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
