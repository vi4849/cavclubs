<?php require("connect-db.php"); ?>
<?php require("request-db.php"); ?>

<?php
$isExistingUser = null;
$loginStatus = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['submitBtn']))
  {
    $isExistingUser = checkIfUserExists($_POST['computingid'], $_POST['password']);
    if (!empty($isExistingUser)) {
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
  <head>
    <meta charset="utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Shriya, Vivian, Pallavi, & Rakshitha">
    <meta name="description" content="CS 4750 Final Project">
    <meta name="keywords" content="CS 4750, CIO, UVA">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">  
    <style>
      .login-box {
          width: 350px;                     /* set box width to 350 pixels */
          padding: 30px;                    /* add 30px of space inside the box around the content */
          border: 1px solid #ccc;           /* add a 1px gray border around the box */
          border-radius: 10px;              /* rounds the corners of the box by 10px */
          box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* add a subtle shadow below and around the box */
      }
      .login-box h1 {
          text-align: center;               
          margin-bottom: 25px;             
          font-size: 1.5rem;    /* set font size to be 1.5x the root font size */
      }
      .login-container {
        display: flex;
        justify-content: center;           /* center the login box horizontally */
        align-items: center;               /* center the login box vertically within the container */
        min-height: calc(100vh - 150px);  /* make container at least the full viewport height minus 150px (for header space) */
        padding-top: 40px;                 /* add extra space at the top (from header or page content) */
      }
    </style>
  </head>
  <body>  
    <?php include("header.php") ?> 

    <?php 
      if (!empty($loginStatus)) {
          echo "<div class='alert alert-danger'>Login failed. Please check your computing ID or password.</div>";
      }
    ?>

    <div class="login-container">
      <div class="login-box">
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

    <?php // include('footer.html') ?> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>