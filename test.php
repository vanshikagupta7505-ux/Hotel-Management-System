<?php
include "includes/db.php";
$result = $conn->query("SELECT * FROM room_types");
while ($row = $result->fetch_assoc()) {
    echo $row['name'] . " - Rs. " . $row['price_per_night'] . "<br>";
}
?>