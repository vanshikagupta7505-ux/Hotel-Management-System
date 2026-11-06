<?php
include "auth.php";
include "../includes/db.php";

$id = (int)$_GET["id"];
$do = $_GET["do"];

$stmt = $conn->prepare("SELECT room_id FROM bookings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if ($booking) {
    $room_id = $booking["room_id"];

    if ($do == "checkin") {
        $conn->query("UPDATE bookings SET status='checked_in' WHERE id=$id");
        $conn->query("UPDATE rooms SET status='occupied' WHERE id=$room_id");
    } elseif ($do == "checkout") {
        $conn->query("UPDATE bookings SET status='checked_out' WHERE id=$id");
        $conn->query("UPDATE rooms SET status='cleaning' WHERE id=$room_id");
        } elseif ($do == "cancel") {
        $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$id");
    } elseif ($do == "markpaid") {
        $conn->query("UPDATE bookings SET payment_status='paid', payment_method=COALESCE(payment_method,'Cash at Hotel'), paid_at=NOW() WHERE id=$id AND status != 'cancelled'");
    }
}

header("Location: bookings.php");
exit;
?>