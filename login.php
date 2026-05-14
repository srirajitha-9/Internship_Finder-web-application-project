<?php
session_start();
include 'db.php';
$msg='';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; // 'student' or 'company'

    if($role === 'student'){
        $stmt = $conn->prepare("SELECT id,name,password FROM students WHERE email=? LIMIT 1");
    } else {
        $stmt = $conn->prepare("SELECT id,name,password FROM companies WHERE email=? LIMIT 1");
    }
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res && $res->num_rows==1){
        $row = $res->fetch_assoc();
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $role;
            $_SESSION['user_name'] = $row['name'];
            if($role=='student') header("Location: student_dashboard.php");
            else header("Location: company_dashboard.php");
            exit;
        } else $msg = "Invalid password.";
    } else $msg = "Email not found.";
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title><link rel="stylesheet"href="style.css"></head>
<body>
 <!-- <div class="container">
    <div class="card" style="max-width:420px;margin:auto">
      <h2 class="center">Login</h2>
      <?php if($msg) echo "<div class='notice'>{$msg}</div>"; ?>
      <form method="post">
        <div class="form-row"><label>Role</label>
          <select name="role" required><option value="student">Student</option><option value="company">Company</option></select>
        </div>
        <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
        <div class="form-row"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-row"><button type="submit">Login</button></div>
      </form>
      <p class="small center">Student? <a href="signup_student.php">Create account</a> • Company? <a href="signup_company.php">Create company account</a></p>
    </div>
  </div>-->



<div class="login-wrapper">
    <div class="login-card">
        <h2>Welcome Back</h2>
        <p class="login-sub">Login to continue</p>

        <?php if($msg) echo "<div class='notice center'>{$msg}</div>"; ?>

        <form method="post">
            <div class="form-row">
                <label>Login As</label>
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="company">Company</option>
                </select>
            </div>

            <div class="form-row">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="example@email.com" required>
            </div>

            <div class="form-row">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="login-footer">
            <p>Student? <a href="signup_student.php">Create account</a></p>
            <p>Company? <a href="signup_company.php">Create company account</a></p>
        </div>
    </div>
</div>

</body>
</html>
