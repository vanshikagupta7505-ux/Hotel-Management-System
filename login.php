<?php
session_start();
include "../includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pass  = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user["password"])) {
        $_SESSION["admin_id"]   = $user["id"];
        $_SESSION["admin_name"] = $user["name"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Wrong email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Grand Stay</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="login-page">
  <div class="login-box">
    <h2>Grand Stay Admin</h2>
    <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>
    <form method="POST">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit" class="btn">Login</button>
    </form>
    <p style="text-align:center; margin-top:15px;"><a href="../index.php">&larr; Back to website</a></p>
  </div>
</body>
</html>