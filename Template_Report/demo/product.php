
<?php
      $namePage = "Products";
      include "view/header.php";
    ?>
    <main>
      <section class="products py-5">
        <div class="container">
          <h1 class="text-center mb-4">Our Tea Products</h1>

          <!-- Search and Filter Section -->
          <div class="filter-section">
            <div class="row g-3">
              <div class="col-md-4">
                <input
                  type="text"
                  class="form-control"
                  id="searchInput"
                  placeholder="Search by name..."
                />
              </div>
              <div class="col-md-3">
                <select class="form-select" id="herbalFilter">
                  <option value="">Filter by Herbal Type</option>
                  <option value="green">Green Tea</option>
                  <option value="herbal">Herbal Tea</option>
                  <option value="black">Black Tea</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" id="priceFilter">
                  <option value="">Filter by Price</option>
                  <option value="low">Under $10</option>
                  <option value="mid">$10 - $20</option>
                  <option value="high">Above $20</option>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" id="useFilter">
                  <option value="">Filter by Use</option>
                  <option value="relax">Relaxation</option>
                  <option value="energy">Energy</option>
                  <option value="digestion">Digestion</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Product List -->
          <div class="row" id="productList">
            <!-- Green Tea -->
            <div
              class="col-md-4 product-card"
              data-type="green"
              data-price="mid"
              data-use="energy"
            >
              <div class="card">
                <img
                  src="images/d119b63b9bd8b731694dec72d8f09236.jpg"
                  class="card-img-top"
                  alt="Green Tea"
                />
                <div class="card-body">
                  <h5 class="card-title">Premium Green Tea</h5>
                  <p class="card-text">
                    <strong>Ingredients:</strong> Organic green tea leaves<br />
                    <strong>Uses:</strong> Boosts energy, rich in
                    antioxidants<br />
                    <strong>Price:</strong> $15.00
                  </p>
                </div>
              </div>
            </div>
            <!-- Herbal Tea -->
            <div
              class="col-md-4 product-card"
              data-type="herbal"
              data-price="low"
              data-use="relax"
            >
              <div class="card">
                <img
                  src="images/5837da289e09a37a3439e6c176ea6df7.jpg"
                  class="card-img-top"
                  alt="Chamomile Tea"
                />
                <div class="card-body">
                  <h5 class="card-title">Chamomile Bliss</h5>
                  <p class="card-text">
                    <strong>Ingredients:</strong> Chamomile flowers, lavender<br />
                    <strong>Uses:</strong> Promotes relaxation and sleep<br />
                    <strong>Price:</strong> $8.00
                  </p>
                </div>
              </div>
            </div>
            <!-- Black Tea -->
            <div
              class="col-md-4 product-card"
              data-type="black"
              data-price="high"
              data-use="energy"
            >
              <div class="card">
                <img
                  src="images/6a3771cc2b2e3f060e1e6fec7d60344c.jpg"
                  class="card-img-top"
                  alt="Black Tea"
                />
                <div class="card-body">
                  <h5 class="card-title">Classic Black Tea</h5>
                  <p class="card-text">
                    <strong>Ingredients:</strong> Assam black tea leaves<br />
                    <strong>Uses:</strong> Enhances focus and energy<br />
                    <strong>Price:</strong> $25.00
                  </p>
                </div>
              </div>
            </div>
            <!-- Herbal Tea -->
            <div
              class="col-md-4 product-card"
              data-type="herbal"
              data-price="mid"
              data-use="digestion"
            >
              <div class="card">
                <img
                  src="images/e89b1971d52f87f48259ad41d671f028.jpg"
                  class="card-img-top"
                  alt="Peppermint Tea"
                />
                <div class="card-body">
                  <h5 class="card-title">Peppermint Refresh</h5>
                  <p class="card-text">
                    <strong>Ingredients:</strong> Peppermint leaves, ginger<br />
                    <strong>Uses:</strong> Aids digestion, refreshes<br />
                    <strong>Price:</strong> $12.00
                  </p>
                </div>
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