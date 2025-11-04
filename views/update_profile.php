<?php 
require("connect-db.php");
require("request-db.php");
session_start();

// Check if logged in
if (!isset($_SESSION['computingid'])) {
  header("Location: index.php?page=login");
  exit();
}

// Load current user info
$user = getUserByComputingID($_SESSION['computingid']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateBtn'])) {
  try {
    updateUserProfile(
      $_SESSION['computingid'],
      $_POST['first_name'],
      $_POST['last_name'],
      $_POST['email'],
      $_POST['year'],
      $_POST['city_address'],
      $_POST['state_address'],
      $_POST['zipcode_address']
    );
    $successMsg = "Profile updated successfully!";
    // reload updated user info
    $user = getUserByComputingID($_SESSION['computingid']);
  } catch (Exception $e) {
    $errorMsg = "Error updating profile: " . htmlspecialchars($e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html>
  <?php require("base.php"); ?>
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

          <button type="submit" name="updateBtn" class="btn btn-primary w-100">Save Changes</button>
        </form>
      </div>
    </div>
  </body>
</html>
