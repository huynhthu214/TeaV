   <?php
      $namePage = "Home";
      include "view/header.php";
    
    // require_once('db/product_db.php'); 
    // session_start();
    // if (!isset($_SESSION['user'])) {
    //     header('Location: login.php');
    //     exit();
    // }
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
        <h1 class="about-section">About</h1>
          <div class="container">
            <div class="row align-items-center">
   
            <div class="col-lg-5">
        <h2 class="fw-bold mb-3">The Story Behind Our Ocha House</h2>
        <p class="mb-4">
          We also specialize in bubble tea, a beverage originating from Taiwan that combines freshly brewed teas with a large variety of exotic natural fruit concentrates, served cold with delicious chewy tapioca pearls.
        </p>
        <button class="btn btn-primary fw-bold" id="learnMoreButton" type="button">Learn more</button>
      </div>

      <div class="col-lg-7">
        <div id="aboutCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
          
            <div class="carousel-item active">
              <div class="d-flex gap-3">
                <div class="card border-0">
                  <img src="../demo/layout/images/assam-gold.jpg" class="card-img-top rounded" alt="Green Tea">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Green Tea</p>
                  </div>
                </div>
                <div class="card border-0">
                  <img src="./layout/images/background-about.jpg" class="card-img-top rounded" alt="Chai Teas">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Chai Teas</p>
                  </div>
                </div>
                <div class="card border-0">
                  <img src="../demo/layout/images/ceylon-star.jpg" class="card-img-top rounded" alt="Single Estate">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Single Estate</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item active">
              <div class="d-flex gap-3">
                <div class="card border-0">
                  <img src="../demo/layout/images/assam-gold.jpg" class="card-img-top rounded" alt="Green Tea">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Green Tea</p>
                  </div>
                </div>
                <div class="card border-0">
                  <img src="./layout/images/background-about.jpg" class="card-img-top rounded" alt="Chai Teas">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Chai Teas</p>
                  </div>
                </div>
                <div class="card border-0">
                  <img src="../demo/layout/images/ceylon-star.jpg" class="card-img-top rounded" alt="Single Estate">
                  <div class="card-body text-center bg-beige rounded-bottom">
                    <p class="fw-bold m-0">Single Estate</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
          
        </div>
      </div>
    </div>
  </div>
      <h1 class="product-section">Products</h1>
        <div class="container-p-0">
          <div id="carouselProduct" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
       
        <div class="carousel-item active">
          <div class="container">
            <div class="row">
              <div class="col-md-4 product-card-home" data-type="green" data-price="mid" data-use="relax">
                <div class="card">
                  <img src="images/green-tea.jpg" class="card-img-top" alt="Green Tea" />
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

              <div class="col-md-4 product-card-home" data-type="herbal" data-price="low" data-use="digestion">
                <div class="card">
                  <img src="images/herbal-tea.jpg" class="card-img-top" alt="Herbal Tea" />
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

              <div class="col-md-4 product-card-home" data-type="blacktea" data-price="high" data-use="energy">
                <div class="card">
                  <img src="images/black-tea.jpg" class="card-img-top" alt="Black Tea" />
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
          </div>
        </div>

        <div class="carousel-item active">
          <div class="container">
            <div class="row">
              <div class="col-md-4 product-card-home" data-type="green" data-price="mid" data-use="relax">
                <div class="card">
                  <img src="images/green-tea.jpg" class="card-img-top" alt="Green Tea" />
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

              <div class="col-md-4 product-card-home" data-type="herbal" data-price="low" data-use="digestion">
                <div class="card">
                  <img src="images/herbal-tea.jpg" class="card-img-top" alt="Herbal Tea" />
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

              <div class="col-md-4 product-card-home" data-type="blacktea" data-price="high" data-use="energy">
                <div class="card">
                  <img src="images/black-tea.jpg" class="card-img-top" alt="Black Tea" />
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
          </div>
        </div>

      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#carouselProduct" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselProduct" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
        <h1 class="blogs-section">Blogs</h1>
        <div class="container my-5">
  <div class="row g-4">
   
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <img src="./layout/images/chamomile-bliss.jpg" class="card-img-top" alt="Green Tea Daily" />
        <div class="card-body">
          <div class="mb-2">
            <span class="badge bg-success">Healthcare</span>
            <span class="badge bg-warning text-dark">Lifestyle</span>
          </div>
          <h5 class="card-title">Five Reasons to Drink Green Tea Daily</h5>
          <p class="card-text">Break down some of the health benefits of one of the most classic teas, GREEN tea!</p>
        </div>
        <div class="card-footer text-muted small">3 min read • May 24, 2022</div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <img src="./layout/images/earl-grey-classic.jpg" class="card-img-top" alt="Yerba Mate Tea" />
        <div class="card-body">
          <div class="mb-2">
            <span class="badge bg-success">Healthcare</span>
          </div>
          <h5 class="card-title">3 Reasons We Are Obsessed with Yerba Mate Tea</h5>
          <p class="card-text">Why Yerba Mate Tea is becoming everyone's favorite energetic tea lately.</p>
        </div>
        <div class="card-footer text-muted small">3 min read • May 24, 2022</div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <img src="./layout/images/chamomile-bliss-1.jpg" class="card-img-top" alt="Motivated This Winter" />
        <div class="card-body">
          <div class="mb-2">
            <span class="badge bg-info">Recipe</span>
          </div>
          <h5 class="card-title">How Tea Can Help You Stay Motivated This Winter</h5>
          <p class="card-text">Warm up your soul and motivation with a good cup of seasonal tea.</p>
        </div>
        <div class="card-footer text-muted small">3 min read • May 24, 2022</div>
      </div>
    </div>
  </div>
</div>

<div class="d-flex justify-content-center my-4">
  <a href="blog.php" class="btn btn-primary fw-bold">VIEW ALL ARTICLES</a>
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
