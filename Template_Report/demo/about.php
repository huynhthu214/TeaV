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
      <section class="about-us">
        <div class="container-about">
          <div class="text">
            <h1>About us</h1>
            <p>Tea has a complex positive effect on the body. Daily use of a cup of tea is good for your health.</p>
          </div>
        </div>
        <div class="container">
          <div class="row py-5" id="row1">
            <div class="col">
              <h3>History</h3>
              <p>
                TeaV was founded in 2015 with a deep passion for tea, born
                from the lush green tea fields of Vietnam. Our journey is rooted in
                tradition, craftsmanship, and a love for nature's simplest
                pleasures.
              </p>
            </div>           
            <div class="col">
              <img src="layout/images/about-1.jpg" alt="">
            </div>
          </div>  
          <div class="row py-5" id="row2">
            <div class="col">
              <img src="layout/images/about-1.jpg" alt="">
            </div>
            <div class="col">
            <h3>Mission</h3>
              <p>
                To bring the essence of premium tea to
                every cup, connecting tea lovers with the rich heritage and
                flavors of Vietnam and beyond.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- footer -->
<?php 
    include "view/footer.php";
?>
