    <?php 
        $namePage = "Cart";
        include "view/header.php";
    ?>
    <main>
        <section class="title-cart py-3">
            <div class="container">
                <h3>Cart</h3>
            </div>
        </section>
        <div class="container text-center py-5">
            <div class="row align-items-start">
              <div class="col">
                Product
              </div>
              <div class="col">
                Price
              </div>
              <div class="col">
                Quantity
              </div>
              <div class="col">
                Subtotal
              </div>
            </div>
          </div>
    </main>
    <?php
        include "view/footer.php";
    ?>