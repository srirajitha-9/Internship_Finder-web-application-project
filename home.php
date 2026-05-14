<?php
session_start();
include 'db.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Internship Finder - Home</title>
  <link rel="stylesheet" href="style.css">
</head>

<body >
<!-- TOP NAVIGATION -->
<header class="top-nav">
    <div class="logo">Internship Finder 

    </div>

    <nav class="menu">
        <?php if(isset($_SESSION['user_id'])): ?>
            <span class="user-text">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php">Logout</a>

            <?php if($_SESSION['role']=='student'): ?>
                <a href="student_dashboard.php">Dashboard</a>
            <?php else: ?>
                <a href="company_dashboard.php">Company Panel</a>
            <?php endif; ?>

        <?php else: ?>
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="login.php">Login</a>
             <div class="set">
                <select onchange="location.href=this.value" class="account-select">
                    <option selected disabled>Sign in Accounts</option> 
                    <option value="signup_student.php">Signup (Student)</option>
                    <option value="signup_company.php">Signup (Company)</option>
                </select>
            </div>
        <?php endif; ?>
    </nav>
</header>


<!-- HERO SECTION -->
<section class="hero">
   <div class="overlay">
        <div class="hero-content">
            <h1>Find the Perfect Internship</h1>
            <p>Explore opportunities tailored to your career path</p>
        </div>
    </div>
</section>


<!-- MAIN CONTENT -->
<div class="container">

    <div class="section-title">Explore Options</div>

    <div class="card-grid">

        <!-- Available Internships 
        <a class="card" href="internships.php">
            <img src="available.png" alt="Browse Internships">
            <h3>Browse All Internships</h3>
        </a>
        -->

        <!-- Post Internship -->
         <br>
            <h3>Internship Post</h3>
        <a class="card" href="post_internship.php">
            <img src="Post.png" alt="Post Internship">

        </a>
        

    </div>


    <!-- Popular Categories -->
    <div class="section-title">Popular Categories</div>

    <div class="categories">
        <span>Software Engineering</span>
        <span>Marketing</span>
        <span>Medicine</span>
        <span>Cybersecurity</span>
    </div>

</div>

</body>
</html>
