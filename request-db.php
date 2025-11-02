<?php
function checkIfUserExists($computingID, $password) 
{
    global $db;
    $query = "SELECT * FROM student WHERE computing_ID=:computingID AND password=:password";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->bindValue(':password', $password);
    $statement->execute(); 
    $result = $statement -> fetchAll();
    $statement->closeCursor();

    return $result;
}

function createUser($firstname, $lastname, $computingid, $email, $year, $dob, $street, $city, $state, $zipcode, $password) 
{
    global $db;
    $query = "INSERT INTO student (computing_ID, year, email, DOB, first_name, last_name, street_address, city_address, state_address, zipcode_address, password) VALUES (:computing_ID, :year, :email, :DOB, :first_name, :last_name, :street_address, :city_address, :state_address, :zipcode_address, :password)";
    try {
        $statement = $db->prepare($query); //prevent sql injection
        $statement->bindValue(':computing_ID', $computingid);
        $statement->bindValue(':year', $year);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':DOB', $dob);
        $statement->bindValue(':first_name', $firstname);
        $statement->bindValue(':last_name', $lastname);
        $statement->bindValue(':street_address', $street);
        $statement->bindValue(':city_address', $city);
        $statement->bindValue(':state_address', $state);
        $statement->bindValue(':zipcode_address', $zipcode);
        $statement->bindValue(':password', $password);
        $statement->execute();
        $statement->closeCursor(); 
        return true;
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
    catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

?>
