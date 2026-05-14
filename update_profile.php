<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$msg = "";



/* Handle form submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $skills = isset($_POST['skills']) ? trim($_POST['skills']) : '';
    $qualifications = isset($_POST['qualifications']) ? trim($_POST['qualifications']) : '';

    if ($skills === "" || $qualifications === "") {
        $msg = "All fields are required.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE students SET skills = ?, qualifications = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $skills, $qualifications, $uid);

        if ($stmt->execute()) {
            $msg = "Profile updated successfully.";
        } else {
            $msg = "Profile update failed.";
        }
    }
}


/* Fetch existing data */
$get = $conn->prepare("SELECT skills, qualifications FROM students WHERE id = ? LIMIT 1");
$get->bind_param("i", $uid);
$get->execute();
$res = $get->get_result();
$data = $res->fetch_assoc();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Update Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card" style="max-width:700px;margin:auto">
        <h2>Update Profile</h2>

        <?php if ($msg): ?>
            <div class="notice"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-row">
                <label>Skills</label>
                <textarea name="skills" required><?php
                    echo htmlspecialchars($data['skills'] ?? '');
                ?></textarea>
            </div>

            <div class="form-row">
                <label>Qualifications</label>
                <textarea name="qualifications" required><?php
                    echo htmlspecialchars($data['qualifications'] ?? '');
                ?></textarea>
            </div>

            <div class="form-row">
                <button type="submit">Update Profile</button>
            </div>
        </form>

        <p class="small">
            <a href="student_dashboard.php">Back to Dashboard</a>
        </p>
    </div>
</div>
</body>
</html>
