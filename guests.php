<?php
include "auth.php";
include "../includes/db.php";

$guests = $conn->query("
  SELECT g.id, g.full_name, g.email, g.phone, g.created_at,
         COUNT(b.id) AS total_bookings
  FROM guests g
  LEFT JOIN bookings b ON b.guest_id = g.id
  GROUP BY g.id
  ORDER BY g.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guests - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Guests</h1>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Bookings</th><th>Registered</th></tr>
    <?php while ($g = $guests->fetch_assoc()) { ?>
    <tr>
      <td>#<?php echo $g["id"]; ?></td>
      <td><?php echo htmlspecialchars($g["full_name"]); ?></td>
      <td><?php echo htmlspecialchars($g["email"]); ?></td>
      <td><?php echo htmlspecialchars($g["phone"]); ?></td>
      <td><?php echo $g["total_bookings"]; ?></td>
      <td><?php echo date("d M Y", strtotime($g["created_at"])); ?></td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>