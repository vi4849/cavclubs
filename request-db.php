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

function isUniqueEmail($email) 
{
    global $db;
    $query = "SELECT 1 FROM student WHERE email = :email LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    
    $row = $statement->fetch(); // fetch 1 row, returns false if no row is fetched
    $statement->closeCursor();

    if ($row==false) return true;
    else return false;
}


function isUniqueComputingID($computingID) 
{
    global $db;
    $query = "SELECT 1 FROM student WHERE computing_ID = :computingID LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    
    $row = $statement->fetch(); // fetch 1 row, returns false if no row is fetched
    $statement->closeCursor();

    if ($row==false) return true; //if no row is returned, then the computing ID is a new / unique one
    else return false;
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
    }
    catch (Exception $e) {
        throw $e; 
    }
}

function addPhoneNumber($computing_ID, $phone_number) 
{
    global $db;
    $query = "INSERT INTO student_phone (computing_ID, phone_number) VALUES (:computing_ID, :phone_number)";
    try {
        $statement = $db->prepare($query); //prevent sql injection
        $statement->bindValue(':computing_ID', $computing_ID);
        $statement->bindValue(':phone_number', $phone_number);
        $statement->execute();
        $statement->closeCursor(); 
    }
    catch (Exception $e) {
        throw $e; 
    }
}

//used for inserting multiple phone numbers / major / minors for a specific student baed on their computing ID
function insertMultiple($table, $computingID, $columnName, $values) {
    global $db;

    $valueString = "";
    for($i=0;$i<count($values);$i++)
        $valueString .= "(:computing_ID, :value{$i}),";
    $valueString = rtrim($valueString, ','); //remove extra comma at the end

    //don't have to prevent sql injection via binding for column name + table since those values are set internally (see create_account.php)
    $query = "INSERT INTO $table (computing_ID, $columnName) VALUES {$valueString}";

    try {
        $statement = $db->prepare($query); //prevent sql injection
        $statement->bindValue(':computing_ID', $computingID);
        for($i=0;$i<count($values);$i++)
            $statement->bindValue(":value{$i}", $values[$i]);
        $statement->execute();
        $statement->closeCursor(); 
    }
    catch (Exception $e) {
        throw $e; 
    }
}

function getAllCIONames() 
{
    global $db;
    $query = "SELECT cio_id, cio_name FROM cio";
    $statement = $db->prepare($query); //prepare takes the query + compiles it
    $statement->execute(); //runs the query against the database / table
    $result = $statement->fetchAll(PDO::FETCH_ASSOC); // only returns an array with (cio_id, cio_name)
    $statement->closeCursor();
    return $result;
}

function addCIOExecutive($computing_ID, $cio_id) {
    global $db;
    $curr_year = date('Y');
    $role = "President";

    //set default start and end dates to be 8/20 (so all term lengths are 1 year long)
    if (date('m') < 6) {
        $start_term = ($curr_year-1) . "-08-20"; 
        $end_term = ($curr_year) . "-08-20"; 
    }
    else {
        $start_term = ($curr_year) . "-08-20"; 
        $end_term = ($curr_year+1) . "-08-20"; 
    }
    

    $query = "INSERT INTO cio_executive (computing_ID, cio_id, start_term, end_term, role) VALUES (:computing_ID, :cio_id, :start_term, :end_term, :role)";
    try {
        $statement = $db->prepare($query); //prevent sql injection
        $statement->bindValue(':computing_ID', $computing_ID);
        $statement->bindValue(':cio_id', $cio_id);
        $statement->bindValue(':start_term', $start_term);
        $statement->bindValue(':end_term', $end_term);
        $statement->bindValue(':role', $role);
        $statement->execute();
        $statement->closeCursor(); 
    }
    catch (Exception $e) {
        throw $e; 
    }
}

function getUserByComputingID($computingID) {
  global $db;
  $query = "SELECT * FROM student WHERE computing_ID = :computingID";
  $statement = $db->prepare($query);
  $statement->bindValue(':computingID', $computingID);
  $statement->execute();
  $result = $statement->fetch(PDO::FETCH_ASSOC);
  $statement->closeCursor();
  return $result;
}

function updateUserProfile($computingID, $first, $last, $email, $year, $city, $state, $zip) {
  global $db;
  $query = "UPDATE student
            SET first_name = :first, last_name = :last, email = :email, year = :year,
                city_address = :city, state_address = :state, zipcode_address = :zip
            WHERE computing_ID = :computingID";
  $statement = $db->prepare($query);
  $statement->bindValue(':computingID', $computingID);
  $statement->bindValue(':first', $first);
  $statement->bindValue(':last', $last);
  $statement->bindValue(':email', $email);
  $statement->bindValue(':year', $year);
  $statement->bindValue(':city', $city);
  $statement->bindValue(':state', $state);
  $statement->bindValue(':zip', $zip);
  $statement->execute();
  $statement->closeCursor();
}

function deleteUser($computingID) {
    global $db;
    $query = "DELETE FROM student WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    $statement->closeCursor();
}

function createEvent($eventName, $description, $location, $date, $time) {
    global $db;
    $query = "INSERT INTO events (event_name, description, location, event_date, event_time)
              VALUES (:eventName, :description, :location, :eventDate, :eventTime)";
    $statement = $db->prepare($query);
    $statement->bindValue(':eventName', $eventName);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':location', $location);
    $statement->bindValue(':eventDate', $date);
    $statement->bindValue(':eventTime', $time);
    $statement->execute();
    $statement->closeCursor();
}

function getAllEvents() {
    global $db;
    $query = "SELECT * FROM events ORDER BY event_date DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $results;
}


?>
