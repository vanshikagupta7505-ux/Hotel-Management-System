<?php
include "auth.php";
include "../includes/db.php";
include "upload_helper.php";

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    try {
        if ($action == "add") {
            $name  = trim($_POST["name"]);
            $cat   = trim($_POST["category"]);
            $desc  = trim($_POST["description"]);
            $price = (float)$_POST["price"];

            if ($name == "" || $cat == "" || $price <= 0) {
                $err = "Please enter name, category and a valid price.";
            } else {
                list($img, $uerr) = upload_food_image("image");
                if ($uerr) {
                    $err = $uerr;
                } else {
                    $stmt = $conn->prepare("INSERT INTO food_items (name, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssds", $name, $cat, $desc, $price, $img);
                    $stmt->execute();
                    $msg = htmlspecialchars($name) . " added to the menu.";
                }
            }
        }

        if ($action == "toggle") {
            $id = (int)$_POST["id"];
            $stmt = $conn->prepare("UPDATE food_items SET available = 1 - available WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $msg = "Item visibility updated.";
        }

        if ($action == "delete") {
            $id = (int)$_POST["id"];
            $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $msg = "Item deleted.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($action == "delete") {
            $err = "This dish is part of past orders, so it cannot be deleted. Use Hide instead.";
        } else {
            $err = "Something went wrong. Please try again.";
        }
    }
}

$items = $conn->query("SELECT * FROM food_items ORDER BY category, name");
$cats  = $conn->query("SELECT DISTINCT category FROM food_items ORDER BY category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Food Menu - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<div class="main">
  <h1>Food Menu</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='alert bad'>$err</div>"; ?>

  <form method="POST" enctype="multipart/form-data" class="add-room">
    <input type="hidden" name="action" value="add">
    <input type="text" name="name" placeholder="Dish name" required>
    <input type="text" name="category" list="cat-list" placeholder="Category (e.g. Snacks)" required>
    <datalist id="cat-list">
      <?php while ($c = $cats->fetch_assoc()) { ?>
        <option value="<?php echo htmlspecialchars($c["category"]); ?>">
      <?php } ?>
    </datalist>
    <input type="text" name="description" placeholder="Short description" style="min-width:230px;">
    <input type="number" name="price" placeholder="Price (Rs.)" min="1" step="1" required style="width:130px;">
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
    <button type="submit" class="act green">+ Add Dish</button>
  </form>

  <table>
    <tr>
      <th>Photo</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Action</th>
    </tr>
    <?php while ($f = $items->fetch_assoc()) { ?>
    <tr>
      <td>
        <?php if ($f["image"]) { ?>
          <img src="../images/food/<?php echo htmlspecialchars($f["image"]); ?>" class="thumb" alt="">
        <?php } else { echo "-"; } ?>
      </td>
      <td><b><?php echo htmlspecialchars($f["name"]); ?></b><br>
        <small style="opacity:0.7;"><?php echo htmlspecialchars($f["description"]); ?></small></td>
      <td><?php echo htmlspecialchars($f["category"]); ?></td>
      <td>Rs. <?php echo number_format($f["price"]); ?></td>
      <td>
        <?php if ($f["available"]) { ?>
          <span class="badge confirmed">Visible</span>
        <?php } else { ?>
          <span class="badge cancelled">Hidden</span>
        <?php } ?>
      </td>
      <td>
        <a class="act blue" href="food_edit.php?id=<?php echo $f['id']; ?>">Edit</a>

        <form method="POST" class="inline">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
          <button type="submit" class="act green"><?php echo $f["available"] ? "Hide" : "Show"; ?></button>
        </form>

        <form method="POST" class="inline" onsubmit="return confirm('Delete this dish?')">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
          <button type="submit" class="act red">Delete</button>
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
</div>

</body>
</html>