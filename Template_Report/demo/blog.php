<?php 
    $namePage = "Blog";
    include "view/header.php";
?>
    <main>
      <section class="blog py-5">
        <div class="container">
          <h1 class="text-center mb-4">Tea Blog</h1>

          <!-- Blog Post 1 -->
          <article class="blog-post">
            <h2>The Health Benefits of Green Tea</h2>
            <p class="text-muted">Posted on March 26, 2025 by Trang Tea Team</p>
            <img
              src="images/6a3771cc2b2e3f060e1e6fec7d60344c.jpg"
              class="img-fluid mb-3"
              alt="Green Tea"
            />
            <p>
              Green tea is renowned for its antioxidant properties, which help
              combat free radicals in the body. Rich in catechins, it supports
              heart health, boosts metabolism, and enhances mental focus. Learn
              how a daily cup can transform your wellness routine.
            </p>
            <div class="share-buttons">
              <a
                href="https://facebook.com/sharer/sharer.php?u=#"
                target="_blank"
                class="btn btn-sm btn-primary"
                ><i class="fab fa-facebook-f"></i> Share</a
              >
              <a
                href="https://twitter.com/intent/tweet?url=#&text=The Health Benefits of Green Tea"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            <div class="comment-section">
              <h5>Comments</h5>
              <div id="comments-1" class="mb-3"></div>
              <form class="comment-form" data-post="1">
                <div class="mb-3">
                  <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Write your comment..."
                    required
                  ></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                  Post Comment
                </button>
              </form>
            </div>
          </article>

          <!-- Blog Post 2 -->
          <article class="blog-post">
            <h2>How to Brew the Perfect Cup of Tea</h2>
            <p class="text-muted">Posted on March 20, 2025 by Trang Tea Team</p>
            <img
              src="images/3fc845b5f8f91e1f7f047fdf92ed2bfc.jpg"
              class="img-fluid mb-3"
              alt="Tea Brewing"
            />
            <p>
              Brewing tea is an art. Use fresh water, steep at the right
              temperature (80°C for green tea, 100°C for black tea), and time it
              perfectly (2-3 minutes). Follow these steps to elevate your tea
              experience.
            </p>
            <div class="share-buttons">
              <a
                href="https://facebook.com/sharer/sharer.php?u=#"
                target="_blank"
                class="btn btn-sm btn-primary"
                ><i class="fab fa-facebook-f"></i> Share</a
              >
              <a
                href="https://twitter.com/intent/tweet?url=#&text=How to Brew the Perfect Cup of Tea"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            <div class="comment-section">
              <h5>Comments</h5>
              <div id="comments-2" class="mb-3"></div>
              <form class="comment-form" data-post="2">
                <div class="mb-3">
                  <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Write your comment..."
                    required
                  ></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                  Post Comment
                </button>
              </form>
            </div>
          </article>

          <!-- Blog Post 3 -->
          <article class="blog-post">
            <h2>DIY Tea-Infused Essential Oil Recipe</h2>
            <p class="text-muted">Posted on March 15, 2025 by Trang Tea Team</p>
            <img
              src="images/1bc5ea54ad81f86126dcfae2caa71270.jpg"
              class="img-fluid mb-3"
              alt="Essential Oil"
            />
            <p>
              Create your own tea-infused essential oil with dried tea leaves,
              carrier oil (like jojoba), and a slow infusion process. This
              aromatic blend is perfect for relaxation or as a natural skincare
              boost.
            </p>
            <div class="share-buttons">
              <a
                href="https://facebook.com/sharer/sharer.php?u=#"
                target="_blank"
                class="btn btn-sm btn-primary"
                ><i class="fab fa-facebook-f"></i> Share</a
              >
              <a
                href="https://twitter.com/intent/tweet?url=#&text=DIY Tea-Infused Essential Oil Recipe"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            <div class="comment-section">
              <h5>Comments</h5>
              <div id="comments-3" class="mb-3"></div>
              <form class="comment-form" data-post="3">
                <div class="mb-3">
                  <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Write your comment..."
                    required
                  ></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                  Post Comment
                </button>
              </form>
            </div>
          </article>
        </div>
      </section>
    </main>
    <!-- footer -->
    <?php 
    include "view/footer.php";
    ?>