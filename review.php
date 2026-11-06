<?php
include "includes/db.php";

if (!isset($_SESSION["customer_id"])) { header("Location: login.php"); exit; }
$cid = (int)$_SESSION["customer_id"];
$bid = (int)($_GET["booking_id"] ?? $_POST["booking_id"] ?? 0);

$stmt = $conn->prepare("
  SELECT b.id, b.status, r.room_number, t.name AS type_name
  FROM bookings b
  JOIN rooms r ON b.room_id = r.id
  JOIN room_types t ON r.room_type_id = t.id
  WHERE b.id = ? AND b.customer_id = ?
");
$stmt->bind_param("ii", $bid, $cid);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

$error = "";
$done = false;

if (!$b) {
    $error = "Booking not found.";
} elseif (!in_array($b["status"], ["checked_in", "checked_out"])) {
    $error = "You can review a stay only after check-in.";
} else {
    $chk = $conn->prepare("SELECT id FROM reviews WHERE booking_id = ?");
    $chk->bind_param("i", $bid);
    $chk->execute();
    if ($chk->get_result()->fetch_assoc()) {
        $error = "You have already reviewed this booking. Thank you!";
    }
}

if (!$error && $_SERVER["REQUEST_METHOD"] == "POST") {
    $rating  = (int)($_POST["rating"] ?? 0);
    $comment = trim($_POST["comment"]);

    if ($rating < 1 || $rating > 5) {
        $error = "Please choose a star rating.";
    } elseif (strlen($comment) < 5) {
        $error = "Please write a short comment.";
    } else {
        $name = $_SESSION["customer_name"];
        $ins = $conn->prepare("INSERT INTO reviews (booking_id, customer_id, name, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $ins->bind_param("iisis", $bid, $cid, $name, $rating, $comment);
        $ins->execute();
        $done = true;
    }
}

include "includes/header.php";
?>

<h2 class="section-title">Rate Your Stay</h2>

<div class="form-box" style="max-width:520px;">
  <?php if ($done) { ?>
    <div class="msg-success">Thank you for your review! It is now live on our home page.</div>
    <p style="text-align:center;"><a href="index.php" class="btn">Go to Home</a></p>
  <?php } else { ?>
    <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>

    <?php if ($b && !$error || ($b && $_SERVER["REQUEST_METHOD"] == "POST")) { ?>
      <p style="margin-bottom:10px;"><?php echo htmlspecialchars($b["type_name"]); ?> (Room <?php echo htmlspecialchars($b["room_number"]); ?>) &middot; Booking #<?php echo $b["id"]; ?></p>
      <form method="POST">
        <input type="hidden" name="booking_id" value="<?php echo $b["id"]; ?>">

        <label>Your Rating</label>
        <div class="star-rating">
          <?php for ($i = 5; $i >= 1; $i--) { ?>
            <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">★</label>
          <?php } ?>
        </div>

        <label>Your Review</label>
        <textarea name="comment" rows="5" required style="width:100%; padding:11px; border:1px solid #d9e0d3; border-radius:6px; font-family:inherit; font-size:15px;"></textarea>

        <button type="submit" class="btn">Submit Review</button>
      </form>
    <?php } else { ?>
      <p style="text-align:center;"><a href="my_bookings.php" class="btn">Back to My Bookings</a></p>
    <?php } ?>
  <?php } ?>
</div>

<?php include "includes/footer.php"; ?>