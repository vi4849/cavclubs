<?php
require("connect-db.php");
require("request-db.php");
require("base.php");

// Support both session keys 'computingid' (older) and 'username' (login sets this)
$computingID = null;
if (!empty($_SESSION['computingid'])) {
  $computingID = $_SESSION['computingid'];
} elseif (!empty($_SESSION['username'])) {
  $computingID = $_SESSION['username'];
  // keep backward compatibility by setting computingid
  $_SESSION['computingid'] = $computingID;
}

// Check if logged in
if (empty($computingID)) {
  header("Location: index.php?page=login");
  exit();
}

// Load current user info
$user = getUserByComputingID($computingID);

$user_phonenumbers = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_phone', 'phone_number');
if (!is_array($user_phonenumbers))
  $user_phonenumbers = [$user_phonenumbers];

$user_majors = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_major', 'major_name');
if (!is_array($user_majors))
  $user_majors = [$user_majors];

$user_minors = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_minor', 'minor_name');
if (!is_array($user_minors))
  $user_minors = [$user_minors];


$majorCount = 0;
$minorCount = 0;
$phoneNumberCount = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBtn'])) {
  try {
    // trim inputs
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $street = trim($_POST['street_address'] ?? '');
    $city = trim($_POST['city_address'] ?? '');
    $state = trim($_POST['state_address'] ?? '');
    $zip = trim($_POST['zipcode_address'] ?? '');

    updateUserProfile(
      $computingID,
      $first,
      $last,
      $email,
      $year,
      $street,
      $city,
      $state,
      $zip
    );


    if (!empty($_POST['phonenumbers']))
    {
      deleteMultiAttributes($_SESSION['username'], 'student_phone');
      $phonenumbers = array_values(array_filter($_POST['phonenumbers'], fn($m) => trim($m) !== ''));
      insertMultiple('student_phone', $_SESSION['username'], 'phone_number', $phonenumbers);
    }
    if (!empty($_POST['minors']))
    {
      deleteMultiAttributes($_SESSION['username'], 'student_minor');
      $minors = array_values(array_filter($_POST['minors'], fn($m) => trim($m) !== '')); //['CS', ''] -> ['CS'] (removes empty values)
      insertMultiple('student_minor', $_SESSION['username'], 'minor_name', $minors);
    }
    if (!empty($_POST['majors']))
    {
      deleteMultiAttributes($_SESSION['username'], 'student_major');
      $majors = array_values(array_filter($_POST['majors'], fn($m) => trim($m) !== '')); 
      insertMultiple('student_major', $_SESSION['username'], 'major_name', $majors);
    }

    // reload updated user info
    $user = getUserByComputingID($computingID);

    // update session display values
    $_SESSION['full_name'] = trim($first . ' ' . $last);
    $_SESSION['email'] = $email;

    $_SESSION['notification_message'] = 'Profile updated successfully!';

    header("Location: index.php?page=profile");
  } catch (Exception $e) {
    $errorMsg = "Error updating profile: " . htmlspecialchars($e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html>
<body>
  <div class="form-container">
    <div class="large-form-box">
      <h1>Update Profile</h1>

      <?php if (!empty($successMsg)) echo "<div class='alert alert-success'>$successMsg</div>"; ?>
      <?php if (!empty($errorMsg)) echo "<div class='alert alert-danger'>$errorMsg</div>"; ?>

      <form method="post" action="">
        <div class="mb-3">
          <label for="first_name" class="form-label">First Name</label>
          <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="last_name" class="form-label">Last Name</label>
          <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="year" class="form-label">Year</label>
          <input type="number" class="form-control" name="year" value="<?php echo htmlspecialchars($user['year']); ?>" min="1" max="4" required>
        </div>
        <div class="mb-3">
          <label for="street_address" class="form-label">Street</label>
          <input type="text" class="form-control" name="street_address" value="<?php echo htmlspecialchars($user['street_address']); ?>">
        </div>
        <div class="mb-3">
          <label for="city_address" class="form-label">City</label>
          <input type="text" class="form-control" name="city_address" value="<?php echo htmlspecialchars($user['city_address']); ?>">
        </div>
        <div class="mb-3">
          <label for="state_address" class="form-label">State</label>
          <input type="text" class="form-control" name="state_address" value="<?php echo htmlspecialchars($user['state_address']); ?>">
        </div>
        <div class="mb-3">
          <label for="zipcode_address" class="form-label">Zip Code</label>
          <input type="text" class="form-control" name="zipcode_address" value="<?php echo htmlspecialchars($user['zipcode_address']); ?>">
        </div>
        <div id="majorContainer">
          <p>Major(s):</p>
          <?php if (!empty($user_majors)): ?>
            <?php foreach ($user_majors as $major): ?>
                <div class="form-group mt-2 d-flex align-items-start">
                    <div style="flex:1">
                      <input type="text" class="form-control" id="major<?php $majorCount ?>" name="majors[]" value=<?= htmlspecialchars($major) ?>>
                    </div>
                    <div class="ms-2">
                      <button type="button" class="btn btn-outline-danger btn-sm remove-major">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group">
                    <input type="text" class="form-control" id="major1" name="majors[]">
                </div>
          <?php endif; ?>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px; margin-bottom: 10px;" id="addMajor">+ Add Major</button>
        <div id="minorContainer">
          <p>Minor(s):</p>
          <?php if (!empty($user_minors)): ?>
            <?php foreach ($user_minors as $minor): ?>
                <div class="form-group mt-2 d-flex align-items-start">
                    <div style="flex:1">
                      <input type="text" class="form-control" id="minor<?php $minorCount ?>" name="minors[]" value=<?= htmlspecialchars($minor) ?>>
                    </div>
                    <div class="ms-2">
                      <button type="button" class="btn btn-outline-danger btn-sm remove-minor">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group">
                    <input type="text" class="form-control" id="minor1" name="minors[]">
                </div>
          <?php endif; ?>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px; margin-bottom: 10px;" id="addMinor">+ Add Minor</button>
        <div id="phoneContainer">
          <p>Phone Number(s):</p>
          <?php if (!empty($user_phonenumbers)): ?>
            <?php foreach ($user_phonenumbers as $phoneNumber): ?>
                <div class="form-group mt-2 d-flex align-items-start">
                    <div style="flex:1">
                      <input type="text" class="form-control" id="phoneNumber<?php $phoneNumberCount ?>" name="phonenumbers[]" value=<?= htmlspecialchars($phoneNumber) ?>>
                    </div>
                    <div class="ms-2">
                      <button type="button" class="btn btn-outline-danger btn-sm remove-phoneNumber">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group">
                    <input type="text" class="form-control" id="phoneNumber1" name="phonenumbers[]">
                </div>
          <?php endif; ?>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px; margin-bottom: 20px;" id="addPhone">+ Add Phone Number</button>
        <button type="submit" name="updateBtn" class="btn btn-primary w-100">Save Changes</button>
      </form>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function() {
      let phoneCount = <?= $minorCount ?>;
      let majorCount = <?= $majorCount ?>;
      let minorCount = <?= $phoneNumberCount ?>;

      $('#addPhone').click(function() {
        phoneCount++;
        const html = `
                  <div class="form-group mt-2 d-flex align-items-start" data-phone-index="${phoneCount}">
                      <div style="flex:1">
                        <input type="text" class="form-control" id="phone${phoneCount}" name="phonenumbers[]">
                      </div>
                      <div class="ms-2">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-phone">Remove</button>
                      </div>
                  </div>
              `;
        $('#phoneContainer').append(html);
      });

      // Delegated handler to remove a major input
      $('#phoneContainer').on('click', '.remove-phone', function() {
        $(this).closest('.form-group').remove();
      });

      $('#addMajor').click(function() {
        majorCount++;
        const html = `
                  <div class="form-group mt-2 d-flex align-items-start" data-major-index="${majorCount}">
                      <div style="flex:1">
                        <input type="text" class="form-control" id="major${majorCount}" name="majors[]">
                      </div>
                      <div class="ms-2">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-major">Remove</button>
                      </div>
                  </div>
              `;
        $('#majorContainer').append(html);
      });

      // Delegated handler to remove a major input
      $('#majorContainer').on('click', '.remove-major', function() {
        $(this).closest('.form-group').remove();
      });

      $('#addMinor').click(function() {
        minorCount++;
        const html = `
                  <div class="form-group mt-2 d-flex align-items-start" data-minor-index="${minorCount}">
                      <div style="flex:1">
                        <input type="text" class="form-control" id="minor${minorCount}" name="minors[]">
                      </div>
                      <div class="ms-2">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-minor">Remove</button>
                      </div>
                  </div>
              `;
        $('#minorContainer').append(html);
      });

      // Delegated handler to remove a major input
      $('#minorContainer').on('click', '.remove-minor', function() {
        $(this).closest('.form-group').remove();
      });
    });
  </script>
</body>

</html>