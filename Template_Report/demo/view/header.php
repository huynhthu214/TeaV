<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TeaV - <?php echo $namePage ?></title>
    <link rel="icon" type="image/png" href="layout/images/tea2.jpg">
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        rel="stylesheet"
    />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
    <link rel="stylesheet" href="layout/css/style_base.css"/>
    <link rel="stylesheet" href="layout/css/style1.css?v=2"/>
    <link rel="stylesheet" href="layout/css/style_header.css"/>
     <link rel="stylesheet" href="layout/css/style_payment.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

</head>
<body>
<header>
    <nav class="navbar section-content navbar-expand-lg">
        <div class="container-fluid">
            <a href="#" class="nav-logo">
                <h2 class="logo-text">🍃TeaV</h2>
            </a>
            <button
                class="navbar-toggler"
                style="background-color: #fdf751"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div
                class="collapse navbar-collapse justify-content-end"
                id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php if ($namePage === 'Trang chủ') echo 'active'; ?>" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($namePage === 'Về chúng tôi') echo 'active'; ?>" href="about.php">Về chúng tôi</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if ($namePage === 'Sản phẩm') echo 'active'; ?>" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Sản phẩm
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                            <?php
                            $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

                            if (!$conn) {
                                die("Kết nối thất bại: " . mysqli_connect_error());
                            }

                            $sql = "SELECT Categoryid, Name FROM categories";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0):
                                while ($row = mysqli_fetch_assoc($result)):
                            ?>
                                    <li>
                                        <a class="dropdown-item" href="products.php?category_id=<?php echo htmlspecialchars($row['Categoryid']); ?>">
                                            <?php echo htmlspecialchars($row['Name']); ?>
                                        </a>
                                    </li>
                            <?php
                                endwhile;
                            else:
                            ?>
                                    <li><a class="dropdown-item" href="#">Chưa có danh mục được hiển thị.</a></li>
                            <?php
                            endif;

                            mysqli_free_result($result);
                            mysqli_close($conn);
                            ?>
                            <li>
                                <a class="dropdown-item" href="products.php">Tất cả sản phẩm</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($namePage === 'Blog') echo 'active'; ?>" href="blog.php">Bài báo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($namePage === 'Chính sách & Điều khoản') echo 'active'; ?>" href="term.php">Chính sách & điều khoản</a>
                    </li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <?php $firstChar = strtoupper(substr($_SESSION['email'], 0, 1)); ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="avatar-circle"><?php echo $firstChar; ?></div>
                                <span style="color: white;"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php">Hồ sơ của tôi</a></li>
                                <li><a class="dropdown-item" href="cart.php">Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php if ($namePage === 'Login') echo 'active'; ?>" href="login.php">Đăng nhập</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <a href="cart.php">
                    <i class="bi bi-cart" style="color: white; font-size:16px"></i>
                </a>
            </div>
        </div>
    </nav>
</header>