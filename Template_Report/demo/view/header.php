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
