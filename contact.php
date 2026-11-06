<?php
include "includes/db.php";
include "includes/header.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $phone   = trim($_POST["phone"]);
    $subject = trim($_POST["subject"]);
    $msg     = trim($_POST["message"]);

    if ($name == "" || $email == "" || $msg == "") {
        $error = "Please fill name, email and message.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $msg);
        $stmt->execute();
        $message = "Thank you, " . htmlspecialchars($name) . "! Your message has been sent. We will contact you soon.";
    }
}
?>

<h2 class="section-title">Contact Us</h2>

<div class="contact-wrap">

  <div class="contact-info">
    <div class="info-card">
      <div class="icon">📍</div>
      <div><h4>Address</h4><p>Grand Stay Hotel, Civil Lines<br>Bareilly, Uttar Pradesh</p></div>
    </div>
    <div class="info-card">
      <div class="icon">📞</div>
      <div><h4>Phone</h4><p>+91 98765 43210</p></div>
    </div>
    <div class="info-card">
      <div class="icon">✉️</div>
      <div><h4>Email</h4><p>info@grandstay.com</p></div>
    </div>
    <div class="info-card">
      <div class="icon">🕒</div>
      <div><h4>Timings</h4><p>Reception: 24x7<br>Check-in: 12 PM | Check-out: 11 AM</p></div>
    </div>
  </div>

  <div class="form-box contact-form">
    <?php if ($message) echo "<div class='msg-success'>$message</div>"; ?>
    <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>

    <form method="POST">
      <label>Your Name</label>
      <input type="text" name="name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Phone (optional)</label>
      <input type="text" name="phone">

      <label>Subject</label>
      <input type="text" name="subject" placeholder="e.g. Booking enquiry">

      <label>Message</label>
      <textarea name="message" rows="5" required></textarea>

      <button type="submit" class="btn">Send Message</button>
    </form>
  </div>

</div>

<div class="map-box">
  <iframe src="https://www.google.com/maps?q=Bareilly,Uttar+Pradesh&output=embed" loading="lazy" allowfullscreen></iframe>
</div>

<?php include "includes/footer.php"; ?>