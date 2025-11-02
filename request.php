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
      header("Location: home.php");
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
  </head>
  <body>  
    <?php include("header.php") ?> 

    <?php 
      if (!empty($loginStatus)) {
          echo "<div class='alert alert-danger'>Login failed. Please check your computing ID or password.</div>";
      }
    ?>

    <h1>Sign in to CavClubs</h1>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      Computing ID: <input type="text" name="computingid" class="form-control" /> <br /> 
      Password: <input type="text" name="password" class="form-control" /> <br /> 
      <input type="submit" name="submitBtn" value="Submit" class="btn btn-primary" />
    </form>

    <p>New to CavClubs? <a href="create_account.php"> Create an account.</a> </p>

    <?php // include('footer.html') ?> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>