<?php
include "includes/db.php";

if (isset($_SESSION["customer_id"])) { header("Location: my_bookings.php"); exit; }

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $pass  = $_POST["password"];

    if (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $chk = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $chk->bind_param("s", $email);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $error = "This email is already registered. Please login.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO customers (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $ins->bind_param("ssss", $name, $email, $phone, $hash);
            $ins->execute();

            $_SESSION["customer_id"]    = $conn->insert_id;
            $_SESSION["customer_name"]  = $name;
            $_SESSION["customer_email"] = $email;
            $_SESSION["customer_phone"] = $phone;
            header("Location: my_bookings.php");
            exit;
        }
    }
}

include "includes/header.php";
?>

<h2 class="section-title">Create Account</h2>

<div class="form-box" style="max-width:480px;">
  <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>
  <form method="POST">
    <label>Full Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Phone</label>
    <input type="text" name="phone" required>

    <label>Password (min 6 characters)</label>
    <input type="password" name="password" required>

    <button type="submit" class="btn">Register</button>
  </form>
  <p style="text-align:center; margin-top:15px;">Already have an account? <a href="login.php">Login</a></p>
</div>

<?php include "includes/footer.php"; ?>