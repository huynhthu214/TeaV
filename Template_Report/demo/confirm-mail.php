<?php 
    $namePage = "Confirm Email";
    include "view/header.php";
?>

<div class="container form-box-confirmmail mt-5">
    <form method="post">
      <h1 class="confirmmail text-center mb-4">Please check your inbox</h1>
        <p>Please enter the verification code we just sent to </p>
      <div class="mb-3">
        <label for="text" class="form-label">Code</label>
        <input type="text" class="form-control" id="text" />
      </div>
      <button type="submit" class="btn btn-outline-primary">Continue</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Resend code</a>
        </div>
    </form>
  </div>
</div>


<?php 
    include "view/footer.php";
?>

