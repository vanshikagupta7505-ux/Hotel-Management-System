<?php
include "auth.php";
include "../includes/db.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST["id"];
    $action = $_POST["action"];

    if ($action == "read") {
        $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Message marked as read.";
    }
    if ($action == "delete") {
        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Message deleted.";
    }
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Customer Messages</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>

  <table>
    <tr>
      <th>Status</th><th>From</th><th>Contact</th><th>Subject</th><th>Message</th><th>Date</th><th>Action</th>
    </tr>
    <?php while ($m = $messages->fetch_assoc()) { ?>
    <tr>
      <td>
        <?php if ($m["is_read"]) { ?>
          <span class="badge checked_out">Read</span>
        <?php } else { ?>
          <span class="badge pending">New</span>
        <?php } ?>
      </td>
      <td><b><?php echo htmlspecialchars($m["name"]); ?></b></td>
      <td>
        <?php echo htmlspecialchars($m["email"]); ?><br>
        <?php echo htmlspecialchars($m["phone"]); ?>
      </td>
      <td><?php echo htmlspecialchars($m["subject"]); ?></td>
      <td style="max-width:300px;"><?php echo nl2br(htmlspecialchars($m["message"])); ?></td>
      <td><?php echo date("d M, h:i A", strtotime($m["created_at"])); ?></td>
      <td>
        <?php if (!$m["is_read"]) { ?>
        <form method="POST" class="inline">
          <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
          <input type="hidden" name="action" value="read">
          <button type="submit" class="act green">Mark Read</button>
        </form>
        <?php } ?>
        <form method="POST" class="inline" onsubmit="return confirm('Delete this message?')">
          <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
          <input type="hidden" name="action" value="delete">
          <button type="submit" class="act red">Delete</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>