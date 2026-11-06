<?php
include "auth.php";
include "../includes/db.php";

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    try {
        if ($action == "add") {
            $number  = trim($_POST["room_number"]);
            $type_id = (int)$_POST["room_type_id"];
            $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type_id) VALUES (?, ?)");
            $stmt->bind_param("si", $number, $type_id);
            $stmt->execute();
            $msg = "Room " . htmlspecialchars($number) . " added.";
        }

        if ($action == "status") {
            $id = (int)$_POST["id"];
            $status = $_POST["status"];
            $ok = ["available", "occupied", "cleaning", "maintenance"];
            if (in_array($status, $ok)) {
                $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $id);
                $stmt->execute();
                $msg = "Room status updated.";
            }
        }

        if ($action == "delete") {
            $id = (int)$_POST["id"];
            $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $msg = "Room deleted.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($action == "add") {
            $err = "This room number already exists.";
        } elseif ($action == "delete") {
            $err = "This room has bookings, so it cannot be deleted. Set it to maintenance instead.";
        } else {
            $err = "Something went wrong.";
        }
    }
}

$types = $conn->query("SELECT id, name FROM room_types");
$rooms = $conn->query("
  SELECT r.id, r.room_number, r.status, t.name AS type_name, t.price_per_night
  FROM rooms r
  JOIN room_types t ON r.room_type_id = t.id
  ORDER BY r.room_number
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Rooms</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='alert bad'>$err</div>"; ?>

  <form method="POST" class="add-room">
    <input type="hidden" name="action" value="add">
    <input type="text" name="room_number" placeholder="Room number (e.g. 106)" required>
    <select name="room_type_id" required>
      <?php while ($t = $types->fetch_assoc()) { ?>
        <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
      <?php } ?>
    </select>
    <button type="submit" class="act green">+ Add Room</button>
  </form>

  <table>
    <tr><th>Room No.</th><th>Type</th><th>Price / night</th><th>Status</th><th>Action</th></tr>
    <?php while ($r = $rooms->fetch_assoc()) { ?>
    <tr>
      <td><b><?php echo htmlspecialchars($r["room_number"]); ?></b></td>
      <td><?php echo $r["type_name"]; ?></td>
      <td>Rs. <?php echo number_format($r["price_per_night"]); ?></td>
      <td>
        <form method="POST" class="inline">
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
          <select name="status" onchange="this.form.submit()">
            <?php foreach (["available","occupied","cleaning","maintenance"] as $s) { ?>
              <option value="<?php echo $s; ?>" <?php if ($r["status"] == $s) echo "selected"; ?>><?php echo ucfirst($s); ?></option>
            <?php } ?>
          </select>
        </form>
      </td>
      <td>
        <form method="POST" class="inline" onsubmit="return confirm('Delete this room?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
          <button type="submit" class="act red">Delete</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>