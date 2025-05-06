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
        <div class="container py-5">
          <div class="row py-5" id="row1">
            <div class="col-4">
              <h3>History</h3>
              <p>
                Tea Heritage began in the misty mountains of Vietnam when founder Mai Nguyen discovered her grandmother's ancient tea recipes in 2008. These treasured family techniques, passed down through generations, inspired our mission to preserve authentic tea traditions. </br>
                What started as a small family garden has grown into a network of artisans working with farmers across Vietnam's finest tea regions. We combine time-honored methods with sustainable practices to create exceptional teas that connect people to our cultural heritage and the natural world.
             </p>
            </div>           
            <div class="col-8">
              <img src="./layout/images/about-0.jpg" alt="">
            </div>
          </div>  
          <div class="row py-5" id="row2">
            <div class="col-8">
              <img src="layout/images/about-1.jpg" alt="">
            </div>
            <div class="col-4">
              <h3>Mission</h3>
              <tr>
                At Tea Heritage, our mission is threefold:
                <li>
                  Preserve: To safeguard traditional tea cultivation practices that have shaped Vietnam's agricultural landscape for centuries, ensuring these methods continue to thrive in a modern world.
                </li>
                <li>
                  Connect: To build meaningful bridges between our tea-growing communities and tea lovers worldwide, fostering cultural understanding and appreciation through the universal language of tea.
                </li>
                <li>
                  Elevate: To continuously refine our craft, pushing the boundaries of what tea can be while honoring its rich history and tradition.
                </li>
              </tr>              
            </div>
          </div>
          <div class="row py-5" id="row2">
            <div class="col-md-3">
              <div class="card" style="width: 18rem; height: auto; border: none">
                <img src="./layout/images/about-2.jpg" class="card-img-top-about" style="border-radius: 4px" alt="...">
                <div class="card-body">
                  <p class="card-text-1">
                    <h5 class="text-1">Sustainability</h5> </br>
                    Our commitment to the environment guides everything we do—from organic farming practices and biodegradable packaging to fair compensation for farmers and investment in local communities.
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card" style="width: 18rem; height: auto; border: none">
                <img src="./layout/images/about-3.jpg" class="card-img-top-about" style="border-radius: 4px" alt="...">
                <div class="card-body">
                  <p class="card-text-1">
                    <h5 class="text-1">Innovation</h5> </br>
                    While we respect tradition, we also embrace thoughtful innovation. We continuously explore new techniques and flavor profiles that can enhance the tea experience while staying true to our roots.                  
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card" style="width: 18rem; height: auto; border: none">
                <img src="./layout/images/about-4.jpg" class="card-img-top-about" style="border-radius: 4px" alt="...">
                <div class="card-body">
                  <p class="card-text-1">
                    <h5 class="text-1">Authenticity</h5> </br>
                    We honor traditional methods and authentic flavors, never compromising on quality or cutting corners for convenience. Each product reflects genuine practices refined over generations.
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card" style="width: 18rem; height: auto; border: none">
                <img src="./layout/images/about-5.jpg" class="card-img-top-about" style="border-radius: 4px" alt="...">
                <div class="card-body">
                  <p class="card-text-1">
                    <h5 class="text-1">Community</h5> </br>
                    We believe in the power of tea to bring people together. Whether it's farmers collaborating on cultivation techniques or customers gathering a shared pot, tea creates connections that transcend borders.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="row py-5">
            <div id="carouselExampleDark" class="carousel slide">
              <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
              </div>
              <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                  <img src="./layout/images/quote-1.jpg" class="d-block-about w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <p style="font-style: italic; ">"In the leaf's unfurling, we witness the dance between human hands and nature's bounty. This dance is what we call tea."</p>
                    <h6 style="font-weight: bold">— Traditional Vietnamese saying</h6>
                  </div>
                </div>
                <div class="carousel-item" data-bs-interval="2000">
                  <img src="./layout/images/quote-2.jpg" class="d-block-about w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <p style="font-style: italic;">"Tea reminds us to slow down. In a world of instant gratification, the patient art of brewing teaches us that the most rewarding experiences cannot be rushed." </p>
                    <h6 style="font-weight: bold">— The 🍃TeaV Philosophy</h6>
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="./layout/images/quote-3.jpg" class="d-block-about w-100" alt="...">
                  <div class="carousel-caption d-none d-md-block">
                    <p style="font-style: italic;">"Each tea leaf carries the memory of the soil that nourished it, the rain that fed it, the sun that warmed it, and the hands that tenderly harvested it. When you drink our tea, you taste this entire journey."</p>
                    <h6 style="font-weight: bold">— From "The Tea Heritage Story"</h6>
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- footer -->
<?php 
    include "view/footer.php";
?>
