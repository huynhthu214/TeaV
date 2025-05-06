   <?php
   if(session_status() === PHP_SESSION_NONE){
    session_start();
   }

      $namePage = "Home";
      include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$product_query = "SELECT 
            product.ProductId,
            product.Name,
            product.Price,
            product.ImgUrl AS img_product,
            product.Type,
            GROUP_CONCAT(ingredients.IngreName SEPARATOR ', ') AS ingredients,
            product.Usefor
          FROM product
          LEFT JOIN productingredient ON product.ProductId = productingredient.ProductId
          LEFT JOIN ingredients ON productingredient.IngredientId = ingredients.IngredientId
          WHERE product.IsShow = 'Yes'
          GROUP BY product.ProductId
          LIMIT 6";

$about_query = "SELECT 
            about.AboutId,
            about.ImgUrl AS img_about,
            about.Title AS title_about,
            about.Content AS content_about,
            about.DateUpload AS date_about
        FROM about
        WHERE about.IsShow = 'Yes'
        ORDER BY DateUpload DESC
        LIMIT 1";

$blog_query = "SELECT 
            blog.BlogId,
            blog.ImgLink AS img_blog,
            blog.Title AS title_blog,
            blog.Content AS content_blog,
            blog.DateUpload AS date_blog,
            GROUP_CONCAT(tag.Name SEPARATOR ', ') AS tag_names
        FROM blog
        JOIN blogtag ON blog.BlogId = blogtag.BlogId
        JOIN tag ON blogtag.TagId = tag.TagId
        WHERE blog.IsShow = 'Yes'
        GROUP BY blog.BlogId
        ORDER BY DateUpload DESC";

$result = mysqli_query($conn, $product_query);
$about_result = mysqli_query($conn, $about_query);
$blog_result = mysqli_query($conn, $blog_query);

if (!$result || !$blog_result || !$about_result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}

$about_list = [];
while ($row = mysqli_fetch_assoc($about_result)) {
    $about_list[] = $row;
}
$first_about = $about_list[0];
?>

    <main>
      <section class="hero-section mb-5">
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
          <div class="container">
            <div class="row align-items-center">
   
            <div class="col-lg-5">
        <h2 class="fw-bold mb-3"><?php echo $first_about['title_about']; ?></h2>
        <p class="mb-4"><?php echo $first_about['content_about'];?></p>
        <a href="about.php" target="_blank">
          <button class="btn btn-primary fw-bold" id="learnMoreButton" type="button">Learn more</button>
        </a>
      </div>

      <div class="col-lg-7">
      <div id="aboutCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php  foreach ($about_list as $index => $about) {?>
      <div class="carousel-item <?php echo $index === 0 ? 'active': ''; ?>">
        <div class="d-flex gap-3">
          <div class="card border-0">
            <img src="<?php echo $about['img_about']; ?>" class="card-img-top rounded" alt="">
          </div>
          <div class="card border-0">
            <img src="<?php echo $about['img_about']; ?>" class="card-img-top rounded" alt="">
          </div>
          <div class="card border-0">
            <img src="<?php echo $about['img_about']; ?>" class="card-img-top rounded" alt="">
          </div>
        </div>
      </div>
      <?php } ?>
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
  <h1 class="product-section">
  <a href="product.php" style="text-decoration: none; color: inherit;">Products</a>
</h1>
        <div class="container-p-0">
        <div id="carouselProduct" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php 
    $i = 0;
    while ($product = mysqli_fetch_assoc($result)) {
        if ($i % 3 === 0) {
            if ($i === 0) {
                echo '<div class="carousel-item active"><div class="container"><div class="row g-4">';
            } else {
                echo '</div></div></div>';
                echo '<div class="carousel-item"><div class="container"><div class="row g-4">';
            }
        }
    ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="<?php echo $product['img_product']; ?>" class="card-img-top" alt="<?php echo $product['Name']; ?>" />
            <div class="card-body">
              <h5 class="card-title"><?php echo $product['Name']; ?></h5>
              <p class="card-text">
                <strong>Ingredients:</strong> <?php echo $product['ingredients']; ?><br />
                <strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?>
              </p>
              <a href="detail-product.php?id=<?php echo $product['ProductId']; ?>" class="btn btn-success">View Details</a>
            </div>
          </div>
        </div>
    <?php 
        $i++;
    }
    if ($i > 0) echo '</div></div></div>'; 
    ?>
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
  <h1 class="blog-section">
  <a href="blog.php" style="text-decoration: none; color: inherit;">Blogs</a>
</h1>

<div class="container my-5">
  <div class="row g-4">
    <?php while ($blog = mysqli_fetch_assoc($blog_result)) { ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
        <a href="blog.php?id=<?php echo $blog['BlogId']; ?>">
          <img src="<?php echo $blog['img_blog']; ?>" class="card-img-top" alt="<?php echo $blog['title_blog']; ?>" /></a>
          <div class="card-body">
            <div class="mb-2">
              <?php if (!empty($blog['tag_names'])) { ?>
                <span class="badge bg-success"><?php echo $blog['tag_names']; ?></span>
              <?php } ?>
            </div>
            <h5 class="card-title"><?php echo $blog['title_blog']; ?></h5>
            <p class="card-text"><?php echo $blog['content_blog']; ?></p>
          </div>
          <div class="card-footer text-muted small">
            <?php echo $blog['date_blog']; ?>
          </div>
        </div>
      </div> 
    <?php } ?>
  </div> 

  <div class="d-flex justify-content-center my-4">
    <a href="blog.php" class="btn btn-primary fw-bold">VIEW ALL ARTICLES</a>
  </div>
</div>

      </section>
    </main>
<?php 
    include "view/footer.php";
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
