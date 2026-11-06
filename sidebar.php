<?php
$cur = basename($_SERVER["SCRIPT_NAME"]);
if ($cur == "food_edit.php") { $cur = "food_menu.php"; }

$menu = [
  ["dashboard.php",       "Dashboard"],
  ["bookings.php",        "Bookings"],
  ["rooms.php",           "Rooms"],
  ["guests.php",          "Guests"],
  ["food_orders.php",     "Food Orders"],
  ["food_menu.php",       "Food Menu"],
  ["messages.php",        "Messages"],
  ["reviews.php",         "Reviews"],
  ["reports.php",         "Reports"],
  ["change_password.php", "Change Password"],
];
?>
<div class="sidebar">
  <h2>Grand Stay</h2>
  <?php foreach ($menu as $m) { ?>
    <a href="<?php echo $m[0]; ?>" class="<?php echo $cur == $m[0] ? 'active' : ''; ?>"><?php echo $m[1]; ?></a>
  <?php } ?>
  <a href="logout.php">Logout</a>
</div>