<?php
include "includes/db.php";
include "includes/header.php";
$types = $conn->query("SELECT * FROM room_types");
$all_types = $conn->query("SELECT id, name FROM room_types");
?>

<!-- HERO + SEARCH -->
<?php
$slide_candidates = [
  "images/hero.jpg",
  "images/gallery/g3.jpg",
  "images/gallery/g1.jpg",
  "images/gallery/g6.jpg",
];
$slides = [];
foreach ($slide_candidates as $s) {
    if (file_exists(__DIR__ . "/" . $s)) { $slides[] = $s; }
}
?>
<section class="hero">
  <?php foreach ($slides as $i => $s) { ?>
    <div class="hero-slide <?php echo $i == 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo $s; ?>');"></div>
  <?php } ?>
  <div class="hero-overlay"></div>

  <div class="hero-content">
    <p class="hero-tag">&#9733; &#9733; &#9733; &#9733; &#9733; &nbsp; Comfort Stay in Bareilly</p>
    <h1>Welcome to Grand Stay Hotel</h1>
    <p class="hero-sub">Comfort, luxury and care in the heart of Bareilly</p>
    <div class="hero-btns">
      <a href="booking.php" class="btn">Book Your Stay</a>
      <a href="rooms.php" class="btn btn-outline">Explore Rooms</a>
    </div>
  </div>

  <?php if (count($slides) > 1) { ?>
    <div class="hero-dots">
      <?php foreach ($slides as $i => $s) { ?><span class="<?php echo $i == 0 ? 'on' : ''; ?>"></span><?php } ?>
    </div>
  <?php } ?>
</section>

<script>
(function () {
  var slides = document.querySelectorAll(".hero-slide");
  var dots = document.querySelectorAll(".hero-dots span");
  if (slides.length < 2) return;
  var cur = 0, timer;

  function go(n) {
    slides[cur].classList.remove("active");
    dots[cur].classList.remove("on");
    cur = (n + slides.length) % slides.length;
    slides[cur].classList.add("active");
    dots[cur].classList.add("on");
  }
  function start() { timer = setInterval(function () { go(cur + 1); }, 5000); }

  dots.forEach(function (d, i) {
    d.addEventListener("click", function () { clearInterval(timer); go(i); start(); });
  });
  start();
})();
</script>

<!-- ABOUT -->
<section class="about">
  <div>
    <h2>A Stay You Will Remember</h2>
    <p>Grand Stay Hotel offers clean, comfortable and elegant rooms with warm hospitality. Whether you are here for business, family time or a short getaway, we make sure you feel at home.</p>
    <p>Enjoy modern amenities, delicious food and 24x7 service, all at fair prices.</p>
    <a href="rooms.php" class="btn">Explore Rooms</a>
  </div>
  <div class="about-img"></div>
</section>

<!-- ROOMS -->
<h2 class="section-title">Our Rooms</h2>
<div class="room-grid">
  <?php while ($row = $types->fetch_assoc()) { ?>
    <div class="room-card">
      <div class="room-img" style="background-image: url('images/<?php echo $row['image']; ?>'), linear-gradient(135deg, #2c3e50, #d4a94f);"></div>
      <div class="room-info">
        <h3><?php echo $row['name']; ?></h3>
        <p><?php echo $row['description']; ?></p>
        <p>Max guests: <?php echo $row['max_guests']; ?></p>
        <div class="price">Rs. <?php echo number_format($row['price_per_night']); ?> / night</div>
        <a href="booking.php?type=<?php echo $row['id']; ?>" class="btn">Book Now</a>
      </div>
    </div>
  <?php } ?>
</div>
<!-- OFFERS -->
<h2 class="section-title">Special Offers</h2>
<div class="offer-grid">
  <div class="offer-card">
    <span class="offer-badge">20% OFF</span>
    <div class="offer-icon">&#127796;</div>
    <h3>Weekend Getaway</h3>
    <p>Stay Friday to Sunday and enjoy a complimentary breakfast for two.</p>
    <a href="booking.php" class="btn">Book This Offer</a>
  </div>

  <div class="offer-card featured">
    <span class="offer-badge">BEST VALUE</span>
    <div class="offer-icon">&#128149;</div>
    <h3>Honeymoon Package</h3>
    <p>Suite stay with candle-light dinner, flower decoration and late check-out.</p>
    <a href="booking.php?type=3" class="btn">Book This Offer</a>
  </div>

  <div class="offer-card">
    <span class="offer-badge">15% OFF</span>
    <div class="offer-icon">&#128106;</div>
    <h3>Long Stay Saver</h3>
    <p>Stay 4 nights or more and save on your total room charges.</p>
    <a href="booking.php" class="btn">Book This Offer</a>
  </div>
</div>

<!-- FACILITIES -->
<section class="facilities">
  <h2 class="section-title">Our Facilities</h2>
  <div class="fac-grid">
    <div class="fac-item"><div class="icon">📶</div>Free WiFi</div>
    <div class="fac-item"><div class="icon">🍽️</div>Restaurant</div>
    <div class="fac-item"><div class="icon">🚗</div>Free Parking</div>
    <div class="fac-item"><div class="icon">❄️</div>Air Conditioning</div>
    <div class="fac-item"><div class="icon">🛎️</div>24x7 Room Service</div>
    <div class="fac-item"><div class="icon">🔒</div>Safe & Secure</div>
  </div>
</section>

<!-- REVIEWS -->
<?php
$revs = $conn->query("SELECT name, rating, comment FROM reviews WHERE visible = 1 ORDER BY id DESC LIMIT 6");
$review_list = [];
while ($rv = $revs->fetch_assoc()) { $review_list[] = $rv; }

if (count($review_list) == 0) {
    $review_list = [
      ["name" => "Rahul Sharma", "rating" => 5, "comment" => "Very clean rooms and polite staff. Booking was quick and easy."],
      ["name" => "Priya Singh",  "rating" => 5, "comment" => "Great value for money. The deluxe room was spacious and comfortable."],
      ["name" => "Amit Verma",   "rating" => 4, "comment" => "Lovely stay with my family. Food was tasty and service was excellent."],
    ];
}
?>
<h2 class="section-title">What Our Guests Say</h2>
<div class="review-grid">
  <?php foreach ($review_list as $rv) { ?>
    <div class="review">
      <div class="stars"><?php echo str_repeat("★", $rv["rating"]) . str_repeat("☆", 5 - $rv["rating"]); ?></div>
      <p><?php echo htmlspecialchars($rv["comment"]); ?></p>
      <h4><?php echo htmlspecialchars($rv["name"]); ?></h4>
    </div>
  <?php } ?>
</div>

<?php include "includes/footer.php"; ?>