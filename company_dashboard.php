<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='company'){
    header("Location: login.php"); exit;
}
$cid = $_SESSION['user_id'];

// company posts
$stmt = $conn->prepare("SELECT * FROM internships WHERE company_id = ? ORDER BY id DESC");
$stmt->bind_param("i",$cid); $stmt->execute(); $posts = $stmt->get_result();
$msg='';

// handle action hire/reject if requested
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['action'])){
    $action = $_POST['action'];
    $app_id = intval($_POST['app_id']);
    if($action && in_array($action,['shortlisted','rejected','hired'])){
        $u = $conn->prepare("UPDATE applications SET status=? WHERE id=?");
        $u->bind_param("si",$action,$app_id); $u->execute();
        $msg = "Updated application.";
    }
}

// fetch applicants for the company's internships
$appsQ = $conn->prepare("
    SELECT a.*, s.name as student_name, i.title as internship_title 
    FROM applications a 
    JOIN students s ON s.id=a.student_id 
    JOIN internships i ON i.id=a.internship_id 
    WHERE i.company_id = ? 
    ORDER BY a.applied_at DESC
");
$appsQ->bind_param("i",$cid); $appsQ->execute(); $apps = $appsQ->get_result();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color:#3b9df2;">
  <header class="top-nav">
    <div class="container">
      <div class="header">
        <h1 style="color:#ffffff;">Company Dashboard</h1>
      </div>
    </div>

 
        <div class="nav" style="text-align: right;">
          <a href="home.php" style="color:#ffffff;">Home</a>
          <a href="logout.php" style="color:#ffffff;">Logout</a>
        
         </div>
    </header>
 

    <?php if($msg) echo "<div class='card notice'>{$msg}</div>"; ?>

    <div class="grid">
      <main>
        <!-- Internship Posts -->
        <div class="card">
          <h3>Your Internship Posts</h3>
          <p class="small">
            <a href="post_internship.php"><button type="submit">Create new post</button></a>
          </p>
          <ul class="list">
            <?php while($p = $posts->fetch_assoc()): ?>
              <li>
                <strong><?php echo htmlspecialchars($p['title']); ?></strong>
                <div class="small"><?php echo htmlspecialchars($p['category']); ?> • <?php echo htmlspecialchars($p['location']); ?></div>
                <div class="small"><?php echo nl2br(htmlspecialchars($p['description'])); ?></div>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
        <br>

        <!-- Applicants -->
        <div class="card">
          <h3>Applicants</h3>
          <table class="table">
            <tr>
              <th>Student</th>
              <th>Internship</th>
              <th>CV</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
            <?php while($a = $apps->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($a['student_name']); ?></td>
                <td><?php echo htmlspecialchars($a['internship_title']); ?></td>

                <!-- Cover Letter PDF -->
                <td>
                  <?php if($a['cover_letter']): ?>
                    <a href="<?php echo htmlspecialchars($a['cover_letter']); ?>"
                       target="_blank"
                       style="color:#1e88e5;font-weight:600;">
                       View the CV
                    </a>
                  <?php else: ?>
                    <span>No cover letter</span>
                  <?php endif; ?>
                </td>

                <td><?php echo htmlspecialchars($a['status']); ?></td>

                <td>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="app_id" value="<?php echo $a['id']; ?>">
                    <button name="action" value="shortlisted">Shortlist</button>
                  </form>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="app_id" value="<?php echo $a['id']; ?>">
                    <button name="action" value="rejected">Reject</button>
                  </form>
                  <form method="post" style="display:inline">
                    <input type="hidden" name="app_id" value="<?php echo $a['id']; ?>">
                    <button name="action" value="hired">Mark Hired</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </table>
        </div>
      </main>
    </div>
  </div>
</body>
</html>
