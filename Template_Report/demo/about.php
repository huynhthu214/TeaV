  
<!-- Header/Navbar -->
    <?php 
        $namePage = "About";
        include "view/header.php";
        
        $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

$about_query = "SELECT 
            about.AboutId,
            about.ImgUrl AS img_about,
            about.Title,
            about.Content,
            about.DateUpload
        FROM about
        WHERE about.IsShow = 'Yes'
        ORDER BY DateUpload ASC";

$about_result = mysqli_query($conn, $about_query);

if (!$about_result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}
$about = mysqli_fetch_assoc($about_result);
    ?>
    <main>
      <section class="about-us py-5">
        <div class="container">
          <h1 class="text-center mb-4">About Us</h1>
          <section class="section-1">
          <div class="row py-5">
            <div class="col-md-6">
              <h3>Our History</h3>
              <p>
                TeaV was founded in 2015 with a deep passion for tea, born
                from the lush green tea fields of Vietnam. What started as a
                small family venture has grown into a brand dedicated to sharing
                the finest teas with the world. Our journey is rooted in
                tradition, craftsmanship, and a love for nature's simplest
                pleasures.
              </p>
            </div>
            <div class="col-md-6">
              <h3>Mission & Vision</h3>
              <p>
                <strong>Mission:</strong> To bring the essence of premium tea to
                every cup, connecting tea lovers with the rich heritage and
                flavors of Vietnam and beyond.<br />
                <strong>Vision:</strong> To become a globally recognized tea
                brand that celebrates sustainability, quality, and the art of
                tea-making.
              </p>
            </div>
          </div>
          </section> 
          <section class="section-2">
            <div class="row mt-5">
              <div class="col-md-4">
                <img
                  src="layout/images/tea-field.jpg" alt="Tea Fields" class="img-fluid rounded" />
                <h3 class="mt-2">Our Tea Fields</h3>
                <p class="mt-2">
                  Carefully tended tea fields reflect dedication and harmony with nature. Each leaf tells the story of farmers who nurture the plants, ensuring high-quality tea that embodies the region's unique flavors and traditions.              
                </p>
              </div>
              <div class="col-md-4">
                <h3 class="mt-2">Harvest</h3>
                <p class="mt-2">
                  Harvest tea begins with cultivating tea plants under optimal conditions. Farmers hand-pick young leaves at peak seasons, which are then withered and oxidized or dried. Finally, the leaves are packaged to preserve their quality for consumers.              
                </p>
                <img src="layout/images/thu-hoach.jpg" alt="" class="img-fluid rounded" />
              </div>
              <div class="col-md-4">
                <img src="layout/images/van-chuyen.jpg" alt="" class="img-fluid rounded" />
                <h3 class="mt-2">Our Craftsmanship</h3>
                <p class="mt-2">
                  Transportation tea involves collecting high-quality leaves and processing them through drying and rolling. After packaging in airtight containers, logistics ensure timely delivery to retailers or consumers while maintaining quality control throughout the journey.             
                </p>
              </div>
            </div>
          </section>
          <div class="text-center mt-5">
            <p class="lead">
              Discover the soul of tea with TeaV - where tradition meets taste.
            </p>
          </div>
        </div>
      </section>
    </main>
    <!-- footer -->
<?php 
    include "view/footer.php";
?>
