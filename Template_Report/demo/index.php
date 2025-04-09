
   <?php
      $namePage = "Home";
      include "view/header.php";
    ?>
    <main>
      <section class="hero-section">
        <video autoplay loop muted class="hero-video-background">
          <source
            src="layout/images/video-home.mp4"
            type="video/mp4"
          />
          Your browser does not support the video tag.
        </video>
        <div class="section-content">
          <div class="hero-details">
            <h2 class="title">Best Tea</h2>
            <h3 class="subtitle">Make your day great with our special tea!</h3>
            <p class="description">
              Discover a world of traditional tea, where each carefully selected
              leaf tells a story of heritage and passion. From misty mountain
              gardens to time-honored brewing techniques, our teas are a journey
              of flavor, culture, and pure delight.
            </p>
            <div class="buttons">
              <a href="#" class="button-order-now">Order Now</a>
            </div>
          </div>
        </div>
      </section>
    </main>
<?php 
    include "view/footer.php";
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/jquery.js"></script>
  </body>
</html>
