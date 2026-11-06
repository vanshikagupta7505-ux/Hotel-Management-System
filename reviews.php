<?php
include "auth.php";
include "../includes/db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $action = $_POST["action"];

    if ($action == "toggle") {
        $stmt = $conn->prepare("UPDATE reviews SET visible = 1 - visible WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Review visibility updated.";
    }
    if ($action == "delete") {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Review deleted.";
    }
}

$reviews = $conn->query("SELECT * FROM reviews ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reviews - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Guest Reviews</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>

  <table>
    <tr><th>Guest</th><th>Rating</th><th>Review</th><th>Date</th><th>Status</th><th>Action</th></tr>
    <?php $n = 0; while ($r = $reviews->fetch_assoc()) { $n++; ?>
    <tr>
      <td><b><?php echo htmlspecialchars($r["name"]); ?></b><br><small style="opacity:0.7;">Booking #<?php echo $r["booking_id"]; ?></small></td>
      <td style="color:#d9bb7e; white-space:nowrap;"><?php echo str_repeat("★", $r["rating"]) . str_repeat("☆", 5 - $r["rating"]); ?></td>
      <td style="max-width:320px;"><?php echo nl2br(htmlspecialchars($r["comment"])); ?></td>
      <td><?php echo date("d M Y", strtotime($r["created_at"])); ?></td>
      <td>
        <?php if ($r["visible"]) { ?>
          <span class="badge confirmed">Visible</span>
        <?php } else { ?>
          <span class="badge cancelled">Hidden</span>
        <?php } ?>
      </td>
      <td>
        <form method="POST" class="inline">
          <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
          <input type="hidden" name="action" value="toggle">
          <button type="submit" class="act green"><?php echo $r["visible"] ? "Hide" : "Show"; ?></button>
        </form>
        <form method="POST" class="inline" onsubmit="return confirm('Delete this review?')">
          <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
          <input type="hidden" name="action" value="delete">
          <button type="submit" class="act red">Delete</button>
        </form>
      </td>
    </tr>
    <?php } ?>
    <?php if ($n == 0) { ?>
      <tr><td colspan="6" style="text-align:center; padding:30px;">No reviews yet.</td></tr>
    <?php } ?>
  </table>
</div>

</body>
</html>