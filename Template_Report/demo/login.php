
<?php 
    $namePage = "Login";
    include "view/header.php";

    $servername = "localhost";
    $username = "root";
    $password = "";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // prepare and bind
    $stmt = $conn->prepare("Select Email, Password from Customer where(?,?)");
    $stmt->bind_param("sss", $email, $password);

    // set parameters and execute


    echo "New records created successfully";

    $stmt->close();
    $conn->close();

    // require_once('db/account_db.php');
    // session_start();
    // if (isset($_SESSION['user'])) {
    //     header('Location: index.php');
    //     exit();
    // }

    // $error = '';

    // $user = '';
    // $pass = '';

    // if (isset($_POST['user']) && isset($_POST['pass'])) {
    //     $user = $_POST['user'];
    //     $pass = $_POST['pass'];

    //     if (empty($user)) {
    //         $error = 'Please enter your username';
    //     }
    //     else if (empty($pass)) {
    //         $error = 'Please enter your password';
    //     }
    //     else if (strlen($pass) < 6) {
    //         $error = 'Password must have at least 6 characters';
    //     }else{
    //         $result = login($user, $pass);
    //         if(gettype($result) == 'boolean'){
    //             $_SESSION['user'] = 'admin';
    //             $_SESSION['name'] = 'Mai Van Manh';

    //         }
    //     }
    //   }

?>


<!-- Login -->
<div class="container form-box mt-5">
    <form method="post">
      <h1 class="login text-center mb-4">Login</h1>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="Enter your email" />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter your password" />
      </div>

      <div class="mb-3 text-end">
        <a href="forgotpwd.php" id="forgotPassword" class="text-decoration-none">Forgot Password?</a>
      </div>

      <button type="submit" class="btn btn-outline-primary">Login</button>

      <div class="text-center mt-3">
        <p>Don't have an account? <a href="signup.php" id="openRegisterModal">Sign Up</a></p>
      </div>
    </form>
  </div>
</div>

<?php 
    include "view/footer.php";
?>
