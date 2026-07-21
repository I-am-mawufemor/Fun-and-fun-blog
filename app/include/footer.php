<!-- footer -->


<footer class="footer" aria-label="Site footer">

  <div class="footer-layout">

    <!-- Col 1: Brand -->
    <div class="footer-col">
      <div class="footer-brand">
        <div class="footer-logo">
           <a href="?page=home">
                <img class="image-logo"
                src="<?php echo BASE_URL; ?>public/image/logo.jpg" alt="Tech & Fun Logo">
            </a>
          <span class="footer-logo-text" style="display:none;">APF</span>
        </div>

        <div class="footer-divider"></div>

        <p class="footer-tagline">
          Quenching thirst, igniting passion — empowering communities through
          knowledge and opportunity.
        </p>

        <div class="footer-socials-small">
          <a href="#" class="social-link whatsapp" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <a href="#" class="social-link twitter"   aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" class="social-link facebook"  aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="social-link instagram" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="social-link linkedin"  aria-label="LinkedIn"><i class="fa-brands fa-tiktok"></i></a>
        </div>
      </div>
    </div>

    <!-- Col 2: Links -->
    <div class="footer-col">
      <div class="footer-links-group">

        <div class="footer-content">
          <h3 class="footer-content-headline">Partners</h3>
          <ul>
            <li><a href="#">Awakening Potential Foundation</a></li>
            <li><a href="#">Tech &amp; Fun</a></li>
            <li><a href="#">ReadEase</a></li>
            <li><a href="#">Peason &amp; Sons</a></li>
          </ul>
        </div>

        <div class="footer-content">
          <h3 class="footer-content-headline">Sponsors</h3>
          <ul>
            <li><a href="#">Awakening Potential Foundation</a></li>
            <li><a href="#">Tech &amp; Fun</a></li>
          </ul>
        </div>

      </div>
    </div>

    <!-- Col 3: Newsletter -->
    <div class="footer-col">
      <div class="footer-newsletter">
        <h3 class="footer-content-headline">Stay in the Loop</h3>

        <p class="newsletter-copy">
          Be the first to know about our <strong>new products, exclusive collections,</strong>
          and special offers. Subscribe to our newsletter and never miss a drop.
        </p>

        <div class="newsletter-form">
          <div class="newsletter-input-wrap">
            <input
              class="newsletter-input"
              id="newsletterEmail"
              type="email"
              placeholder="Your email address"
              aria-label="Email address for newsletter"
            />
            <button class="newsletter-btn" id="newsletterBtn" aria-label="Subscribe">
              Subscribe <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>

          <p class="newsletter-note">
            No spam, ever. Unsubscribe at any time.
          </p>

          <div class="newsletter-success" id="newsletterSuccess" role="alert">
            <i class="fa-solid fa-circle-check"></i>
            <span>You're subscribed! Welcome to the community.</span>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Bottom bar -->
  <div class="footer-bottom">
    <div class="footer-bottom-inner">
      <p>&copy; <span id="year"></span> Fun &amp; tech. All rights reserved.</p>
      <p class="powered-by">
        <i class="fa-solid fa-bolt"></i>
        Powered by <a href="#" aria-label="Tech and Fun">Tech &amp; Fun</a>
      </p>
    </div>
  </div>

</footer>

<script src="script/footer.js" type="module"></script>
<script src="script/contact.js" type="module"></script>
<script src="script/script.js" type="module"></script>
<script src="script/main.js" type="module"></script>
<script src="script/main-2.js" type="module"></script>
<script src="script/main-3.js" type="module"></script>
<script src="script/image.js" type="module"></script>


</body>
</html>