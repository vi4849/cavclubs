<?php require("connect-db.php"); ?>
<?php require("request-db.php"); ?>
<?php
// Ensure session is started so we can set session variables on successful login
if (session_status() == PHP_SESSION_NONE) {
  session_start();
  $_SESSION = array();
}
$isExistingUser = null;
$loginStatus = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!empty($_POST['submitBtn'])) {
    $isExistingUser = checkIfUserExists($_POST['computingid'], $_POST['password']);
    if (!empty($isExistingUser)) {
      // Store useful user info in session
      $_SESSION['username'] = $isExistingUser['computing_ID'];
      $_SESSION['full_name'] = trim(($isExistingUser['first_name'] ?? '') . ' ' . ($isExistingUser['last_name'] ?? ''));
      $_SESSION['email'] = $isExistingUser['email'] ?? '';
      
      if(isCIOExecutive($_POST['computingid']))
        $_SESSION['user_type'] = "cio_exec";
      else
        $_SESSION['user_type'] = "student";

      header("Location: index.php?page=home");
      exit();
    } else {
      $loginStatus = "Login failed";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<?php require("base.php"); ?>

<body>
  <?php
  if (!empty($loginStatus)) {
    echo "<div class='alert alert-danger alert-dismissable'>Login failed. Incorrect computing ID or password.</div>";
  }
  ?>

  <div class="form-container">
    <div class="small-form-box">
      <h1>Sign in to CavClubs</h1>
      <form method="post" action="">
        <div class="mb-3">
          <label for="computingid" class="form-label">Computing ID</label>
          <input type="text" name="computingid" id="computingid" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" name="submitBtn" value="Submit" class="btn btn-primary w-100">Sign In</button>
      </form>

      <p class="mt-3 text-center">
        New to CavClubs? <a href="index.php?page=create_account">Create an account</a>.
      </p>
    </div>
  </div>

  <?php // include('footer.html') 
  ?>
</body>

</html>



