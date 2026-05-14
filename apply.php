<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student'){
    header("Location: login.php"); exit;
}
$student_id = $_SESSION['user_id'];
$internship_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg='';

if($_SERVER['REQUEST_METHOD']=='POST'){
    /*$cover = trim($_POST['cover']);*/

    $coverPath = null;

if(isset($_FILES['cover']) && $_FILES['cover']['error'] === 0){
    $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

    if($ext !== 'pdf'){
        $msg = "Only PDF files are allowed.";
    } else {
        if(!is_dir('uploads')) mkdir('uploads');

        $fileName = 'cover_'.$student_id.'_'.$internship_id.'_'.time().'.pdf';
        $coverPath = 'uploads/'.$fileName;

        move_uploaded_file($_FILES['cover']['tmp_name'], $coverPath);
    }
}

    // check duplicate apply
    $chk = $conn->prepare("SELECT id FROM applications WHERE internship_id=? AND student_id=? LIMIT 1");
    $chk->bind_param("ii", $internship_id, $student_id); $chk->execute(); $chk->store_result();
    if($chk->num_rows>0) $msg = "You already applied to this internship.";
    else {
        $ins = $conn->prepare("INSERT INTO applications (internship_id, student_id, cover_letter) VALUES (?,?,?)");
        $ins->bind_param("iis", $internship_id, $student_id, $coverPath);
        if($ins->execute()) $msg = "Application submitted.";
        else $msg = "Unable to apply.";
    }
}

$intern = $conn->prepare("SELECT i.*, c.name as company_name FROM internships i JOIN companies c ON c.id=i.company_id WHERE i.id = ? LIMIT 1");
$intern->bind_param("i",$internship_id); $intern->execute(); $r = $intern->get_result(); $data = $r->fetch_assoc();
if(!$data){ echo "Internship not found."; exit; }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Apply</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="container">
    <div class="card" style="max-width:700px;margin:auto">
      <h2>Apply: <?php echo htmlspecialchars($data['title']); ?></h2>
      <div class="small">Company: <?php echo htmlspecialchars($data['company_name']); ?></div>
      <?php if($msg) echo "<div class='notice'>{$msg}</div>"; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="form-row"><!--<label>Cover letter (optional)</label>-->
        <div class="form-row">
  <label>Cover Letter (PDF only)</label>
  <input type="file" name="cover" accept=".pdf" required>
</div>
</div>
        <div class="form-row"><button type="submit">Submit Application</button></div>
      </form>
      <p class="small"><a href="student_dashboard.php">Back to listings</a></p>
    </div>
  </div>
</body>
</html>
