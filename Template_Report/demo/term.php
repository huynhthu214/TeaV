
<!-- Header/Navbar -->
<?php 
        $namePage = "Terms & Conditions";
        include "view/header.php";

        $conn = mysqli_connect("localhost", "root", "", "teav_shop1");

        if (!$conn) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }
        
        $term_query = "SELECT 
                    term.TermId,
                    term.Email,
                    term.Title,
                    term.Content,
                    term.ImgUrl AS img_term,
                    term.DateUpload
                  FROM term
                  WHERE term.IsShow = 'Yes'";
        
        
        $result = mysqli_query($conn, $term_query);
        
        if (!$result) {
            die("Kết nối thất bại: " . mysqli_error($conn));
        }    
    ?>
    <main>
      <div class="container">
        <section class="term py-5">
          <?php 
             while($term = mysqli_fetch_assoc($result)){
              echo "<h1 style='text-align: center;'>" .$term['Title'] . "</h1>";
              echo "<p>" .$term['Content'] . "</p>";
              echo "<img src='" .$term['img_term'] . "' alt='" . $term['Title'] . "' />";
              echo "<p><small>Uploaded on: " . $term['DateUpload'] . "</small></p>";
              echo "<p><strong>Contact us:</strong> " . $term['Email'] . "</p>";
        }
        ?>
        </section>
      </div>
    </main>
    <?php 
    include "view/footer.php";
?>