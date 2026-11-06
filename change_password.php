<?php
include "auth.php";
include "../includes/db.php";

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old  = $_POST["old_password"];
    $new  = $_POST["new_password"];
    $conf = $_POST["confirm_password"];
    $uid  = (int)$_SESSION["admin_id"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if (!$u || !password_verify($old, $u["password"])) {
        $err = "Current password is wrong.";
    } elseif (strlen($new) < 8) {
        $err = "New password must be at least 8 characters.";
    } elseif ($new !== $conf) {
        $err = "New password and confirm password do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $up = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $up->bind_param("si", $hash, $uid);
        $up->execute();
        $msg = "Password changed successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<div class="main">
  <h1>Change Password</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='alert bad'>$err</div>"; ?>

  <form method="POST" class="add-room" style="flex-direction:column; max-width:420px;">
    <label>Current Password</label>
    <input type="password" name="old_password" required>

    <label>New Password (min 8 characters)</label>
    <input type="password" name="new_password" required>

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" required>

    <button type="submit" class="act green" style="margin-top:10px;">Update Password</button>
  </form>
</div>

</body>
</html>