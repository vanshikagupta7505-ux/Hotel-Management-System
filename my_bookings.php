<?php
include "includes/db.php";

if (!isset($_SESSION["customer_id"])) { header("Location: login.php"); exit; }
$cid = (int)$_SESSION["customer_id"];

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];

    $stmt = $conn->prepare("SELECT status, check_in FROM bookings WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $id, $cid);
    $stmt->execute();
    $b = $stmt->get_result()->fetch_assoc();

    if (!$b) {
        $err = "Booking not found.";
    } elseif (!in_array($b["status"], ["pending", "confirmed"])) {
        $err = "This booking cannot be cancelled.";
    } elseif ($b["check_in"] <= date("Y-m-d")) {
        $err = "Booking cannot be cancelled on or after the check-in date. Please contact the hotel.";
    } else {
        $u = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND customer_id = ?");
        $u->bind_param("ii", $id, $cid);
        $u->execute();
        $msg = "Booking #" . $id . " has been cancelled.";
    }
}

$stmt = $conn->prepare("
   SELECT b.id, b.check_in, b.check_out, b.total_amount, b.status, b.payment_status,
         r.room_number, t.name AS type_name
  FROM bookings b
  JOIN rooms r ON b.room_id = r.id
  JOIN room_types t ON r.room_type_id = t.id
  WHERE b.customer_id = ?
  ORDER BY b.id DESC
");
$stmt->bind_param("i", $cid);
$stmt->execute();
$bookings = $stmt->get_result();

include "includes/header.php";
?>

<h2 class="section-title">My Bookings</h2>

<div class="my-wrap">
  <p class="welcome-line">Hello, <b><?php echo htmlspecialchars($_SESSION["customer_name"]); ?></b>!</p>

  <?php if ($msg) echo "<div class='msg-success'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='msg-error'>$err</div>"; ?>

  <table class="my-table">
    <tr>
      <th>ID</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Amount</th><th>Status</th><th>Action</th>
    </tr>
    <?php $count = 0; while ($b = $bookings->fetch_assoc()) { $count++; ?>
    <tr>
      <td>#<?php echo $b["id"]; ?></td>
      <td><?php echo $b["type_name"]; ?> (<?php echo $b["room_number"]; ?>)</td>
      <td><?php echo date("d M Y", strtotime($b["check_in"])); ?></td>
      <td><?php echo date("d M Y", strtotime($b["check_out"])); ?></td>
      <td>Rs. <?php echo number_format($b["total_amount"]); ?></td>
            <td>
        <span class="pill <?php echo $b["status"]; ?>"><?php echo str_replace("_", " ", $b["status"]); ?></span>
        <?php if ($b["status"] != "cancelled") { ?>
          <br>
          <?php if ($b["payment_status"] == "paid") { ?>
            <small style="color:#3f5f3a;">&#10004; Paid</small>
          <?php } else { ?>
            <a href="payment.php?id=<?php echo $b['id']; ?>" class="pay-link">Pay Now</a>
          <?php } ?>
        <?php } ?>
      </td>
        <?php if (in_array($b["status"], ["pending", "confirmed"]) && $b["check_in"] > date("Y-m-d")) { ?>
          <form method="POST" onsubmit="return confirm('Cancel this booking?')">
            <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
            <button type="submit" class="cancel-btn">Cancel</button>
          </form>
                <?php } elseif (in_array($b["status"], ["checked_in", "checked_out"])) { ?>
          <a href="review.php?booking_id=<?php echo $b['id']; ?>" class="cancel-btn" style="background:#c9a86a; text-decoration:none; display:inline-block;">Review</a>
        <?php } else { echo "-"; } ?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($count == 0) { ?>
      <tr><td colspan="7" style="text-align:center; padding:30px;">No bookings yet. <a href="booking.php">Book your first stay</a></td></tr>
    <?php } ?>
  </table>
</div>

<?php include "includes/footer.php"; ?>