
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
    <link rel="stylesheet" href="layout/css/style.css" />
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
                  echo "active";} ?>"href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mx-auto" id="loginModalLabel">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="email" class="form-label">Account</label>
            <input type="email" class="form-control" id="email" placeholder="Enter your account name" />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Enter your password" />
          </div>
          <!-- Forgot Password Link -->
          <div class="mb-3 text-end">
            <a href="#" id="forgotPassword" class="text-decoration-none">Forgot Password?</a>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
          <div class="text-center mt-3">
            <p>
              Don't have an account? <a href="#" id="openRegisterModal">Sign Up</a>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

      <!-- Register Modal -->
      <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title mx-auto">Sign Up</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>
            <div class="modal-body">
              <form>
                <div class="mb-3">
                  <label for="new-email" class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="new-email"
                    placeholder="Enter your email"
                  />
                </div>
                <div class="mb-3">
                  <label for="new-password" class="form-label">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="new-password"
                    placeholder="Create a password"
                  />
                </div>
                <button type="submit" class="btn btn-success w-100">
                  Sign Up
                </button>
                <div class="text-center mt-3">
                  <p>
                    Already have an account?
                    <a href="#" id="openLoginModal">Login</a>
                  </p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </header>
