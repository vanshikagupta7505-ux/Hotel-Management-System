<?php
include "includes/db.php";
include "includes/header.php";

$photos = [
  ["g1.jpg", "Hotel Lobby"],
  ["g2.jpg", "Restaurant"],
  ["g3.jpg", "Swimming Pool"],
  ["g4.jpg", "Spa & Wellness"],
  ["g5.jpg", "Garden"],
  ["g6.jpg", "Room Interior"],
];
?>

<h2 class="section-title">Our Gallery</h2>

<!-- 3D CAROUSEL -->
<div class="scene" id="scene">
  <div class="carousel" id="carousel">
    <?php foreach ($photos as $p) { ?>
      <div class="c-item"
           onclick="openBox('images/gallery/<?php echo $p[0]; ?>')"
           style="background-image: url('images/gallery/<?php echo $p[0]; ?>'), linear-gradient(135deg, #4f6b5a, #dfe8da);">
        <span><?php echo $p[1]; ?></span>
      </div>
    <?php } ?>
  </div>
</div>

<div class="c-controls">
  <button class="btn" onclick="rotateTo(-1)">&larr; Previous</button>
  <button class="btn" onclick="rotateTo(1)">Next &rarr;</button>
</div>

<!-- LIGHTBOX -->
<div id="lightbox" onclick="closeBox()">
  <span class="close-x">&times;</span>
  <img id="lightbox-img" src="" alt="">
</div>

<script>
var carousel = document.getElementById("carousel");
var items = carousel.querySelectorAll(".c-item");
var n = items.length;
var angle = 0;
var step = 360 / n;
var itemWidth = 320;
var radius = Math.round((itemWidth / 2) / Math.tan(Math.PI / n)) + 40;

for (var i = 0; i < n; i++) {
  items[i].style.transform = "rotateY(" + (i * step) + "deg) translateZ(" + radius + "px)";
}

function update() {
  carousel.style.transform = "translateZ(-" + radius + "px) rotateY(" + angle + "deg)";
}

function rotateTo(dir) {
  angle -= dir * step;
  update();
}

update();

var auto = setInterval(function () { rotateTo(1); }, 3000);
var scene = document.getElementById("scene");
scene.addEventListener("mouseenter", function () { clearInterval(auto); });
scene.addEventListener("mouseleave", function () {
  auto = setInterval(function () { rotateTo(1); }, 3000);
});

function openBox(src) {
  document.getElementById("lightbox-img").src = src;
  document.getElementById("lightbox").style.display = "flex";
}
function closeBox() {
  document.getElementById("lightbox").style.display = "none";
}
</script>

<?php include "includes/footer.php"; ?>