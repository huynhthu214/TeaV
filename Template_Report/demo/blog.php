
  <?php 
    $namePage = "Blog";
    include "view/header.php";
?>
    <main>
      <section class="blog py-5">
        <div class="container">
          <h1 class="text-center mb-4">Blog</h1>
          <div class="row" style="justify-content: center">
          <div class="col-sm-10 px-5">
              <!-- Blog Post 1 -->
              <article class="blog-post">
                <h2>The Health Benefits of Green Tea</h2>
                <p class="text-muted">Posted on March 26, 2025 by Trang Tea Team</p>
                <img
                  src="layout/images/graphic-1.jpg"
                  class="img-fluid-blog mb-3"
                  alt="Green Tea"
                />
                <p>
                  Green tea is renowned for its antioxidant properties, which help
                  combat free radicals in the body. Rich in catechins, it supports
                  heart health, boosts metabolism, and enhances mental focus. Learn
                  how a daily cup can transform your wellness routine.
                </p>
                <a href="#" class="read-more">Read more</a>
                <div class="share-buttons">
                  <a
                    href="https://facebook.com"
                    target="_blank"
                    class="btn btn-sm btn-primary-blog"
                    ><i class="fab fa-facebook-f"></i> Share</a
                  >
                  <a
                    href="https://x.com"
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
                  src="layout/images/graphic-3.jpg"
                  class="img-fluid-blog mb-3"
                  alt="Tea Brewing"
                />
                <p>
                  Brewing tea is an art. Use fresh water, steep at the right
                  temperature (80°C for green tea, 100°C for black tea), and time it
                  perfectly (2-3 minutes). Follow these steps to elevate your tea
                  experience.
                </p>
                <a href="#" class="read-more">Read more</a>
                <div class="share-buttons">
                  <a
                    href="https://facebook.com"
                    target="_blank"
                    class="btn btn-sm btn-primary-blog"
                    ><i class="fab fa-facebook-f"></i> Share</a
                  >
                  <a
                    href="https://x.com"
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
                  src="layout/images/chamomile-bliss-1.jpg"
                  class="img-fluid-blog mb-3"
                  alt="Essential Oil"
                />
                <p>
                  Create your own tea-infused essential oil with dried tea leaves,
                  carrier oil (like jojoba), and a slow infusion process. This
                  aromatic blend is perfect for relaxation or as a natural skincare
                  boost.
                </p>
                <a href="#" class="read-more">Read more</a>
                <div class="share-buttons">
                  <a
                    href="https://facebook.com"
                    target="_blank"
                    class="btn btn-sm btn-primary-blog"
                    ><i class="fab fa-facebook-f"></i> Share</a
                  >
                  <a
                    href="https://x.com"
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
            <div class="col-sm-2 ps-5">
              <div class="sidebar">
                <h3>Categories</h3>
                <ul>
                  <li><a href="#">Green Tea</a></li>
                  <li><a href="#">Herbal Tea</a></li>
                  <li><a href="#">Oolong Tea</a></li>
                  <li><a href="#">Black Tea</a></li>
                  <li><a href="#">Black Tea (Spiced)</a></li>
                </ul>
                <h3>Recent Posts</h3>
                <ul>
                  <li><a href="#">How to Brew Great Tea</a></li>
                  <li><a href="#">History of Organic Tea</a></li>
                  <li><a href="#">This Month's Promotions</a></li>
                </ul>
                <h3>Popular posts</h3>
                <ul>
                  <li>
                    <div class="row">
                      <div class="col-4">
                        <a href="#"><img src="layout/images/graphic-1.jpg" alt="" class="img-fluid-blog-min mb-3"></a>
                      </div>
                      <div class="col-8">
                        <a href="#" class="min-title">The Health Benefits of Green Tea</a>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="row">
                      <div class="col-4">
                        <img src="layout/images/chamomile-bliss-1.jpg" alt="" class="img-fluid-blog-min mb-3">
                      </div>
                      <div class="col-8">
                        <a href="#" class="min-title">How to Brew the Perfect Cup of Tea</a>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="row">
                      <div class="col-4">
                        <img src="layout/images/graphic-3.jpg" alt="" class="img-fluid-blog-min mb-3">
                      </div>
                      <div class="col-8">
                        <a href="#" class="min-title">DIY Tea-Infused Essential Oil Recipe</a>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <!-- footer -->
    <?php 
    include "view/footer.php";
    ?>