<?php
session_start();
include 'db.php';

// fetch internships

if(isset($_SESSION['role']) && $_SESSION['role']=='student'){
    // fetch student's category
    $sid = $_SESSION['user_id'];
    $sres = $conn->prepare("SELECT category FROM students WHERE id = ? LIMIT 1");
    $sres->bind_param("i",$sid); $sres->execute(); $r=$sres->get_result(); $row = $r->fetch_assoc();
    $student_cat = $row['category'] ?? null;
    if($student_cat){
        $stmt = $conn->prepare("SELECT i.*, c.name as company_name, c.category as company_category FROM internships i JOIN companies c ON c.id=i.company_id WHERE i.category = ? ORDER BY i.id DESC");
        $stmt->bind_param("s",$student_cat);
    } else {
        $stmt = $conn->prepare("SELECT i.*, c.name as company_name, c.category as company_category FROM internships i JOIN companies c ON c.id=i.company_id ORDER BY i.id DESC");
    }
} else {
    // not a student - show all
    $stmt = $conn->prepare("SELECT i.*, c.name as company_name, c.category as company_category FROM internships i JOIN companies c ON c.id=i.company_id ORDER BY i.id DESC");
}
$stmt->execute();
$res = $stmt->get_result();
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Internships</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="container">
    <div class="header">
      <h1>Internship Listings</h1>
      <div class="nav">
        <a href="home.php">Home</a>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="grid">
      <div>
        <div class="card">
          <h3>Open Internships</h3>
          <ul class="list">
            <?php while($row = $res->fetch_assoc()): ?>
              <li>
                <strong><?php echo htmlspecialchars($row['title']); ?></strong> <span class="small">by <?php echo htmlspecialchars($row['company_name']); ?> (<?php echo htmlspecialchars($row['company_category']); ?>)</span>
                <div class="small"><?php echo nl2br(htmlspecialchars($row['description'])); ?></div>
                <div style="margin-top:6px" class="flex">
                  <span class="small">Category: <?php echo htmlspecialchars($row['category']); ?></span>
                  <?php if(isset($_SESSION['role']) && $_SESSION['role']=='student'): ?>
                    <a href="apply.php?id=<?php echo $row['id']; ?>" style="margin-left:auto">Apply</a>
                  <?php endif; ?>
                </div>
              </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>

      <aside>
        <div class="card">
          <h4>Info</h4>
          <?php if(isset($_SESSION['role']) && $_SESSION['role']=='student'): ?>
            <div class="small">You see internships matching your category (<?php echo htmlspecialchars($student_cat ?? '—'); ?>).</div>
          <?php else: ?>
            <div class="small">Login as student to see category-matched internships.</div>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</body>
</html>
