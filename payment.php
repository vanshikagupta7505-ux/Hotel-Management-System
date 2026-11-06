<?php
include "includes/db.php";

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conn->prepare("
  SELECT b.id, b.total_amount, b.status, b.check_in, b.check_out, b.customer_id,
         b.payment_status, r.room_number, t.name AS type_name
  FROM bookings b
  JOIN rooms r ON b.room_id = r.id
  JOIN room_types t ON r.room_type_id = t.id
  WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

/* Access: same browser jisne abhi booking ki, ya logged-in owner */
$allowed = false;
if ($b) {
    if (isset($_SESSION["pay_booking_id"]) && (int)$_SESSION["pay_booking_id"] === $id) { $allowed = true; }
    if (isset($_SESSION["customer_id"]) && $b["customer_id"] !== null
        && (int)$b["customer_id"] === (int)$_SESSION["customer_id"]) { $allowed = true; }
}

$error = "";
$done = false;
$txn = "";
$method_used = "";

if (!$b || !$allowed) {
    $error = "Booking not found, or you do not have access to it.";
} elseif ($b["status"] == "cancelled") {
    $error = "This booking is cancelled, so payment is not possible.";
} elseif ($b["payment_status"] == "paid") {
    $error = "This booking is already paid. Thank you!";
}

if (!$error && $_SERVER["REQUEST_METHOD"] == "POST") {
    $method = $_POST["method"] ?? "";

    if (!in_array($method, ["UPI", "Card", "Pay at Hotel"])) {
        $error = "Please choose a payment method.";
    } else {
        if ($method == "Pay at Hotel") {
            $u = $conn->prepare("UPDATE bookings SET payment_method = ? WHERE id = ?");
            $u->bind_param("si", $method, $id);
            $u->execute();
        } else {
            $txn = "TXN" . strtoupper(bin2hex(random_bytes(5)));
            $u = $conn->prepare("UPDATE bookings SET payment_status = 'paid', payment_method = ?, transaction_id = ?, paid_at = NOW() WHERE id = ?");
            $u->bind_param("ssi", $method, $txn, $id);
            $u->execute();
        }
        $done = true;
        $method_used = $method;
    }
}

include "includes/header.php";
?>

<h2 class="section-title">Secure Payment</h2>

<div class="pay-wrap">

<?php if ($done) { ?>

  <div class="form-box pay-success">
    <div class="tick">&#10004;</div>
    <?php if ($txn) { ?>
      <h3>Payment Successful</h3>
      <p>Rs. <?php echo number_format($b["total_amount"]); ?> paid via <b><?php echo htmlspecialchars($method_used); ?></b></p>
      <p>Transaction ID: <b><?php echo $txn; ?></b></p>
    <?php } else { ?>
      <h3>Booking Confirmed</h3>
      <p>Please pay <b>Rs. <?php echo number_format($b["total_amount"]); ?></b> at the reception during check-in.</p>
    <?php } ?>
    <p style="margin-top:20px;">
      <?php if (isset($_SESSION["customer_id"])) { ?>
        <a href="my_bookings.php" class="btn">My Bookings</a>
      <?php } else { ?>
        <a href="index.php" class="btn">Back to Home</a>
      <?php } ?>
    </p>
  </div>

<?php } elseif ($error) { ?>

  <div class="form-box">
    <div class="msg-error"><?php echo $error; ?></div>
    <p style="text-align:center;"><a href="index.php" class="btn">Back to Home</a></p>
  </div>

<?php } else { ?>

  <div class="form-box">
    <div class="demo-note">DEMO MODE: no real money is charged. Please do not enter real card details.</div>

    <div class="pay-summary">
      <div><span>Booking</span><b>#<?php echo $b["id"]; ?></b></div>
      <div><span>Room</span><b><?php echo htmlspecialchars($b["type_name"]); ?> (<?php echo htmlspecialchars($b["room_number"]); ?>)</b></div>
      <div><span>Stay</span><b><?php echo date("d M", strtotime($b["check_in"])); ?> &ndash; <?php echo date("d M Y", strtotime($b["check_out"])); ?></b></div>
      <div class="pay-total"><span>Amount to pay</span><b>Rs. <?php echo number_format($b["total_amount"]); ?></b></div>
    </div>

    <form method="POST" id="payForm">
      <input type="hidden" name="id" value="<?php echo $b["id"]; ?>">

      <p class="pay-heading">Choose payment method</p>
      <div class="method-list">
        <label class="method"><input type="radio" name="method" value="UPI" data-panel="panel-upi"><span>&#128241; UPI (GPay / PhonePe / Paytm)</span></label>
        <label class="method"><input type="radio" name="method" value="Card" data-panel="panel-card"><span>&#128179; Credit / Debit Card</span></label>
        <label class="method"><input type="radio" name="method" value="Pay at Hotel" data-panel="panel-hotel"><span>&#127976; Pay at Hotel</span></label>
      </div>

      <!-- Neeche ke boxes ko jaan-boojhkar name nahi diya, ye server ko bheje nahi jate -->
      <div class="pay-panel" id="panel-upi">
        <label>UPI ID</label>
        <input type="text" id="upi_id" placeholder="yourname@upi" autocomplete="off">
      </div>

      <div class="pay-panel" id="panel-card">
        <label>Card Number</label>
        <input type="text" id="card_no" placeholder="4111 1111 1111 1111" maxlength="23" autocomplete="off">
        <div class="card-row">
          <div><label>Expiry (MM/YY)</label><input type="text" id="card_exp" placeholder="08/29" maxlength="5" autocomplete="off"></div>
          <div><label>CVV</label><input type="password" id="card_cvv" placeholder="123" maxlength="4" autocomplete="off"></div>
        </div>
      </div>

      <div class="pay-panel" id="panel-hotel">
        <p class="pay-hint">Your booking stays confirmed. You can pay in cash or by card at the reception.</p>
      </div>

      <button type="submit" class="btn" id="payBtn">Pay Rs. <?php echo number_format($b["total_amount"]); ?></button>
    </form>
  </div>

  <script>
  var amount = "Rs. <?php echo number_format($b["total_amount"]); ?>";
  var btn = document.getElementById("payBtn");

  function showPanel() {
    var c = document.querySelector('input[name="method"]:checked');
    document.querySelectorAll(".pay-panel").forEach(function (p) { p.style.display = "none"; });
    if (c) {
      document.getElementById(c.dataset.panel).style.display = "block";
      btn.textContent = (c.value === "Pay at Hotel") ? "Confirm (Pay at Hotel)" : "Pay " + amount;
    }
  }
  document.querySelectorAll('input[name="method"]').forEach(function (r) {
    r.addEventListener("change", showPanel);
  });

  document.getElementById("payForm").addEventListener("submit", function (e) {
    var c = document.querySelector('input[name="method"]:checked');
    if (!c) { alert("Please choose a payment method."); e.preventDefault(); return; }

    if (c.value === "UPI") {
      var upi = document.getElementById("upi_id").value.trim();
      if (upi.indexOf("@") < 1) { alert("Enter a valid UPI ID (e.g. name@upi)."); e.preventDefault(); return; }
    }
    if (c.value === "Card") {
      var num = document.getElementById("card_no").value.replace(/\s/g, "");
      var exp = document.getElementById("card_exp").value.trim();
      var cvv = document.getElementById("card_cvv").value.trim();
      if (!/^\d{12,19}$/.test(num) || !/^\d{2}\/\d{2}$/.test(exp) || !/^\d{3,4}$/.test(cvv)) {
        alert("Please fill valid demo card details."); e.preventDefault(); return;
      }
    }
    btn.textContent = "Processing...";
    btn.disabled = true;
  });
  </script>

<?php } ?>

</div>

<?php include "includes/footer.php"; ?>