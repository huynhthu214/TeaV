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
        <button type="submit" class="btn signup-btn">Send code</button>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">Back to login</a>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="verifyCodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="verifyCodeForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Verification Code</h5>
                </div>
                <div class="modal-body">
                    <label for="verificationCode" class="form-label">Verification Code</label>
                    <input type="text" name="verification_code" class="form-control" id="verificationCode" required />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php 
    include "view/footer.php";
?>
