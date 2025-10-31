<?php
$config = require 'config.php';

$host = $config['DB_HOST']; 
$dbname = $config['DB_NAME'];
$username = $config['DB_USER'];
$password = $config['DB_PASS'];

$dsn = "mysql:host=$host;dbname=$dbname";

/** connect to the database **/
try 
{
   $db = new PDO($dsn, $username, $password);
   
   // display a message to let us know that we are connected to the database 
   echo "<p>You are connected to the database! </p>";
}
catch (PDOException $e)     
{
   $error_message = $e->getMessage();        
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       
{
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
}

?>