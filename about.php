<?php
include "includes/db.php";
include "includes/header.php";
?>

<section class="page-banner">
  <h1>About Grand Stay</h1>
  <p>A warm welcome, every single time</p>
</section>

<section class="about-story">
  <div class="story-text">
    <h2>Our Story</h2>
    <p>Grand Stay Hotel was started with one simple idea: every guest should feel at home, whether they are here for a day or a month. Located in the heart of Bareilly, we bring together comfortable rooms, tasty food and friendly service.</p>
    <p>From business travellers to families on a holiday, our team works around the clock to make each stay clean, peaceful and memorable.</p>
    <a href="booking.php" class="btn">Book Your Stay</a>
  </div>
  <div class="story-img"></div>
</section>

<h2 class="section-title">Why Guests Choose Us</h2>
<div class="value-grid">
  <div class="value-card"><div class="v-icon">&#10024;</div><h3>Spotless Rooms</h3><p>Cleaned and freshened every day.</p></div>
  <div class="value-card"><div class="v-icon">&#127869;</div><h3>Room Service</h3><p>Fresh, hot food at your door.</p></div>
  <div class="value-card"><div class="v-icon">&#128176;</div><h3>Fair Prices</h3><p>No hidden charges, ever.</p></div>
  <div class="value-card"><div class="v-icon">&#128737;</div><h3>Safe &amp; Secure</h3><p>24x7 reception and security.</p></div>
</div>

<h2 class="section-title">Frequently Asked Questions</h2>
<div class="faq-wrap">

  <details class="faq-item">
    <summary>What are the check-in and check-out times?</summary>
    <p>Check-in starts at 12 PM and check-out is by 11 AM. Our reception is open 24x7, so you can reach us any time.</p>
  </details>

  <details class="faq-item">
    <summary>How do I book a room?</summary>
    <p>Choose a room on the Rooms page, click Book Now, fill in your details and dates, and confirm. You can also create an account to see all your bookings in one place.</p>
  </details>

  <details class="faq-item">
    <summary>Can I cancel my booking?</summary>
    <p>Yes. Log in, open My Bookings and press Cancel on any upcoming booking. Cancellation is possible before the check-in date. On or after that day, please contact the hotel.</p>
  </details>

  <details class="faq-item">
    <summary>What payment options are available?</summary>
    <p>You can pay online by UPI or card, or choose Pay at Hotel and settle the bill at the reception during check-in.</p>
  </details>

  <details class="faq-item">
    <summary>How do I order food to my room?</summary>
    <p>Once you are checked in, open the Food Menu, enter your Booking ID and the phone number used for booking, pick your dishes and place the order. It will be delivered to your room.</p>
  </details>

  <details class="faq-item">
    <summary>Do I need an ID proof at check-in?</summary>
    <p>Yes, every guest must show a valid government photo ID (Aadhaar, Driving Licence, Passport or Voter ID) at the reception.</p>
  </details>

  <details class="faq-item">
    <summary>Is WiFi and parking available?</summary>
    <p>Yes. Free WiFi is available in all rooms and public areas, and free parking is available for our guests.</p>
  </details>

</div>

<div class="cta-band">
  <h2>Still have a question?</h2>
  <p>Our team is happy to help you plan your stay.</p>
  <a href="contact.php" class="btn">Contact Us</a>
</div>

<?php include "includes/footer.php"; ?>