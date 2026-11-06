<?php
include "auth.php";
include "../includes/db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $status = $_POST["status"];
    $ok = ["pending", "preparing", "delivered", "cancelled"];
    if (in_array($status, $ok)) {
        $stmt = $conn->prepare("UPDATE food_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $msg = "Order #" . $id . " updated to " . $status . ".";
    }
}

$filter = isset($_GET["status"]) ? $_GET["status"] : "";
$allowed = ["pending", "preparing", "delivered", "cancelled"];

$sql = "
  SELECT o.id, o.total, o.status, o.created_at, o.booking_id,
         g.full_name, r.room_number
  FROM food_orders o
  JOIN bookings b ON o.booking_id = b.id
  JOIN guests g ON b.guest_id = g.id
  JOIN rooms r ON b.room_id = r.id
";

if (in_array($filter, $allowed)) {
    $stmt = $conn->prepare($sql . " WHERE o.status = ? ORDER BY o.id DESC");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $orders = $conn->query($sql . " ORDER BY o.id DESC");
}

function order_items($conn, $order_id) {
    $stmt = $conn->prepare("
      SELECT i.name, oi.qty
      FROM food_order_items oi
      JOIN food_items i ON oi.item_id = i.id
      WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $parts = [];
    while ($r = $res->fetch_assoc()) {
        $parts[] = htmlspecialchars($r["name"]) . " x " . $r["qty"];
    }
    return implode("<br>", $parts);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Orders - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<div class="main">
  <h1>Food Orders</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>

  <div class="filters">
    <a href="food_orders.php" class="<?php echo $filter == '' ? 'on' : ''; ?>">All</a>
    <?php foreach ($allowed as $s) { ?>
      <a href="food_orders.php?status=<?php echo $s; ?>" class="<?php echo $filter == $s ? 'on' : ''; ?>"><?php echo ucfirst($s); ?></a>
    <?php } ?>
  </div>

  <table>
    <tr>
      <th>Order</th>
      <th>Room</th>
      <th>Guest</th>
      <th>Items</th>
      <th>Total</th>
      <th>Time</th>
      <th>Status</th>
    </tr>
    <?php while ($o = $orders->fetch_assoc()) { ?>
    <tr>
      <td>#<?php echo $o["id"]; ?></td>
      <td><b><?php echo $o["room_number"]; ?></b></td>
      <td><?php echo htmlspecialchars($o["full_name"]); ?></td>
      <td><?php echo order_items($conn, $o["id"]); ?></td>
      <td>Rs. <?php echo number_format($o["total"]); ?></td>
      <td><?php echo date("d M, h:i A", strtotime($o["created_at"])); ?></td>
      <td>
        <form method="POST" class="inline">
          <input type="hidden" name="id" value="<?php echo $o['id']; ?>">
          <select name="status" onchange="this.form.submit()">
            <?php foreach ($allowed as $s) { ?>
              <option value="<?php echo $s; ?>" <?php if ($o["status"] == $s) echo "selected"; ?>><?php echo ucfirst($s); ?></option>
            <?php } ?>
          </select>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>