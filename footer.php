<footer class="footer">
  <p>&copy; <?php echo date("Y"); ?> Grand Stay Hotel. All rights reserved.</p>
  <p>Bareilly, Uttar Pradesh | +91 98765 43210</p>
</footer>

<!-- WhatsApp: neeche 919876543210 ki jagah apna number likho (91 ke saath, bina + aur space ke) -->
<a href="https://wa.me/919876543210?text=Hello%20Grand%20Stay%20Hotel%2C%20I%20would%20like%20to%20know%20more." class="wa-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" title="Chat with us on WhatsApp">&#128172;</a>

<button type="button" id="toTop" aria-label="Back to top" title="Back to top">&#8593;</button>

<script>
(function () {
  var b = document.getElementById("toTop");
  window.addEventListener("scroll", function () {
    b.classList.toggle("show", window.scrollY > 400);
  });
  b.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
})();
</script>

</body>
</html>