<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='student'){
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];

/* student info (FIXED) */
$stmt = $conn->prepare(
    "SELECT name, email, category, skills, qualifications 
     FROM students WHERE id=? LIMIT 1"
);
$stmt->bind_param("i",$uid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

/* applications */
$appq = $conn->prepare(
    "SELECT a.*, i.title, c.name as company_name 
     FROM applications a 
     JOIN internships i ON i.id=a.internship_id 
     JOIN companies c ON c.id=i.company_id 
     WHERE a.student_id=? 
     ORDER BY a.applied_at DESC"
);
$appq->bind_param("i",$uid);
$appq->execute();
$apps = $appq->get_result();

/* matched internships */
$matchq = $conn->prepare(
    "SELECT i.*, c.name as company_name 
     FROM internships i 
     JOIN companies c ON c.id=i.company_id 
     WHERE i.category=? 
     ORDER BY i.id DESC"
);
$matchq->bind_param("s",$student['category']);
$matchq->execute();
$matches = $matchq->get_result();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color:#1e88e5">
    <header class="top-nav">
        <div class="container">
            

            <div class="header">
                <h1 style="color:#ffffff">Student Dashboard</h1>

            </div>
        </div>  

           
                <div class="nav" style="text-align:right;">
                    <a href="home.php"style="color:#ffffff;
                  margin-right:20px;
                  text-decoration:none;
                  font-weight:600;">Home</a>
                    <a href="logout.php" style="color:#ffffff;
                  text-decoration:none;
                  font-weight:600;">Logout</a>
                
                 </div>


     </header>
   

    <!-- SUCCESS / ERROR MESSAGES -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="notice" style="color:green;">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="notice" style="color:red;">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="grid">
        <main>

            <div class="card">
                <h3>Welcome, <?php echo htmlspecialchars($student['name']); ?></h3>
                <div class="small">
                    Category: <?php echo htmlspecialchars($student['category']); ?>
                </div>
            </div>

            <br>
            

            <!-- PROFILE UPDATE CARD -->
            <div class="card">
                <h3>Your Profile</h3>

                <form method="post" action="update_profile.php">
                    <div class="form-row">
                        <label>Skills</label>
                        <!--<textarea name="skills" rows="3"
                            placeholder="Eg: PHP, Java, HTML, CSS"></textarea>
    -->
                            <?php
                            echo htmlspecialchars($student['skills'] ?? '');
                        ?>
                    </div>
                    <br>

                    <div class="form-row">
                        <label>Qualifications</label>
                        <!--<textarea name="qualifications" rows="3"
                            placeholder="Eg: Diploma in IT, BICT Undergraduate">
                        ?></textarea>
                        -->
                        <?php
                            echo htmlspecialchars($student['qualifications'] ?? '');?>
                    </div>

                    <button type="submit">Update Profile</button>
                </form>
            </div>
            <br>

            <!-- MATCHED INTERNSHIPS -->
            <div class="card">
                <h3>Matched Internships (<?php echo htmlspecialchars($student['category']); ?>)</h3>
                <ul class="list">
                    <?php while($r = $matches->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($r['title']); ?></strong>
                            <span class="small">by <?php echo htmlspecialchars($r['company_name']); ?></span>
                            <div class="small">
                                <?php echo nl2br(htmlspecialchars($r['description'])); ?>
                            </div>
                            <div style="margin-top:8px">
                                <a href="apply.php?id=<?php echo $r['id']; ?>">
                                    <button type="button" class="login-btn">Apply</button>
                                </a>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <br>

            <!-- APPLICATIONS -->
            <div class="card">
                <h3>Your Applications</h3>
                <table class="table">
                    <tr>
                        <th>Internship</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Applied At</th>
                    </tr>
                    <?php while($a = $apps->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['title']); ?></td>
                            <td><?php echo htmlspecialchars($a['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($a['status']); ?></td>
                            <td><?php echo htmlspecialchars($a['applied_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

        </main>
    </div>
</div>
</body>
</html>
