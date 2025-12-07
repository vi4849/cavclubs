<?php
function checkIfUserExists($computingID, $password)
{
    global $db;
    $query = "SELECT * FROM student WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    //adjust on hashing changes
    // If password_verify fails, write a small diagnostic to the error log so we can
    // inspect whether the stored value looks like a hash (prefix char and length)
    if ($user && password_verify($password, $user['password'])) {
        return $user; // valid credentials
    } else {
        if ($user) {
            $stored = $user['password'];
            $startsWithDollar = (strlen($stored) > 0 && $stored[0] === '$') ? 'yes' : 'no';
            $len = strlen($stored);
            error_log("[CavClubs] Password verify failed for {$computingID}. stored_password_starts_with_\"$\": {$startsWithDollar}; length={$len}");
        }
        return false; // invalid credentials
    }
}

function isCIOExecutive($computing_ID)
{
    global $db;
    $query = "SELECT 1 FROM cio_executive WHERE computing_ID = :computing_ID LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':computing_ID', $computing_ID);
    $statement->execute();

    $row = $statement->fetch(); // fetch 1 row, returns false if no row is fetched
    $statement->closeCursor();

    if ($row == false) return false;
    else return true;
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

    if ($row == false) return true;
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

    if ($row == false) return true; //if no row is returned, then the computing ID is a new / unique one
    else return false;
}

function createUser($firstname, $lastname, $computingid, $email, $year, $dob, $street, $city, $state, $zipcode, $password)
{
    global $db;
    // Hash the password before storing in db - application security implementation
    $hashed = password_hash($password, PASSWORD_DEFAULT);
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
        $statement->bindValue(':password', $hashed);
        $statement->execute();
        $statement->closeCursor();
    } catch (Exception $e) {
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
    } catch (Exception $e) {
        throw $e;
    }
}

