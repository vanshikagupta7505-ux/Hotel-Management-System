<?php
include "auth.php";
include "../includes/db.php";

$total_rooms   = $conn->query("SELECT COUNT(*) c FROM rooms")->fetch_assoc()["c"];
$available     = $conn->query("SELECT COUNT(*) c FROM rooms WHERE status='available'")->fetch_assoc()["c"];
$total_book    = $conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()["c"];
$today_in      = $conn->query("SELECT COUNT(*) c FROM bookings WHERE check_in = CURDATE() AND status IN ('confirmed','checked_in')")->fetch_assoc()["c"];
$pending_food = $conn->query("SELECT COUNT(*) c FROM food_orders WHERE status IN ('pending','preparing')")->fetch_assoc()["c"];
$new_msgs = $conn->query("SELECT COUNT(*) c FROM contact_messages WHERE is_read = 0")->fetch_assoc()["c"];
$unpaid = $conn->query("SELECT COUNT(*) c FROM bookings WHERE payment_status='unpaid' AND status != 'cancelled'")->fetch_assoc()["c"];
$revenue       = $conn->query("SELECT COALESCE(SUM(total_amount),0) s FROM bookings WHERE status != 'cancelled'")->fetch_assoc()["s"];

$recent = $conn->query("
  SELECT b.id, g.full_name, r.room_number, b.check_in, b.check_out, b.total_amount, b.status
  FROM bookings b
  JOIN guests g ON b.guest_id = g.id
  JOIN rooms r ON b.room_id = r.id
  ORDER BY b.id DESC LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION["admin_name"]); ?></h1>

  <div class="cards">
    <div class="stat"><h3><?php echo $total_rooms; ?></h3><p>Total Rooms</p></div>
    <div class="stat"><h3><?php echo $available; ?></h3><p>Available Rooms</p></div>
    <div class="stat"><h3><?php echo $total_book; ?></h3><p>Total Bookings</p></div>
    <div class="stat"><h3><?php echo $today_in; ?></h3><p>Today's Check-ins</p></div>
    <div class="stat"><h3><?php echo $pending_food; ?></h3><p>Pending Food Orders</p></div>
    <div class="stat"><h3><?php echo $unpaid; ?></h3><p>Unpaid Bookings</p></div>
    <div class="stat"><h3><?php echo $new_msgs; ?></h3><p>New Messages</p></div>
    <div class="stat"><h3>Rs. <?php echo number_format($revenue); ?></h3><p>Total Revenue</p></div>
  </div>

  <h2>Recent Bookings</h2>
  <table>
    <tr>
      <th>ID</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Amount</th><th>Status</th>
    </tr>
    <?php while ($b = $recent->fetch_assoc()) { ?>
    <tr>
      <td>#<?php echo $b["id"]; ?></td>
      <td><?php echo htmlspecialchars($b["full_name"]); ?></td>
      <td><?php echo $b["room_number"]; ?></td>
      <td><?php echo $b["check_in"]; ?></td>
      <td><?php echo $b["check_out"]; ?></td>
      <td>Rs. <?php echo number_format($b["total_amount"]); ?></td>
      <td><span class="badge <?php echo $b["status"]; ?>"><?php echo $b["status"]; ?></span></td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>