<?php
session_start();
include('config.php');

// Fetch all active jobs
$sql = "SELECT J.*, R.company_name FROM Jobs J INNER JOIN Recruiters R ON J.recruiter_id = R.recruiter_id WHERE J.status = 'active' LIMIT 6";
$result = mysqli_query($conn, $sql);

// Handle search query
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT J.*, R.company_name FROM Jobs J INNER JOIN Recruiters R ON J.recruiter_id = R.recruiter_id WHERE J.status = 'active' AND (J.job_title LIKE '%$search%' OR J.location LIKE '%$search%' OR J.industry LIKE '%$search%' OR J.keywords LIKE '%$search%')";
    $result = mysqli_query($conn, $sql);
}

// Fetch total job count
$sql_job_count = "SELECT COUNT(*) as total_jobs FROM Jobs WHERE status = 'active'";
$job_count_result = mysqli_query($conn, $sql_job_count);
$job_count = mysqli_fetch_assoc($job_count_result)['total_jobs'];

// Fetch total company count
$sql_company_count = "SELECT COUNT(DISTINCT company_name) as total_companies FROM Recruiters";
$company_count_result = mysqli_query($conn, $sql_company_count);
$company_count = mysqli_fetch_assoc($company_count_result)['total_companies'];

// Fetch total user count
$sql_user_count = "SELECT COUNT(*) as total_users FROM Users WHERE usertype = 'candidate'";
$user_count_result = mysqli_query($conn, $sql_user_count);
$user_count = mysqli_fetch_assoc($user_count_result)['total_users'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Job Portal - Your Gateway to Opportunities</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .navbar {
            background-color: #007bff;
            padding: 1rem 0;
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #e0e0e0 !important;
        }

        .jumbotron {
            background: linear-gradient(rgba(0, 123, 255, 0.85), rgba(0, 123, 255, 0.85)), url('./images/hotel.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 0;
            text-align: center;
            border-radius: 0;
            height:450px;
        }

        .jumbotron h1 {
            font-size: 2.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .jumbotron p {
            font-size: 1.2rem;
            font-weight: 300;
        }

        .btn-primary {
            background-color: #17a2b8;
            border-color: #17a2b8;
            padding: 0.7rem 2rem;
            font-size: 1.1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #138496;
            border-color: #138496;
            transform: translateY(-2px);
        }

        .stats-section {
            padding: 50px 0;
            background-color: #fff;
        }

        .stat-item {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin: 10px 0;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .stat-item i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 10px;
        }

        .count {
            font-size: 2.2rem;
            font-weight: 600;
            color: #333;
        }

        .label {
            font-size: 1rem;
            color: #666;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.3rem;
            font-weight: 600;
            color: #007bff;
        }

        .job-card {
            height: 100%;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .job-card .card-body {
            padding: 1.5rem;
        }

        .job-card .card-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
        }

        .job-card .card-text {
            color: #666;
            font-size: 0.95rem;
        }

        .social-icons a {
            color: #007bff;
            margin-right: 10px;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #17a2b8;
        }

        .about-section, .services-section, .contact-section {
            padding: 50px 0;
            background-color: #f8f9fa;
        }

        .about-section p, .services-section ul, .contact-section p {
            font-size: 1rem;
            line-height: 1.7;
            color: #555;
        }

        .services-section ul {
            list-style: none;
            padding-left: 0;
        }

        .services-section ul li {
            padding: 8px 0;
            font-size: 1rem;
            color: #333;
        }

        .services-section ul li::before {
            content: "\f058";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #17a2b8;
            margin-right: 8px;
        }

        .contact-section .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 0.7rem;
            font-size: 0.95rem;
        }

        .contact-section .btn-primary {
            width: 100%;
            padding: 0.7rem;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .jumbotron h1 {
                font-size: 2rem;
            }

            .jumbotron p {
                font-size: 1rem;
            }

            .section-header {
                font-size: 1.8rem;
            }

            .stat-item {
                margin-bottom: 20px;
            }

            .job-card .card-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="jumbotron text-center">
    <div class="container">
        <h1 class="display-4">Welcome to Job Portal</h1>
        <p class="lead">Discover Your Dream Job with Our Advanced Recruitment Platform</p>
        <a href="login.php" class="btn btn-primary btn-lg">Get Started Now</a>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-briefcase"></i>
                    <div class="count"><?php echo $job_count; ?></div>
                    <div class="label">Active Jobs</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-building"></i>
                    <div class="count"><?php echo $company_count; ?></div>
                    <div class="label">Companies</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <div class="count"><?php echo $user_count; ?></div>
                    <div class="label">Job Seekers</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <section id="jobs" class="py-5">
        <h2 class="section-header">Featured Job Opportunities</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search for jobs by title, location, or industry..." aria-label="Search for jobs">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                </div>
            </div>
        </form>

        <div class="row">
            <?php if (mysqli_num_rows($result) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card job-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['job_title']); ?></h5>
                                <p class="card-text"><strong>Company:</strong> <?php echo htmlspecialchars($row['company_name']); ?></p>
                                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                <p class="card-text"><?php echo htmlspecialchars(substr($row['job_description'], 0, 100)) . '...'; ?></p>
                            </div>
                            <div class="card-footer border-0 bg-white">
                                <a href="login.php" class="btn btn-primary">View Details</a>
                                <div class="social-icons mt-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://yourwebsite.com/job_details.php?id=" . $row['job_id']); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("http://yourwebsite.com/job_details.php?id=" . $row['job_id']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode("http://yourwebsite.com/job_details.php?id=" . $row['job_id']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        No jobs found. Try a different search or check back later.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="login.php" class="btn btn-outline-primary">View All Jobs</a>
        </div>
    </section>

    <section id="about" class="about-section">
        <h2 class="section-header">About Job Portal</h2>
        <div class="row">
            <div class="col-md-6">
                <p>Job Portal is your trusted partner in connecting talent with opportunity. We empower job seekers to find their dream roles and help employers discover top talent across industries.</p>
            </div>
            <div class="col-md-6">
                <p>Since 2024, we’ve been innovating to simplify the hiring process, offering tools and resources to make job searching and recruitment seamless and efficient.</p>
            </div>
        </div>
    </section>

    <section id="services" class="services-section">
        <h2 class="section-header">Our Services</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>For Job Seekers</h4>
                <ul>
                    <li>Advanced Job Search</li>
                    <li>Resume Building Tools</li>
                    <li>Personalized Job Alerts</li>
                    <li>Application Tracking</li>
                    <li>Career Advice & Resources</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4>For Employers</h4>
                <ul>
                    <li>Efficient Job Posting</li>
                    <li>Candidate Screening Tools</li>
                    <li>Employer Branding Solutions</li>
                    <li>Recruitment Analytics</li>
                    <li>Consulting Services</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="contact" class="contact-section">
        <h2 class="section-header">Contact Us</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>Get in Touch</h4>
                <p><i class="fas fa-envelope mr-2"></i> Email: contact@careerhub.com</p>
                <p><i class="fas fa-phone mr-2"></i> Phone: +1 (555) 123-4567</p>
                <p><i class="fas fa-map-marker-alt mr-2"></i> Address: 123 Career Street, Opportunity City, JB 12345</p>
            </div>
            <div class="col-md-6">
                <h4>Send Us a Message</h4>
                <form action="contact_form.php" method="post">
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>
</div>

<footer class="bg-dark text-white py-4">
    <div class="container text-center">
        <p>© 2025 Job Portal. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>