//used for inserting multiple phone numbers / major / minors for a specific student baed on their computing ID
function insertMultiple($table, $computingID, $columnName, $values)
{
    global $db;

    $valueString = "";
    for ($i = 0; $i < count($values); $i++)
        $valueString .= "(:computing_ID, :value{$i}),";
    $valueString = rtrim($valueString, ','); //remove extra comma at the end

    //don't have to prevent sql injection via binding for column name + table since those values are set internally (see create_account.php)
    $query = "INSERT INTO $table (computing_ID, $columnName) VALUES {$valueString}";

    try {
        $statement = $db->prepare($query); //prevent sql injection
        $statement->bindValue(':computing_ID', $computingID);
        for ($i = 0; $i < count($values); $i++)
            $statement->bindValue(":value{$i}", $values[$i]);
        $statement->execute();
        $statement->closeCursor();
    } catch (Exception $e) {
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

function addCIOExecutive($computing_ID, $cio_id, $cio_role)
{
    global $db;
    $curr_year = date('Y');
    $role = $cio_role;

    //set default start and end dates to be 8/20 (so all term lengths are 1 year long)
    if (date('m') < 6) {
        $start_term = ($curr_year - 1) . "-08-20";
        $end_term = ($curr_year) . "-08-20";
    } else {
        $start_term = ($curr_year) . "-08-20";
        $end_term = ($curr_year + 1) . "-08-20";
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
    } catch (Exception $e) {
        throw $e;
    }
}

function getUserByComputingID($computingID)
{
    global $db;
    $query = "SELECT * FROM student WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $result;
}

function getPhoneNumbersByComputingID($computingID)
{
    global $db;
    $query = "SELECT * FROM student_phone WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_COLUMN);
    $statement->closeCursor();
    return $result;
}


function updateUserProfile($computingID, $first, $last, $email, $year, $street, $city, $state, $zip)
{
    global $db;
    $query = "UPDATE student
            SET first_name = :first, last_name = :last, email = :email, year = :year, street_address = :street,
                city_address = :city, state_address = :state, zipcode_address = :zip
            WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->bindValue(':first', $first);
    $statement->bindValue(':last', $last);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':year', $year);
    $statement->bindValue(':street', $street);
    $statement->bindValue(':city', $city);
    $statement->bindValue(':state', $state);
    $statement->bindValue(':zip', $zip);
    $statement->execute();
    $statement->closeCursor();
}

function deleteUser($computingID)
{
    global $db;
    $query = "DELETE FROM student WHERE computing_ID = :computingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':computingID', $computingID);
    $statement->execute();
    $statement->closeCursor();
}

function createEvent($title, $description, $month_date, $day_date, $year_date, $start_time, $end_time, $venue_id, $cio_id, $computing_ID)
{
    global $db;

    $query = "INSERT INTO Events (title, description, month_date, day_date, year_date, start_time, end_time, venue_id, cio_id, computing_ID)
            VALUES (:title, :description, :month_date, :day_date, :year_date, :start_time, :end_time, :venue_id, :cio_id, :computing_ID)";

    $statement = $db->prepare($query);
    $statement->bindValue(':title', $title);
    $statement->bindValue(':description', $description);
    $statement->bindValue(':month_date', $month_date);
    $statement->bindValue(':day_date', $day_date);
    $statement->bindValue(':year_date', $year_date);
    $statement->bindValue(':start_time', $start_time);
    $statement->bindValue(':end_time', $end_time);
    $statement->bindValue(':venue_id', $venue_id);
    $statement->bindValue(':cio_id', $cio_id);
    $statement->bindValue(':computing_ID', $computing_ID);

    $statement->execute();
    $statement->closeCursor();
}

// function getAllEvents()
// {
//     global $db;
//     $query = "SELECT * FROM event ORDER BY event_date DESC";
//     $statement = $db->prepare($query);
//     $statement->execute();
//     $results = $statement->fetchAll(PDO::FETCH_ASSOC);
//     $statement->closeCursor();
//     return $results;
// }

function getEvents($limit, $offset)
{
    global $db;

    $query = "
        SELECT e.*, c.cio_name, v.building_location, v.room_location, cat.category_name
        FROM event e
        JOIN cio c ON e.cio_id = c.cio_id
        JOIN venue v ON e.venue_id = v.venue_id
        LEFT JOIN part_of p ON c.cio_id = p.cio_id
        LEFT JOIN category cat ON p.category_id = cat.category_id
        ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countEvents()
{
    global $db;

    $query = "SELECT COUNT(*) as event_count FROM event";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['event_count'] ?? 0;
}

function searchEvents($keyword = '', $category = '', $date = '', $limit, $offset)
{
    global $db;

    $query = "
        SELECT DISTINCT e.*, 
                        c.cio_name, 
                        v.building_location, 
                        v.room_location,
                        cat.category_name
        FROM event e
        JOIN cio c ON e.cio_id = c.cio_id
        JOIN venue v ON e.venue_id = v.venue_id
        LEFT JOIN part_of p ON c.cio_id = p.cio_id
        LEFT JOIN category cat ON p.category_id = cat.category_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($keyword)) {
        $query .= "
            AND (
                e.title LIKE :kw
                OR e.description LIKE :kw
                OR c.cio_name LIKE :kw
            )
        ";
        $params[':kw'] = '%' . $keyword . '%';
    }

    if (!empty($category)) {
        $query .= " AND cat.category_name = :category";
        $params[':category'] = $category;
    }

    if (!empty($date)) {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if ($dt) {
            $query .= "
                AND e.year_date  = :yr
                AND e.month_date = :mo
                AND e.day_date   = :dy
            ";
            $params[':yr'] = (int)$dt->format('Y');
            $params[':mo'] = (int)$dt->format('n');
            $params[':dy'] = (int)$dt->format('j');
        }
    }

    $query .= " ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC
               ";

    if ($limit !== null && $offset !== null) {
        $query .= " LIMIT :limit OFFSET :offset ";
    }
    $stmt = $db->prepare($query);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    if ($limit !== null && $offset !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }


    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function countSearchEvents($keyword = '', $category = '', $date = '')
{
    global $db;

    $query = "
        SELECT COUNT(DISTINCT e.event_id) AS total
        FROM event e
        JOIN cio c ON e.cio_id = c.cio_id
        LEFT JOIN part_of p ON c.cio_id = p.cio_id
        LEFT JOIN category cat ON p.category_id = cat.category_id
        WHERE 1=1
    ";

    $params = [];

    if (!empty($keyword)) {
        $query .= " AND (e.title LIKE :kw OR e.description LIKE :kw OR c.cio_name LIKE :kw)";
        $params[':kw'] = '%' . $keyword . '%';
    }

    if (!empty($category)) {
        $query .= " AND cat.category_name = :category";
        $params[':category'] = $category;
    }

    if (!empty($date)) {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if ($dt) {
            $query .= " AND e.year_date = :yr AND e.month_date = :mo AND e.day_date = :dy";
            $params[':yr'] = (int)$dt->format('Y');
            $params[':mo'] = (int)$dt->format('n');
            $params[':dy'] = (int)$dt->format('j');
        }
    }

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

/* RSVP helpers */
function createRsvp($event_id, $computing_ID, $status)
{
    global $db;
    // Use MySQL INSERT ... ON DUPLICATE KEY UPDATE to upsert based on composite PK (computing_ID,event_id)
    $query = "INSERT INTO rsvp (computing_ID, event_id, status, rsvp_timestamp) VALUES (:computing_ID, :event_id, :status, NOW()) ON DUPLICATE KEY UPDATE status = VALUES(status), rsvp_timestamp = NOW()";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':computing_ID', $computing_ID);
        $stmt->bindValue(':event_id', $event_id);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        $stmt->closeCursor();
        return true;
    } catch (Exception $e) {
        error_log("[CavClubs] createRsvp failed: " . $e->getMessage());
        return false;
    }
}

function getRsvpsByUser($computing_ID)
{
    global $db;
    // select fields that actually exist in the rsvp table + event meta
    $query = "SELECT r.computing_ID, r.event_id, r.status, r.rsvp_timestamp, e.title, e.month_date, e.day_date, e.year_date FROM rsvp r JOIN event e ON r.event_id = e.event_id WHERE r.computing_ID = :computing_ID ORDER BY r.rsvp_timestamp DESC";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':computing_ID', $computing_ID);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    } catch (Exception $e) {
        error_log("[CavClubs] getRsvpsByUser failed: " . $e->getMessage());
        return [];
    }
}

