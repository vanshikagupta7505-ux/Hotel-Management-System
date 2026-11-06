<?php
include "auth.php";
include "../includes/db.php";

$id = (int)$_GET["id"];

$stmt = $conn->prepare("
    SELECT b.id, b.check_in, b.check_out, b.total_amount, b.status, b.created_at,
         b.payment_status, b.payment_method, b.transaction_id, b.paid_at,
         g.full_name, g.email, g.phone,
         r.room_number, t.name AS type_name, t.price_per_night
  FROM bookings b
  JOIN guests g ON b.guest_id = g.id
  JOIN rooms r ON b.room_id = r.id
  JOIN room_types t ON r.room_type_id = t.id
  WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

if (!$b) { die("Booking not found."); }

$nights = (strtotime($b["check_out"]) - strtotime($b["check_in"])) / 86400;
$fstmt = $conn->prepare("
  SELECT i.name, oi.qty, oi.price
  FROM food_orders o
  JOIN food_order_items oi ON oi.order_id = o.id
  JOIN food_items i ON oi.item_id = i.id
  WHERE o.booking_id = ? AND o.status != 'cancelled'
");
$fstmt->bind_param("i", $id);
$fstmt->execute();
$food = $fstmt->get_result();

$food_total = 0;
$food_rows = [];
while ($f = $food->fetch_assoc()) {
    $f["amount"] = $f["qty"] * $f["price"];
    $food_total += $f["amount"];
    $food_rows[] = $f;
}
$grand_total = $b["total_amount"] + $food_total;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice #<?php echo $b["id"]; ?></title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f2f4f7; padding: 30px; }
    .invoice { max-width: 750px; margin: auto; background: #fff; padding: 40px; border-radius: 10px; }
    .top { display: flex; justify-content: space-between; border-bottom: 3px solid #d4a94f; padding-bottom: 20px; }
    .top h1 { color: #1c2b3a; }
    .top p { color: #666; line-height: 1.6; }
    .info { display: flex; justify-content: space-between; margin: 25px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th { background: #1c2b3a; color: #fff; padding: 12px; text-align: left; }
    td { padding: 12px; border-bottom: 1px solid #eee; }
    .total { text-align: right; font-size: 22px; margin-top: 25px; color: #1c2b3a; }
    .thanks { text-align: center; margin-top: 40px; color: #888; }
    .btns { text-align: center; margin-top: 25px; }
    .btns button, .btns a { padding: 10px 22px; border: none; background: #d4a94f; color: #fff; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 15px; }
    @media print { .btns { display: none; } body { background: #fff; padding: 0; } }
  </style>
</head>
<body>
<div class="invoice">
  <div class="top">
    <div>
      <h1>Grand Stay Hotel</h1>
      <p>Bareilly, Uttar Pradesh<br>+91 98765 43210</p>
    </div>
    <div style="text-align:right;">
      <h2>INVOICE</h2>
      <p>Invoice #: <?php echo $b["id"]; ?><br>Date: <?php echo date("d M Y", strtotime($b["created_at"])); ?></p>
    </div>
  </div>

  <div class="info">
    <div>
      <b>Billed To</b>
      <p><?php echo htmlspecialchars($b["full_name"]); ?><br>
      <?php echo htmlspecialchars($b["phone"]); ?><br>
      <?php echo htmlspecialchars($b["email"]); ?></p>
    </div>
    <div style="text-align:right;">
      <b>Stay</b>
      <p>Check-in: <?php echo date("d M Y", strtotime($b["check_in"])); ?><br>
      Check-out: <?php echo date("d M Y", strtotime($b["check_out"])); ?></p>
    </div>
  </div>

  <table>
    <tr><th>Description</th><th>Room</th><th>Rate</th><th>Nights</th><th>Amount</th></tr>
    <tr>
      <td><?php echo $b["type_name"]; ?></td>
      <td><?php echo $b["room_number"]; ?></td>
      <td>Rs. <?php echo number_format($b["price_per_night"]); ?></td>
      <td><?php echo $nights; ?></td>
      <td>Rs. <?php echo number_format($b["total_amount"]); ?></td>
    </tr>
  </table>

  <?php if (count($food_rows) > 0) { ?>
  <h3 style="margin-top:25px;">Food & Room Service</h3>
  <table>
    <tr><th>Item</th><th>Qty</th><th>Rate</th><th>Amount</th></tr>
    <?php foreach ($food_rows as $f) { ?>
    <tr>
      <td><?php echo htmlspecialchars($f["name"]); ?></td>
      <td><?php echo $f["qty"]; ?></td>
      <td>Rs. <?php echo number_format($f["price"]); ?></td>
      <td>Rs. <?php echo number_format($f["amount"]); ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>

  <div class="total">
    Room: Rs. <?php echo number_format($b["total_amount"]); ?><br>
    Food: Rs. <?php echo number_format($food_total); ?><br>
    <b>Grand Total: Rs. <?php echo number_format($grand_total); ?></b>
  </div>
    <div style="margin-top:25px; padding:15px; border-radius:8px; background:<?php echo $b["payment_status"] == "paid" ? "#e1ecd9" : "#f8ecd0"; ?>;">
    <?php if ($b["payment_status"] == "paid") { ?>
      <b style="color:#3f5f3a;">ROOM CHARGES: PAID</b><br>
      Method: <?php echo htmlspecialchars($b["payment_method"]); ?>
      <?php if ($b["transaction_id"]) echo " | Transaction ID: " . htmlspecialchars($b["transaction_id"]); ?>
      <?php if ($b["paid_at"]) echo "<br>Paid on: " . date("d M Y, h:i A", strtotime($b["paid_at"])); ?>
    <?php } else { ?>
      <b style="color:#8a6d2f;">ROOM CHARGES: PAYMENT PENDING</b>
    <?php } ?>
    <br><small>Food and room service charges are settled at check-out.</small>
  </div>
  <p class="thanks">Thank you for staying with us!</p>

  <div class="btns">
    <button onclick="window.print()">Print Invoice</button>
    <a href="bookings.php">Back</a>
  </div>
</div>
</body>
</html>