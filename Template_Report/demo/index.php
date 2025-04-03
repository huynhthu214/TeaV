<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang Trà - Home</title>
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
    <!-- Header/Navbar -->
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
                <a class="nav-link active" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.html">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="product.html">Products</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="blog.html">Blog</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="term.html">Terms & Conditions</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
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

    <main>
      <section class="hero-section">
        <video autoplay loop muted class="hero-video-background">
          <source
            src="layout/images/Chai - Cinematic Tea B-Roll.mp4"
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
              <a href="#" class="button- order-now">Order Now</a>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer class="py-4 mt-5">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h4>🍃TeaV</h4>
            <p>
              Connecting tea lovers with global traditions, we explore the art
              of brewing and the rich stories behind every carefully selected
              leaf.
            </p>
          </div>
          <div class="col-md-4">
            <h5>Contact</h5>
            <p>Email: trangtra@gmail.com</p>
            <p>Phone: +123 456 7890</p>
          </div>
          <div class="col-md-4">
            <h5>Follow Us</h5>
            <a href="#" class="text-light me-2">Facebook</a>
            <a href="#" class="text-light me-2">Instagram</a>
            <a href="#" class="text-light">YouTube</a>
          </div>
        </div>
        <div class="text-center mt-3">
          <p class="mb-0">
            &copy; <span id="year"></span> 2025 Trang Tra. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/jquery.js"></script>
  </body>
</html>
