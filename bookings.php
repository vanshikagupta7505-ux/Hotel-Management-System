<?php
include "auth.php";
include "../includes/db.php";

$filter = isset($_GET["status"]) ? $_GET["status"] : "";
$allowed = ["pending","confirmed","checked_in","checked_out","cancelled"];

$sql = "
  SELECT b.id, g.full_name, g.phone, r.room_number, b.check_in, b.check_out,
         b.total_amount, b.status, b.payment_status, b.payment_method
  FROM bookings b
  JOIN guests g ON b.guest_id = g.id
  JOIN rooms r ON b.room_id = r.id
";

if (in_array($filter, $allowed)) {
    $stmt = $conn->prepare($sql . " WHERE b.status = ? ORDER BY b.id DESC");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $bookings = $stmt->get_result();
} else {
    $bookings = $conn->query($sql . " ORDER BY b.id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookings - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Bookings</h1>

  <div class="filters">
    <a href="bookings.php" class="<?php echo $filter == '' ? 'on' : ''; ?>">All</a>
    <?php foreach ($allowed as $s) { ?>
      <a href="bookings.php?status=<?php echo $s; ?>" class="<?php echo $filter == $s ? 'on' : ''; ?>">
        <?php echo ucwords(str_replace("_", " ", $s)); ?>
      </a>
    <?php } ?>
  </div>

  <table>
    <tr>
      <th>ID</th>
      <th>Guest</th>
      <th>Phone</th>
      <th>Room</th>
      <th>Check-in</th>
      <th>Check-out</th>
      <th>Amount</th>
      <th>Status</th>
      <th>Payment</th>
      <th>Action</th>
    </tr>

    <?php while ($b = $bookings->fetch_assoc()) { ?>
    <tr>
      <td>#<?php echo $b["id"]; ?></td>
      <td><?php echo htmlspecialchars($b["full_name"]); ?></td>
      <td><?php echo htmlspecialchars($b["phone"]); ?></td>
      <td><?php echo $b["room_number"]; ?></td>
      <td><?php echo $b["check_in"]; ?></td>
      <td><?php echo $b["check_out"]; ?></td>
      <td>Rs. <?php echo number_format($b["total_amount"]); ?></td>

      <td>
        <span class="badge <?php echo $b["status"]; ?>"><?php echo str_replace("_", " ", $b["status"]); ?></span>
      </td>

      <td>
        <?php if ($b["status"] == "cancelled") { ?>
          -
        <?php } elseif ($b["payment_status"] == "paid") { ?>
          <span class="badge confirmed">Paid</span>
          <br><small style="opacity:0.7;"><?php echo htmlspecialchars($b["payment_method"]); ?></small>
        <?php } else { ?>
          <span class="badge pending">Unpaid</span>
          <?php if ($b["payment_method"]) { ?>
            <br><small style="opacity:0.7;"><?php echo htmlspecialchars($b["payment_method"]); ?></small>
          <?php } ?>
        <?php } ?>
      </td>

      <td class="actions">
        <?php if ($b["status"] == "confirmed" || $b["status"] == "pending") { ?>
          <a class="act green" href="booking_action.php?id=<?php echo $b['id']; ?>&do=checkin">Check-in</a>
          <a class="act red" href="booking_action.php?id=<?php echo $b['id']; ?>&do=cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
        <?php } elseif ($b["status"] == "checked_in") { ?>
          <a class="act blue" href="booking_action.php?id=<?php echo $b['id']; ?>&do=checkout">Check-out</a>
        <?php } ?>

        <a class="act blue" href="invoice.php?id=<?php echo $b['id']; ?>">Invoice</a>

        <?php if ($b["payment_status"] != "paid" && $b["status"] != "cancelled") { ?>
          <a class="act green" href="booking_action.php?id=<?php echo $b['id']; ?>&do=markpaid" onclick="return confirm('Mark this booking as paid?')">Mark Paid</a>
        <?php } ?>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>