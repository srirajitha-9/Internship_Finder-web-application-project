<?php
session_start();
include 'db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company'){
    header("Location: login.php"); exit;
}
$msg='';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $category = $_POST['category'];
    $location = $_POST['location'];

    if(!$title || !$category){
        $msg = "Title and category are required.";
    } else {
        $ins = $conn->prepare("INSERT INTO internships (company_id,title,description,category,location) VALUES (?,?,?,?,?)");
        $ins->bind_param("issss", $_SESSION['user_id'], $title, $desc, $category, $location);
        if($ins->execute()){
            $msg = "Internship posted.";
        } else $msg = "Unable to post.";
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Post Internship</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="container">
    <div class="card">
      <h2>Post Internship</h2>
      <?php if($msg) echo "<div class='notice'>{$msg}</div>"; ?>
      <form method="post">
        <div class="form-row"><label>Title</label><input type="text" name="title" required></div>
        <div class="form-row"><label>Description</label><textarea name="description"></textarea></div>
        <div class="form-row"><label>Category</label>
          <select name="category" required>
            <option value="">-- select --</option>
            <option value="software">Software</option>
            <option value="marketing">Marketing</option>
            <option value="medicine">Medicine</option>
            <option value="cybersecurity">Cybersecurity</option>
            <option value="finance">Finance</option>
            <option value="engineering">Engineering</option>
          </select>
        </div>
        <div class="form-row"><label>Location</label><input type="text" name="location"></div>
        <div class="form-row"><button type="submit">Post</button></div>
      </form>
    </div>
  </div>
</body>
</html>