function getRsvpByUserAndEvent($computing_ID, $event_id)
{
    global $db;
    $query = "SELECT * FROM rsvp WHERE computing_ID = :computing_ID AND event_id = :event_id LIMIT 1";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':computing_ID', $computing_ID);
        $stmt->bindValue(':event_id', $event_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    } catch (Exception $e) {
        error_log("[CavClubs] getRsvpByUserAndEvent failed: " . $e->getMessage());
        return null;
    }
}

// Update RSVP for composite key (computing_ID + event_id)
function updateRsvp($computing_ID, $event_id, $status)
{
    global $db;
    $query = "UPDATE rsvp SET status = :status, rsvp_timestamp = NOW() WHERE computing_ID = :computing_ID AND event_id = :event_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':computing_ID', $computing_ID);
        $stmt->bindValue(':event_id', $event_id);
        $stmt->execute();
        $stmt->closeCursor();
        return true;
    } catch (Exception $e) {
        error_log("[CavClubs] updateRsvp failed: " . $e->getMessage());
        return false;
    }
}

function deleteRsvp($computing_ID, $event_id)
{
    global $db;
    $query = "DELETE FROM rsvp WHERE computing_ID = :computing_ID AND event_id = :event_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(':computing_ID', $computing_ID);
        $stmt->bindValue(':event_id', $event_id);
        $stmt->execute();
        $stmt->closeCursor();
        return true;
    } catch (Exception $e) {
        error_log("[CavClubs] deleteRsvp failed: " . $e->getMessage());
        return false;
    }
}

function searchCIOEvents($cioIds, $keyword = '', $date = '')
{
    global $db;

    $inClause = implode(',', array_fill(0, count($cioIds), '?'));

    $query = "
        SELECT e.*, c.cio_name
        FROM event e
        JOIN cio c ON e.cio_id = c.cio_id
        WHERE e.cio_id IN ($inClause)
    ";

    $params = $cioIds;

    if (!empty($keyword)) {
        $query .= " AND (e.title LIKE ? OR e.description LIKE ? OR c.cio_name LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    if (!empty($date)) {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if ($dt) {
            $query .= " AND e.year_date = ? AND e.month_date = ? AND e.day_date = ?";
            $params[] = (int)$dt->format('Y');
            $params[] = (int)$dt->format('n');
            $params[] = (int)$dt->format('j');
        }
    }

    $query .= " ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}