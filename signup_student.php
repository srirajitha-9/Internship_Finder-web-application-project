<?php
session_start();
include 'db.php';
$msg = '';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $category = $_POST['category'];

    if(!$name || !$email || !$password){
        $msg = "Please fill required fields.";
    } else {
        // check exists
        $stmt = $conn->prepare("SELECT id FROM students WHERE email=? LIMIT 1");
        $stmt->bind_param("s",$email);
        $stmt->execute(); $stmt->store_result();
        if($stmt->num_rows>0){
            $msg = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO students (name,email,password,category) VALUES (?,?,?,?)");
            $ins->bind_param("ssss",$name,$email,$hash,$category);
            if($ins->execute()){
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['role'] = 'student';
                $_SESSION['user_name'] = $name;
                header("Location: student_dashboard.php");
                exit;
            } else $msg = "Registration failed.";
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Student Signup</title><link rel="stylesheet" href="style.css"></head>
<body>
  <div class="container">
    <div class="card" style="max-width:700px;margin:auto">
      <h2>Student Signup</h2>
      <?php if($msg) echo "<div class='notice'>{$msg}</div>"; ?>
      <form method="post">
        <div class="form-row"><label>Name</label><input type="text" name="name" required></div>
        <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-row"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-row"><label>Category</label>
          <select name="category" required>
            <option value="">-- Select category --</option>
            <option value="software">Software</option>
            <option value="marketing">Marketing</option>
            <option value="medicine">Medicine</option>
            <option value="cybersecurity">Cybersecurity</option>
            <option value="finance">Finance</option>
            <option value="engineering">Engineering</option>
          </select>
        </div>
        <div class="form-row"><button type="submit">Create account</button></div>
      </form>
      <p class="small">Already have account? <a href="login.php">Login</a></p>
    </div>
  </div>
</body>
</html>
