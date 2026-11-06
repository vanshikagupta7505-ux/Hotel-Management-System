<?php
include "auth.php";
include "../includes/db.php";

/* ---------- Summary cards ---------- */
$room_rev  = $conn->query("SELECT COALESCE(SUM(total_amount),0) s FROM bookings WHERE status != 'cancelled'")->fetch_assoc()["s"];
$food_rev  = $conn->query("SELECT COALESCE(SUM(total),0) s FROM food_orders WHERE status != 'cancelled'")->fetch_assoc()["s"];
$total_book = $conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()["c"];
$total_rooms = $conn->query("SELECT COUNT(*) c FROM rooms")->fetch_assoc()["c"];
$occupied    = $conn->query("SELECT COUNT(*) c FROM rooms WHERE status = 'occupied'")->fetch_assoc()["c"];
$occ_percent = $total_rooms > 0 ? round(($occupied / $total_rooms) * 100) : 0;

/* ---------- Last 6 months revenue ---------- */
$months = [];
$labels = [];
for ($i = 5; $i >= 0; $i--) {
    $key = date("Y-m", strtotime("-$i month", strtotime(date("Y-m-01"))));
    $months[$key] = ["room" => 0, "food" => 0];
    $labels[] = date("M Y", strtotime($key . "-01"));
}

$r = $conn->query("
  SELECT DATE_FORMAT(check_in, '%Y-%m') ym, SUM(total_amount) s
  FROM bookings WHERE status != 'cancelled'
  GROUP BY ym
");
while ($row = $r->fetch_assoc()) {
    if (isset($months[$row["ym"]])) $months[$row["ym"]]["room"] = (float)$row["s"];
}

$f = $conn->query("
  SELECT DATE_FORMAT(created_at, '%Y-%m') ym, SUM(total) s
  FROM food_orders WHERE status != 'cancelled'
  GROUP BY ym
");
while ($row = $f->fetch_assoc()) {
    if (isset($months[$row["ym"]])) $months[$row["ym"]]["food"] = (float)$row["s"];
}

$room_data = array_column($months, "room");
$food_data = array_column($months, "food");

/* ---------- Booking status ---------- */
$status_labels = [];
$status_data = [];
$s = $conn->query("SELECT status, COUNT(*) c FROM bookings GROUP BY status");
while ($row = $s->fetch_assoc()) {
    $status_labels[] = ucwords(str_replace("_", " ", $row["status"]));
    $status_data[] = (int)$row["c"];
}

/* ---------- Revenue by room type ---------- */
$type_labels = [];
$type_data = [];
$t = $conn->query("
  SELECT t.name, COALESCE(SUM(b.total_amount),0) s
  FROM room_types t
  LEFT JOIN rooms r ON r.room_type_id = t.id
  LEFT JOIN bookings b ON b.room_id = r.id AND b.status != 'cancelled'
  GROUP BY t.id
");
while ($row = $t->fetch_assoc()) {
    $type_labels[] = $row["name"];
    $type_data[] = (float)$row["s"];
}

/* ---------- Rooms status now ---------- */
$rs_labels = [];
$rs_data = [];
$q = $conn->query("SELECT status, COUNT(*) c FROM rooms GROUP BY status");
while ($row = $q->fetch_assoc()) {
    $rs_labels[] = ucfirst($row["status"]);
    $rs_data[] = (int)$row["c"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - Grand Stay</title>
  <link rel="stylesheet" href="../css/admin.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>

<?php include "sidebar.php"; ?>

<div class="main">
  <h1>Reports</h1>

  <div class="cards">
    <div class="stat"><h3>Rs. <?php echo number_format($room_rev); ?></h3><p>Room Revenue</p></div>
    <div class="stat"><h3>Rs. <?php echo number_format($food_rev); ?></h3><p>Food Revenue</p></div>
    <div class="stat"><h3>Rs. <?php echo number_format($room_rev + $food_rev); ?></h3><p>Total Income</p></div>
    <div class="stat"><h3><?php echo $total_book; ?></h3><p>Total Bookings</p></div>
    <div class="stat"><h3><?php echo $occ_percent; ?>%</h3><p>Occupancy Now</p></div>
  </div>

  <div class="chart-grid">
    <div class="chart-box wide">
      <h3>Monthly Revenue (last 6 months)</h3>
      <div class="chart-holder"><canvas id="revChart"></canvas></div>
    </div>

    <div class="chart-box">
      <h3>Booking Status</h3>
      <div class="chart-holder"><canvas id="statusChart"></canvas></div>
    </div>

    <div class="chart-box">
      <h3>Revenue by Room Type</h3>
      <div class="chart-holder"><canvas id="typeChart"></canvas></div>
    </div>

    <div class="chart-box">
      <h3>Rooms Right Now</h3>
      <div class="chart-holder"><canvas id="roomChart"></canvas></div>
    </div>
  </div>
</div>

<script>
Chart.defaults.color = "#cdd8cf";
Chart.defaults.borderColor = "rgba(255,255,255,0.08)";
Chart.defaults.font.family = "Segoe UI, Arial, sans-serif";

var palette = ["#d9bb7e", "#4f8f67", "#5f86a3", "#b8685b", "#8a9a7b", "#a99bc7"];

new Chart(document.getElementById("revChart"), {
  type: "bar",
  data: {
    labels: <?php echo json_encode($labels); ?>,
    datasets: [
      { label: "Room", data: <?php echo json_encode($room_data); ?>, backgroundColor: "#d9bb7e", borderRadius: 6 },
      { label: "Food", data: <?php echo json_encode($food_data); ?>, backgroundColor: "#4f8f67", borderRadius: 6 }
    ]
  },
  options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById("statusChart"), {
  type: "doughnut",
  data: {
    labels: <?php echo json_encode($status_labels); ?>,
    datasets: [{ data: <?php echo json_encode($status_data); ?>, backgroundColor: palette, borderWidth: 0 }]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "bottom" } } }
});

new Chart(document.getElementById("typeChart"), {
  type: "bar",
  data: {
    labels: <?php echo json_encode($type_labels); ?>,
    datasets: [{ label: "Revenue (Rs.)", data: <?php echo json_encode($type_data); ?>, backgroundColor: palette, borderRadius: 6 }]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById("roomChart"), {
  type: "doughnut",
  data: {
    labels: <?php echo json_encode($rs_labels); ?>,
    datasets: [{ data: <?php echo json_encode($rs_data); ?>, backgroundColor: palette, borderWidth: 0 }]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: "bottom" } } }
});
</script>

</body>
</html>