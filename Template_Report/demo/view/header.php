
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TeaV - <?php echo $namePage ?></title>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="layout/css/style.css"/>
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
              <a class="nav-link <?php if ($namePage === "Home") {
                echo "active";} ?>" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($namePage === "About") {
                echo "active";} ?>" href="about.php">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($namePage === "Products") {
                echo "active";} ?>" href="product.php">Products</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($namePage === "Blog") {
                echo "active";} ?>" href="blog.php">Blog</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php if ($namePage === "Terms & Conditions") {
                echo "active";} ?>" href="term.php">Terms & Conditions</a>
            </li>
            <li class="nav-item">
                <a
                  class="nav-link <?php if ($namePage === "Login") {
                  echo "active";} ?>"href="login.php">Login</a>
              </li>
          </ul>
        </div>
      </div>
    </nav>

  </header>
