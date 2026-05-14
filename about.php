<?php
session_start();
include 'db.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>About page</title>
  <link rel="stylesheet" href="style.css">
</head>

<body style=background-color:#78ace8;>

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

    <div class="about-container">

     <p class="about-text">
        The Internship Finder website is a web-based platform designed to help students
        easily find internship opportunities that match their interests and skills.
        Students can browse internships based on selected categories, view company
        details, and apply directly through the platform. The system ensures that
        students only see internships relevant to their chosen field, reducing time
        and effort in searching. It also improves communication between students and
        companies by providing a centralized and organized interface. Overall, the
        website aims to make the internship search process efficient, user-friendly,
        and reliable.
     </p>

        <h3>Advantages of the Internship Finder</h3>

    <ul class="advantages">
        <li>Helps students quickly find internships that match their skills and career interests.</li>
        <li>Saves time by displaying all available opportunities in one platform.</li>
        <li>Improves accessibility by allowing students to apply online from anywhere.</li>
        <li>Provides equal opportunities for students by showing verified internship listings.</li>
        <li>Strengthens the connection between students and organizations, making recruitment more efficient.</li>
    </ul>

    </div>

</body>
</html>
