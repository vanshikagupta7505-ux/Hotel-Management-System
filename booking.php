<?php
include "includes/db.php";
include "includes/header.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name      = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $type_id   = (int)$_POST["room_type_id"];
    $check_in  = $_POST["check_in"];
    $check_out = $_POST["check_out"];

    if ($check_out <= $check_in) {
        $error = "Check-out date must be after check-in date.";
    } else {
        $stmt = $conn->prepare("
            SELECT r.id FROM rooms r
            WHERE r.room_type_id = ?
            AND r.status != 'maintenance'
            AND r.id NOT IN (
                SELECT room_id FROM bookings
                WHERE status IN ('pending','confirmed','checked_in')
                AND check_in < ? AND check_out > ?
            )
            LIMIT 1
        ");
        $stmt->bind_param("iss", $type_id, $check_out, $check_in);
        $stmt->execute();
        $room = $stmt->get_result()->fetch_assoc();

        if (!$room) {
            $error = "Sorry, no room of this type is available for the selected dates.";
        } else {
            $p = $conn->prepare("SELECT price_per_night FROM room_types WHERE id = ?");
            $p->bind_param("i", $type_id);
            $p->execute();
            $price = $p->get_result()->fetch_assoc()["price_per_night"];

            $nights = (strtotime($check_out) - strtotime($check_in)) / 86400;
            $total = $nights * $price;

            $g = $conn->prepare("INSERT INTO guests (full_name, email, phone) VALUES (?, ?, ?)");
            $g->bind_param("sss", $name, $email, $phone);
            $g->execute();
            $guest_id = $conn->insert_id;

           $cid = isset($_SESSION["customer_id"]) ? (int)$_SESSION["customer_id"] : null;
$cid = isset($_SESSION["customer_id"]) ? (int)$_SESSION["customer_id"] : null;
$b = $conn->prepare("INSERT INTO bookings (guest_id, room_id, check_in, check_out, total_amount, status, customer_id) VALUES (?, ?, ?, ?, ?, 'confirmed', ?)");
$b->bind_param("iissdi", $guest_id, $room["id"], $check_in, $check_out, $total, $cid);
            $b->execute();
            $booking_id = $conn->insert_id;

                        $_SESSION["pay_booking_id"] = $booking_id;
            $message = "Booking confirmed! Your Booking ID is #" . $booking_id .
                       ". Nights: " . $nights . ", Total: Rs. " . number_format($total) .
                       "<br><br><a href='payment.php?id=" . $booking_id . "' class='btn'>Pay Now</a>" .
                       " &nbsp; <small>(or pay at the hotel)</small>";
        }
    }
}

$selected = isset($_GET["type"]) ? (int)$_GET["type"] : 0;
$types = $conn->query("SELECT id, name, price_per_night FROM room_types");
?>

<h2 class="section-title">Book Your Stay</h2>

<div class="form-box">
  <?php if ($message) echo "<div class='msg-success'>$message</div>"; ?>
  <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>

  <form method="POST">
    <label>Full Name</label>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($_SESSION['customer_name'] ?? ''); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['customer_email'] ?? ''); ?>" required>
    <label>Phone</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($_SESSION['customer_phone'] ?? ''); ?>" required>

    <label>Room Type</label>
    <select name="room_type_id" required>
      <?php while ($t = $types->fetch_assoc()) { ?>
        <option value="<?php echo $t['id']; ?>" <?php if ($t['id'] == $selected) echo "selected"; ?>>
          <?php echo $t['name'] . " - Rs. " . number_format($t['price_per_night']); ?>
        </option>
      <?php } ?>
    </select>

    <label>Check-in Date</label>
    <input type="date" name="check_in" min="<?php echo date('Y-m-d'); ?>" required>

    <label>Check-out Date</label>
    <input type="date" name="check_out" min="<?php echo date('Y-m-d'); ?>" required>

    <button type="submit" class="btn">Confirm Booking</button>
  </form>
</div>

<?php include "includes/footer.php"; ?>