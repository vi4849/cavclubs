<?php require("connect-db.php"); ?>
<?php require("request-db.php"); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['submitBtn']))
  {
    if (createUser($_POST['firstname'], $_POST['lastname'], $_POST['computingid'], $_POST['email'], $_POST['year'], $_POST['dob'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zipcode'], $_POST['password']))
    {
        header("Location: home.php");
        exit();
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

    <h1>Create an account</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        First Name: <input type="text" name="firstname" class="form-control" required /> <br /> 
        Last Name: <input type="text" name="lastname" class="form-control" required /> <br /> 
        Computing ID: <input type="text" name="computingid" class="form-control" required /> <br /> 
        Email: <input type="text" name="email" class="form-control" required /> <br /> 
        Year: <input type="text" name="year" class="form-control" required /> <br /> 
        Date Of Birth: <input type="text" name="dob" class="form-control" required /> <br /> 
        <p> Address: </p> <br /> 
        Street: <input type="text" name="street" class="form-control" required /> <br /> 
        City: <input type="text" name="city" class="form-control" required /> <br /> 
        State: <input type="text" name="state" class="form-control" required /> <br /> 
        Zipcode: <input type="text" name="zipcode" class="form-control" required /> <br /> 
        Password: <input type="text" name="password" class="form-control" required /> <br /> 
        <input type="submit" name="submitBtn" value="Submit" class="btn btn-primary" required />
    </form>

    <?php // include('footer.html') ?> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>

  

</html>