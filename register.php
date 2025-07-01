<?php
include('config.php');

// Define variables and initialize with empty values
$username = $password = $email = $phone = $age = $usertype = $name = $cnic = $address = $town = $region = $postcode = $country = "";
$username_err = $password_err = $email_err = $phone_err = $age_err = $usertype_err = $name_err = $cnic_err = $address_err = $town_err = $region_err = $postcode_err = $country_err = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate and process each field

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username already exists in database
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email already exists in database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email address is already taken.";
        }
    }

    // Validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } elseif (!ctype_digit($_POST["phone"]) || strlen($_POST["phone"]) != 11) {
        $phone_err = "Phone number must be exactly 11 digits.";
    } else {
        $phone = trim($_POST["phone"]);
        // Check if phone number already exists in database
        $sql = "SELECT id FROM users WHERE phone = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_phone);
        $param_phone = $phone;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $phone_err = "This phone number is already taken.";
        }
    }

    // Validate age
    if (empty(trim($_POST["age"]))) {
        $age_err = "Please enter your age.";
    } elseif (!is_numeric($_POST["age"])) {
        $age_err = "Age must be a number.";
    } else {
        $age = trim($_POST["age"]);
        if ($age < 18) {
            $age_err = "You must be at least 18 years old to register.";
        }
    }

    // Validate user type
    if (empty(trim($_POST["usertype"]))) {
        $usertype_err = "Please select a user type.";
    } else {
        $usertype = trim($_POST["usertype"]);
    }

    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

// Validate CNIC
if (empty(trim($_POST["cnic"]))) {
    $cnic_err = "Please enter your CNIC.";
} elseif (!ctype_digit($_POST["cnic"]) || strlen($_POST["cnic"]) != 13) {
    $cnic_err = "CNIC must be a numeric value of exactly 13 digits.";
} else {
    $cnic = trim($_POST["cnic"]);
}

    

    // Validate address
    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    // Validate town
    if (empty(trim($_POST["town"]))) {
        $town_err = "Please enter your town.";
    } else {
        $town = trim($_POST["town"]);
    }

    // Validate region
    if (empty(trim($_POST["region"]))) {
        $region_err = "Please enter your region.";
    } else {
        $region = trim($_POST["region"]);
    }

    // Validate postcode
    if (empty(trim($_POST["postcode"]))) {
        $postcode_err = "Please enter your postcode.";
    } else {
        $postcode = trim($_POST["postcode"]);
    }

    // Validate country
    if (empty(trim($_POST["country"]))) {
        $country_err = "Please enter your country.";
    } else {
        $country = trim($_POST["country"]);
    }

    // If no errors, insert user into database
    if (empty($username_err) && empty($password_err) && empty($email_err) && empty($phone_err) && empty($age_err) && empty($usertype_err) && empty($name_err) && empty($cnic_err) && empty($address_err) && empty($town_err) && empty($region_err) && empty($postcode_err) && empty($country_err)) {
        $sql = "INSERT INTO users (username, password, email, phone, age, usertype, name, cnic, address, town, region, postcode, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssss", $param_username, $param_password, $param_email, $param_phone, $param_age, $param_usertype, $param_name, $param_cnic, $param_address, $param_town, $param_region, $param_postcode, $param_country);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_email = $email;
        $param_phone = $phone;
        $param_age = $age;
        $param_usertype = $usertype;
        $param_name = $name;
        $param_cnic = $cnic;
        $param_address = $address;
        $param_town = $town;
        $param_region = $region;
        $param_postcode = $postcode;
        $param_country = $country;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo '<div class="alert alert-success" role="alert">User registered successfully.</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield; /* Firefox */
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h3>User Registration</h3>
            <p>Please fill in your details to register.</p>
        </div>
        <div class="card-body">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" placeholder="Enter username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <span class="invalid-feedback"><?php echo $username_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" placeholder="Enter password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Enter email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="number" name="phone" placeholder="03XXXXXXXXX" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" name="age" placeholder="Enter age" class="form-control <?php echo (!empty($age_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $age; ?>">
                            <span class="invalid-feedback"><?php echo $age_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>User Type</label>
                            <select name="usertype" class="form-control <?php echo (!empty($usertype_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select user type</option>
                                <option value="candidate" <?php echo ($usertype == 'candidate') ? 'selected' : ''; ?>>Candidate(Job Seeker)</option>
                                <option value="recruiter" <?php echo ($usertype == 'recruiter') ? 'selected' : ''; ?>>Recruiter(Employer)</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $usertype_err; ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" placeholder="Enter full name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>CNIC</label>
                            <input type="number" name="cnic" placeholder="Enter CNIC without dashes" class="form-control <?php echo (!empty($cnic_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cnic; ?>">
                            <span class="invalid-feedback"><?php echo $cnic_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" placeholder="Enter full address" class="form-control <?php echo (!empty($address_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $address; ?>">
                            <span class="invalid-feedback"><?php echo $address_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Town</label>
                            <input type="text" name="town" placeholder="Enter town" class="form-control <?php echo (!empty($town_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $town; ?>">
                            <span class="invalid-feedback"><?php echo $town_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Region</label>
                            <input type="text" name="region" placeholder="Enter region" class="form-control <?php echo (!empty($region_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $region; ?>">
                            <span class="invalid-feedback"><?php echo $region_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Postcode</label>
                            <input type="text" name="postcode" placeholder="Enter postcode" class="form-control <?php echo (!empty($postcode_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $postcode; ?>">
                            <span class="invalid-feedback"><?php echo $postcode_err; ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" placeholder="Enter country" class="form-control <?php echo (!empty($country_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $country; ?>">
                    <span class="invalid-feedback"><?php echo $country_err; ?></span>
                </div>
                <div class="form-group text-center">
                    <input type="submit" class="btn btn-success" value="Register">
                </div>
            </form>
            <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
