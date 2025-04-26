<?php 
    $namePage = "Forgot Password";
    include "view/header.php";
?>

<div class="container form-box-forgot mt-5">
    <form id="sendCodeForm" method="post">
        <h1 class="forgotpwd text-center mb-4">Forgot Password</h1>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                id="email"
                placeholder="Enter your email"
                required />
        </div>
        <a href="confirm-mail.php" class="btn btn-outline-primary">Send code</a>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to login</a>
        </div>
    </form>


    </div>
</div> 


<?php 
    include "view/footer.php";
?>
