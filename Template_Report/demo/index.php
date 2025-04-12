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
      <section>
        <h2 class="product">Products</h2>
        <div class="row" id="productList">
  
  <div
    class="col-md-4 product-card-home"
    data-type="green"
    data-price="mid"
    data-use="relax"
  >
    <div class="card">
      <img
        src="images/green-tea.jpg"
        class="card-img-top"
        alt="Green Tea"
      />
      <div class="card-body">
        <h5 class="card-title">Green Tea</h5>
        <p class="card-text">
          <strong>Ingredients:</strong> Green tea leaves, Jasmine<br />
          <strong>Price:</strong> $35.00
        </p>
        <a href="detail-product.php?id=1" class="btn btn-success">View Details</a>
      </div>
    </div>
  </div>

  <div
    class="col-md-4 product-card-home"
    data-type="herbal"
    data-price="low"
    data-use="digestion"
  >
    <div class="card">
      <img
        src="images/herbal-tea.jpg"
        class="card-img-top"
        alt="Herbal Tea"
      />
      <div class="card-body">
        <h5 class="card-title">Herbal Tea</h5>
        <p class="card-text">
          <strong>Ingredients:</strong> Chamomile, Peppermint<br />
          <strong>Price:</strong> $25.00
        </p>
        <a href="detail-product.php?id=2" class="btn btn-success">View Details</a>
      </div>
    </div>
  </div>

  <div
    class="col-md-4 product-card-home"
    data-type="blacktea"
    data-price="high"
    data-use="energy"
  >
    <div class="card">
      <img
        src="images/black-tea.jpg"
        class="card-img-top"
        alt="Black Tea"
      />
      <div class="card-body">
        <h5 class="card-title">Black Tea</h5>
        <p class="card-text">
          <strong>Ingredients:</strong> Black tea, Spices<br />
          <strong>Price:</strong> $45.00
        </p>
        <a href="detail-product.php?id=3" class="btn btn-success">View Details</a>
      </div>
    </div>
  </div>
  <div
    class="col-md-4 product-card-home"
    data-type="blacktea"
    data-price="high"
    data-use="energy"
  >
    <div class="card">
      <img
        src="images/black-tea.jpg"
        class="card-img-top"
        alt="Black Tea"
      />
      <div class="card-body">
        <h5 class="card-title">Black Tea</h5>
        <p class="card-text">
          <strong>Ingredients:</strong> Black tea, Spices<br />
          <strong>Price:</strong> $45.00
        </p>
        <a href="detail-product.php?id=3" class="btn btn-success">View Details</a>
      </div>
    </div>
  </div>
</div>
      </section>
      <section>
        <h2 class="blog">Blogs</h2>
      </section>
      <section>
        <h2 class="about">About</h2>
      </section>
    </main>
<?php 
    include "view/footer.php";
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/jquery.js"></script>
  </body>
</html>
