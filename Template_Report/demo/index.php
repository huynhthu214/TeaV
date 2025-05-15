   <?php
   if(session_status() === PHP_SESSION_NONE){
    session_start();
   }

      $namePage = "Trang chủ";
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
        ORDER BY DateUpload DESC";

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
        ORDER BY DateUpload DESC
        LIMIT 3";

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
        </video>
        <div class="section-content">
          <div class="hero-details">
            <h2 class="title">Best Tea</h2>
            <h3 class="subtitle">Hãy làm cho ngày của bạn tuyệt vời hơn với loại trà đặc biệt của chúng tôi!</h3>
            <p class="description">
                Khám phá thế giới trà truyền thống, nơi mỗi lá trà được lựa chọn cẩn thận kể một câu chuyện về di sản và niềm đam mê. Từ những khu vườn trên núi
                mù sương đến các kỹ thuật pha chế lâu đời, trà của chúng tôi là một hành trình
                của hương vị, văn hóa và niềm vui thuần khiết.
            </p>
            <div class="buttons">
              <a href="products.php" class="button-order-now">Đặt hàng ngay</a>
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
                <button class="btn btn-primary fw-bold" type="button">Xem thêm</button>
              </a>
            </div>

            <div class="col-lg-7">
              <div id="aboutCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php 
                    $i = 0;
                    foreach ($about_list as $about) {
                        if ($i % 3 == 0) {
                            if ($i == 0) {
                                echo '<div class="carousel-item active"><div class="row">';
                            } else {
                                echo '</div></div>'; // Close previous group
                                echo '<div class="carousel-item"><div class="row">';
                            }
                        }
                  ?>
                    <div class="col-md-4">
                      <div class="card border-0">
                        <img src="<?php echo $about['img_about']; ?>" class="card-img-top rounded" alt="">
                      </div>
                    </div>
                  <?php 
                        $i++;
                    }
                    if ($i > 0) echo '</div></div>'; // Close last group
                  ?>
                </div>

                <!-- Nút điều hướng Bootstrap chuẩn -->
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
      </section>
      
      <section class="py-5 text-center bg-white commit-section">
  <div class="container">
    <div class="mb-4">
      <img src="layout/images/leaf.jpg" alt="Leaf icon" style="height: 60px; width: 50px;">
      <h2 class="fw-bold mt-3">Cam kết của chúng tôi</h2>
      <p class="text-muted">
       "Chất lượng là ưu tiên hàng đầu – từ lá trà đến trải nghiệm của bạn."
      </p>
    </div>

    <div class="row text-center mt-5">
      <div class="col-md-4">
        <img src="layout/images/tea-field-1.jpg" alt="Handmade" class="mb-3" style="height: 150px; width: 250px;">
        <h5 class="fw-bold">Sản phẩm thủ công</h5>
        <p class="text-muted">
         Chúng tôi tạo ra các loại trà lá rời thủ công tốt cho sức khỏe, sản phẩm tắm và chăm sóc cơ thể, cùng với đường hương vị, tất cả đều được sản xuất theo mẻ nhỏ để đảm bảo chất lượng và độ tươi mới.
        </p>
      </div>

      <div class="col-md-4">
        <img src="layout/images/coffee.jpg" alt="Customer service" class="mb-3" style="height: 150px; width: 200px;">
        <h5 class="fw-bold">Dịch vụ khách hàng tốt</h5>
        <p class="text-muted">
         Ngày nay, thật khó để tìm được những công ty thực sự quan tâm đến khách hàng. Nhưng chúng tôi luôn ở đây vì bạn. Chúng tôi thật sự quan tâm đến sức khỏe của bạn.
        </p>
      </div>

      <div class="col-md-4">
        <img src="layout/images/natural.jpg" alt="Natural" class="mb-3" style="height: 140px; width: 250px;">
        <h5 class="fw-bold">Thành phần tự nhiên</h5>
        <p class="text-muted">
         Chúng tôi sử dụng các nguyên liệu có nguồn gốc từ thiên nhiên. Hãy bảo vệ bản thân bạn và những người thân yêu. Trong các loại trà pha trộn của chúng tôi, hoàn toàn không sử dụng hương liệu nhân tạo hay chất bảo quản.
        </p>
      </div>
    </div>
  </div>
</section>

      <section class="py-5 select-section" style="background-color: #264c3d;">
  <div class="container-fluid">
    <div class="row align-items-center">
      <!-- Nội dung bên trái -->
      <div class="col-md-6 text-white px-5 py-4">
        <p class="text-warning fw-bold text-uppercase mb-2">Được chọn lọc kỹ lưỡng</p>
        <h2 class="fw-bold mb-4">Bền vững và Môi trường</h2>
        <p class="mb-4" style="color: #ddd;">
         Chúng tôi quan tâm đến việc sử dụng nhựa và đang thực hiện các hành động nhằm cải thiện tính bền vững của sản phẩm, đồng thời giảm thiểu tác động đến môi trường.
        </p>

        <h5 class="fw-bold">100% Hữu cơ</h5>
        <p style="color: #ccc;">Nguyên liệu được chọn lọc kỹ lưỡng, đảm bảo chất lượng tự nhiên và lành mạnh cho sức khỏe.</p>

        <h5 class="fw-bold">Luôn luôn tươi mới</h5>
        <p style="color: #ccc;">Sản phẩm luôn được chế biến tươi mới theo từng đợt nhỏ để đảm bảo hương vị và chất lượng tốt nhất.</p>
      </div>

      <!-- Hình ảnh bên phải -->
          <div class="col-md-6 p-0">
            <img src="layout/images/thu-hoach.jpg" alt="Sustainability Image" class="img-fluid w-100 h-100" style="object-fit: cover;">
          </div>
        </div>
      </div>
    </section>

  <h1 class="product-section">
  <a href="product.php" style="text-decoration: none; color: inherit;">Sản phẩm trà</a>
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
        <div class="card h-100 d-flex flex-column shadow-sm">
          <a href="blog.php?id=<?php echo $blog['BlogId']; ?>">
            <img src="<?php echo $blog['img_blog']; ?>" class="card-img-top" alt="<?php echo $blog['title_blog']; ?>" />
          </a>
          <div class="card-body flex-grow-1">
            <div class="mb-2">
              <?php if (!empty($blog['tag_names'])) { ?>
                <span class="badge bg-success"><?php echo $blog['tag_names']; ?></span>
              <?php } ?>
            </div>
            <h5 class="card-title"><?php echo $blog['title_blog']; ?></h5>
           <p class="card-text">
            <?php 
              $snippet = strip_tags($blog['content_blog']); 
              $snippet = mb_substr($snippet, 0, 100); 
              echo $snippet . (mb_strlen($snippet) >= 100 ? '...' : '');
            ?>
          </p>
          </div>
          <div class="card-footer text-muted small mt-auto">
            <?php echo $blog['date_blog']; ?>
          </div>
        </div>
      </div> 
    <?php } ?>
  </div> 
</div>

  <div class="d-flex justify-content-center my-4">
    <a href="blog.php" class="btn btn-primary fw-bold">XEM TẤT CẢ</a>
  </div>
</div>
      </section>
    </main>
    <?php 
    include "view/footer.php";
?>
  </body>
</html>