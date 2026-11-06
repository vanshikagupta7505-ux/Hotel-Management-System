<?php
include "auth.php";
include "../includes/db.php";
include "upload_helper.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$f = $stmt->get_result()->fetch_assoc();

if (!$f) { die("Dish not found."); }

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            if ($img) {
                $u = $conn->prepare("UPDATE food_items SET name=?, category=?, description=?, price=?, image=? WHERE id=?");
                $u->bind_param("sssdsi", $name, $cat, $desc, $price, $img, $id);
            } else {
                $u = $conn->prepare("UPDATE food_items SET name=?, category=?, description=?, price=? WHERE id=?");
                $u->bind_param("sssdi", $name, $cat, $desc, $price, $id);
            }
            $u->execute();
            $msg = "Dish updated.";

            $stmt = $conn->prepare("SELECT * FROM food_items WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $f = $stmt->get_result()->fetch_assoc();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Dish - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php include "sidebar.php"; ?>
<div class="main">
  <h1>Edit Dish</h1>

  <?php if ($msg) echo "<div class='alert ok'>$msg</div>"; ?>
  <?php if ($err) echo "<div class='alert bad'>$err</div>"; ?>

  <form method="POST" enctype="multipart/form-data" class="add-room" style="flex-direction:column; max-width:480px;">
    <label>Dish name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($f["name"]); ?>" required>

    <label>Category</label>
    <input type="text" name="category" value="<?php echo htmlspecialchars($f["category"]); ?>" required>

    <label>Description</label>
    <input type="text" name="description" value="<?php echo htmlspecialchars($f["description"]); ?>">

    <label>Price (Rs.)</label>
    <input type="number" name="price" value="<?php echo (float)$f["price"]; ?>" min="1" step="1" required>

    <label>Current photo</label>
    <?php if ($f["image"]) { ?>
      <img src="../images/food/<?php echo htmlspecialchars($f["image"]); ?>" class="thumb big" alt="">
    <?php } else { echo "<p>No photo yet.</p>"; } ?>

    <label>Change photo (optional)</label>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">

    <div style="margin-top:12px;">
      <button type="submit" class="act green">Save Changes</button>
      <a href="food_menu.php" class="act blue">Back</a>
    </div>
  </form>
</div>

</body>
</html>