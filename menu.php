<?php
include "includes/db.php";
include "includes/header.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = (int)$_POST["booking_id"];
    $phone      = trim($_POST["phone"]);
    $qty        = isset($_POST["qty"]) ? $_POST["qty"] : [];

    $stmt = $conn->prepare("
        SELECT b.id, r.room_number
        FROM bookings b
        JOIN guests g ON b.guest_id = g.id
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ? AND g.phone = ? AND b.status = 'checked_in'
    ");
    $stmt->bind_param("is", $booking_id, $phone);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    if (!$booking) {
        $error = "Booking not found, or you are not checked-in yet. Please check your Booking ID and phone number.";
    } else {
        $lines = [];
        $total = 0;
        $q = $conn->prepare("SELECT id, price FROM food_items WHERE id = ? AND available = 1");

        foreach ($qty as $item_id => $n) {
            $item_id = (int)$item_id;
            $n = (int)$n;
            if ($n < 1) continue;
            if ($n > 20) $n = 20;
            $q->bind_param("i", $item_id);
            $q->execute();
            $item = $q->get_result()->fetch_assoc();
            if ($item) {
                $lines[] = [$item["id"], $n, $item["price"]];
                $total += $n * $item["price"];
            }
        }

        if (count($lines) == 0) {
            $error = "Please select at least one item.";
        } else {
            $conn->begin_transaction();
            $o = $conn->prepare("INSERT INTO food_orders (booking_id, total) VALUES (?, ?)");
            $o->bind_param("id", $booking_id, $total);
            $o->execute();
            $order_id = $conn->insert_id;

            $li = $conn->prepare("INSERT INTO food_order_items (order_id, item_id, qty, price) VALUES (?, ?, ?, ?)");
            foreach ($lines as $l) {
                $li->bind_param("iiid", $order_id, $l[0], $l[1], $l[2]);
                $li->execute();
            }
            $conn->commit();

            $message = "Order placed! Order ID #" . $order_id . ". Total: Rs. " . number_format($total) .
                       ". It will be delivered to Room " . htmlspecialchars($booking["room_number"]) . ".";
        }
    }
}

$items = $conn->query("SELECT * FROM food_items WHERE available = 1 ORDER BY category, name");
$menu = [];
while ($row = $items->fetch_assoc()) {
    $menu[$row["category"]][] = $row;
}
?>

<h2 class="section-title">Room Service Menu</h2>

<div class="menu-wrap">
  <?php if ($message) echo "<div class='msg-success'>$message</div>"; ?>
  <?php if ($error) echo "<div class='msg-error'>$error</div>"; ?>

  <form method="POST">
    <div class="order-who">
      <div>
        <label>Booking ID</label>
        <input type="number" name="booking_id" placeholder="e.g. 4" required>
      </div>
      <div>
        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="Phone used for booking" required>
      </div>
    </div>

    <?php foreach ($menu as $category => $list) { ?>
      <h3 class="menu-cat"><?php echo htmlspecialchars($category); ?></h3>
      <div class="menu-grid">
        <?php foreach ($list as $f) { ?>
                    <div class="menu-item">
            <div class="food-img" style="background-image: url('images/food/<?php echo htmlspecialchars($f['image']); ?>'), linear-gradient(135deg, #4f6b5a, #dfe8da);"></div>
            <div class="menu-info">
              <h4><?php echo htmlspecialchars($f["name"]); ?></h4>
              <p><?php echo htmlspecialchars($f["description"]); ?></p>
              <span class="menu-price">Rs. <?php echo number_format($f["price"]); ?></span>
            </div>
            <input type="number" name="qty[<?php echo $f['id']; ?>]" value="0" min="0" max="20" class="qty">
          </div>
        <?php } ?>
      </div>
    <?php } ?>

    <div style="text-align:center; margin:35px 0;">
      <button type="submit" class="btn">Place Order</button>
    </div>
  </form>
</div>

<?php include "includes/footer.php"; ?>