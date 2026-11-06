<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grand Stay Hotel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="logo">Grand Stay</a>
    <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="rooms.php">Rooms</a></li>
    <li><a href="menu.php">Food Menu</a></li>
    <li><a href="gallery.php">Gallery</a></li>
    <li><a href="contact.php">Contact</a></li>
    <?php if (isset($_SESSION["customer_id"])) { ?>
      <li><a href="my_bookings.php">My Bookings</a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php } else { ?>
      <li><a href="login.php">Login</a></li>
    <?php } ?>
    <li><a href="booking.php" class="nav-btn">Book Now</a></li>
    <li><a href="admin/login.php">Admin</a></li>
  </ul>
</nav>