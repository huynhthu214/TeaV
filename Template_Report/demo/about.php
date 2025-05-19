<?php 
$namePage = "Về chúng tôi";
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
            about.DateUpload,
            about.Email
        FROM about
        WHERE about.IsShow = 'Yes' AND about.Email = 'anna.nguyen@email.com'
        ORDER BY AboutId ASC";

$about_result = mysqli_query($conn, $about_query);

if (!$about_result) {
    die("Kết nối thất bại: " . mysqli_error($conn));
}

$about_data = [];
$quotes = [];
while ($row = mysqli_fetch_assoc($about_result)) {
    if (strpos($row['Title'], 'Quote: ') === 0) {
        $quotes[] = $row;
    } else {
        $about_data[$row['Title']] = $row;
    }
}
?>

<!-- <link rel="stylesheet" href="layout/css/style_about.css"/> -->

<main>
  <section class="about-us">
    <div class="container-about">
      <div class="text">
        <h1>Về chúng tôi</h1>
        <p>Trà không chỉ đơn thuần là một thức uống - mỗi tách trà mỗi ngày đều góp phần nuôi dưỡng sức khỏe và mang lại nhiều lợi ích tuyệt vời cho cơ thể.</p>
      </div>
    </div>
    <div class="container py-5">
      <div class="row py-5" id="row1">
        <div class="col-4">
          <h3>Lịch sử</h3>
          <p>
            <?php echo htmlspecialchars($about_data['Lịch sử']['Content']); ?>
          </p>
        </div>           
        <div class="col-8">
          <img src="<?php echo htmlspecialchars($about_data['Lịch sử']['img_about']); ?>" alt="">
        </div>
      </div>  
      <div class="row py-5" id="row2">
        <div class="col-8">
          <img src="<?php echo htmlspecialchars($about_data['Sứ mệnh']['img_about']); ?>" alt="">
        </div>
        <div class="col-4">
          <h3>Sứ mệnh</h3>
          <p>
            <?php echo htmlspecialchars($about_data['Sứ mệnh']['Content']); ?>
          </p>
        </div>
      </div>
      <div class="row py-5" id="row2">
        <div class="col-md-3">
          <div class="card" style="width: 18rem; height: 40rem; border: none">
            <img src="<?php echo htmlspecialchars($about_data['Phát triển bền vững']['img_about']); ?>" class="card-img-top-about" style="border-radius: 4px" alt="...">
            <div class="card-body">
              <p class="card-text-1">
                <h5 class="text-1">Phát triển bền vững</h5> <br>
                <?php echo htmlspecialchars($about_data['Phát triển bền vững']['Content']); ?>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card" style="width: 18rem; height: 40rem; border: none">
            <img src="<?php echo htmlspecialchars($about_data['Sáng tạo']['img_about']); ?>" class="card-img-top-about" style="border-radius: 4px" alt="...">
            <div class="card-body">
              <p class="card-text-1">
                <h5 class="text-1">Sáng tạo</h5> <br>
                <?php echo htmlspecialchars($about_data['Sáng tạo']['Content']); ?>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card" style="width: 18rem; height: 40rem; border: none">
            <img src="<?php echo htmlspecialchars($about_data['Tính xác thực']['img_about']); ?>" class="card-img-top-about" style="border-radius: 4px" alt="...">
            <div class="card-body">
              <p class="card-text-1">
                <h5 class="text-1">Tính xác thực</h5> <br>
                <?php echo htmlspecialchars($about_data['Tính xác thực']['Content']); ?>
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card" style="width: 18rem; height: 40rem; border: none">
            <img src="<?php echo htmlspecialchars($about_data['Cộng đồng']['img_about']); ?>" class="card-img-top-about" style="border-radius: 4px" alt="...">
            <div class="card-body">
              <p class="card-text-1">
                <h5 class="text-1">Cộng đồng</h5> <br>
                <?php echo htmlspecialchars($about_data['Cộng đồng']['Content']); ?>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="row py-5">
        <div id="carouselExampleDark" class="carousel slide">
          <div class="carousel-indicators">
            <?php foreach ($quotes as $index => $quote): ?>
              <button type="button" data-bs淹target="#carouselExampleDark" data-bs-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
          </div>
          <div class="carousel-inner">
            <?php foreach ($quotes as $index => $quote): ?>
              <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-bs-interval="<?php echo $index === 0 ? '10000' : '2000'; ?>">
                <img src="<?php echo htmlspecialchars($quote['img_about']); ?>" class="d-block-about w-100" alt="...">
                <div class="carousel-caption d-none d-md-block">
                  <p style="font-style: italic;"><?php echo htmlspecialchars($quote['Content']); ?></p>
                  <h6 style="font-weight: bold"><?php echo htmlspecialchars(substr($quote['Title'], 7)); ?></h6>
                </div>
              </div>
            <?php endforeach; ?>
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
<?php 
  include "view/footer.php";
  mysqli_close($conn);
?>