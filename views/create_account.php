<?php
require("connect-db.php");
require("request-db.php");

$errorMessage = null;
$list_of_cios = getAllCIONames();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!empty($_POST['submitBtn'])) {
    // Server-side validation
    $email_val = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password_val = isset($_POST['password']) ? $_POST['password'] : '';
    $dob_val = isset($_POST['dob']) ? $_POST['dob'] : '';
    // email must contain @
    if (strpos($email_val, '@') === false) {
      $errorMessage = 'Please enter a valid email address containing @.';
    }
    // password: at least one uppercase, one lowercase, one special char, length >= 1
    elseif (!preg_match('/[A-Z]/', $password_val) || !preg_match('/[a-z]/', $password_val) || !preg_match('/[^a-zA-Z0-9]/', $password_val) || strlen($password_val) < 1) {
      $errorMessage = 'Password must contain at least one uppercase letter, one lowercase letter, one special character, and be at least 1 character long.';
    }
    // dob must be a past date
    elseif (!empty($dob_val) && $dob_val >= date('Y-m-d')) {
      $errorMessage = 'Date of birth must be a past date.';
    }

    // If server-side validation passed, continue with uniqueness checks and creation
    if (empty($errorMessage)) {
      if (isUniqueEmail($_POST['email'])) {
        if (isUniqueComputingID($_POST['computingid'])) {
          try {
            createUser($_POST['firstname'], $_POST['lastname'], $_POST['computingid'], $_POST['email'], $_POST['year'], $_POST['dob'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zipcode'], $_POST['password']);
            if (!empty($_POST['phonenumbers']))
              insertMultiple('student_phone', $_POST['computingid'], 'phone_number', $_POST['phonenumbers']);
            if (!empty($_POST['minors']))
              insertMultiple('student_minor', $_POST['computingid'], 'minor_name', $_POST['minors']);
            if (!empty($_POST['majors']))
              insertMultiple('student_major', $_POST['computingid'], 'major_name', $_POST['majors']);
            if (!empty($_POST['cio_ids'])) {
              for ($i=0; $i<count($_POST['cio_ids']); $i++)
                addCIOExecutive($_POST['computingid'], $_POST['cio_ids'][$i], $_POST['cio_roles'][$i]);
              $_SESSION['user_type'] = "cio_exec";
            } else
              $_SESSION['user_type'] = "student";
            $_SESSION['username'] = $_POST['computingid'];
            $_SESSION['full_name'] = trim(($_POST['firstname'] ?? '') . ' ' . ($_POST['lastname'] ?? ''));
            $_SESSION['email'] = $_POST['email'] ?? '';
            $_SESSION['login_success'] = 'You have successfully created an account and logged in!'; #used to display a notification
            header("Location: index.php?page=home");
            exit();
          } catch (Exception $e) {
            $errorMessage = "Unable to create account, error was: " . $e->getMessage();
          }
        } else
          $errorMessage = "An account with the provided computing ID already exists.";
      } else
        $errorMessage = "An account with the provided email already exists.";
    }
  }
}
?>


<!DOCTYPE html>
<html>
<?php require("base.php"); ?>

<?php
if ($errorMessage != null) {
  echo "<div class='alert alert-danger alert-dismissable'>{$errorMessage}</div>";
}
?>

<body>
  <div class="form-container">
    <div class="large-form-box">
      <h1>Create an account</h1>
      <div id="clientErrorContainer"></div>
      <form id="createForm" method="post" action="">
        <div class="mb-3">
          <label for="firstname" class="form-label">First Name*</label>
          <input type="text" name="firstname" id="firstname" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="lastname" class="form-label">Last Name*</label>
          <input type="text" name="lastname" id="lastname" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="computingid" class="form-label">Computing ID*</label>
          <input type="text" name="computingid" id="computingid" class="form-control" required placeholder="abc1de">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email*</label>
          <input type="text" name="email" id="email" class="form-control" required placeholder="abc1de@virginia.edu">
        </div>
        <div id="phoneContainer">
          <div class="form-group">
            <label for="phonenumber1">Phone Number</label>
            <input type="text" class="form-control" id="phonenumber1" name="phonenumbers[]" placeholder="7031234567">
          </div>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px;" id="addPhone">+ Add Phone Number</button>
        <div class="mb-3">
          <br>
          <label class="form-label">Year*</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="year" id="year1" value="1" checked>
            <label class="form-check-label" for="year1">1</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="year" id="year2" value="2">
            <label class="form-check-label" for="year2">2</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="year" id="year3" value="3">
            <label class="form-check-label" for="year3">3</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="year" id="year4" value="4">
            <label class="form-check-label" for="year4">4</label>
          </div>
        </div>
        <div id="majorContainer">
          <div class="form-group">
            <label for="major1">Major</label>
            <input type="text" class="form-control" id="major1" name="majors[]">
          </div>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px;" id="addMajor">+ Add Major</button>
        <div id="minorContainer">
          <br>
          <div class="form-group">
            <label for="minor1">Minor</label>
            <input type="text" class="form-control" id="minor1" name="minors[]">
          </div>
        </div>
        <button type="button" class="btn btn-light btn-sm" style="margin-top: 5px; margin-bottom: 20px;" id="addMinor">+ Add Minor</button>

        <div class="mb-3">
          <label for="dob" class="form-label">Date of Birth*</label>
          <input type="date" name="dob" id="dob" class="form-control" required>
          <br>
        </div>
        <div class="mb-3">
          <p> <u> Address: </u> </p>
          <div>
            <div class="mb-3">
              <label for="street" class="form-label">Street*</label>
              <input type="text" name="street" id="street" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="city" class="form-label">City*</label>
              <input type="text" name="city" id="city" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="state" class="form-label">State*</label>
              <input type="text" name="state" id="state" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="zipcode" class="form-label">Zip Code*</label>
              <input type="text" name="zipcode" id="zipcode" class="form-control" required>
            </div>
            <div class="mb-3">
              <br>
              <label class="form-label">Are you an executive member of any CIOs?*</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cio_exec" id="cio_exec_yes" value="yes">
                <label class="form-check-label" for="cio_exec_yes">Yes</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="cio_exec" id="cio_exec_no" value="no" checked>
                <label class="form-check-label" for="cio_exec_no">No</label>
              </div>
              <div id="cioContainer">
                <br>
                <p> <u> Select the CIO(s) you are an executive member of and your role: </u> </p>
                <div class="form-group">
                    <label for="cio">CIO</label>
                    <select class="form-control" id="cio1_name" name="cio_ids[]" required></select>
                    <label for="cio1_role">Role</label>
                    <input type="text" class="form-control" id="cio1_role" name="cio_roles[]" placeholder="President" required>
                </div>
              </div>
              <button type="button" class="btn btn-light btn-sm" style="margin-top: 10px;" id="addCIO">+ Add CIO</button>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password*</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="submitBtn" value="Submit" class="btn btn-primary w-100">Create Account</button>
      </form>
    </div>
  </div>
  <br>
  <br>

  <?php // include('footer.html') 
  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script>
    const list_of_cios = <?php echo json_encode($list_of_cios); ?>; //convert php array into a javascript array (format: [{cio_id: 1, cio_name: "GWC", etc.}] )
    $(document).ready(function() {
      // Client-side form validation
      $('#createForm').on('submit', function(e) {
        // remove previous client errors
        $('#clientErrorContainer').empty();
        const errors = [];
        const email = $('#email').val().trim();
        const pwd = $('#password').val();
        const dob = $('#dob').val();
        if (email.indexOf('@') === -1) {
          errors.push('Please enter a valid email address containing @.');
        }
        if (!(/[A-Z]/.test(pwd) && /[a-z]/.test(pwd) && /[^a-zA-Z0-9]/.test(pwd) && pwd.length >= 1)) {
          errors.push('Password must contain at least one uppercase letter, one lowercase letter, one special character, and be at least 1 character long.');
        }
        if (dob) {
          const today = new Date().toISOString().split('T')[0];
          if (dob >= today) {
            errors.push('Date of birth must be a past date.');
          }
        }
        if (errors.length > 0) {
          e.preventDefault();
          const html = `<div class="alert alert-danger" role="alert">${errors.join('<br>')}</div>`;
          $('#clientErrorContainer').html(html);
          window.scrollTo({
            top: $('.large-form-box').offset().top - 10,
            behavior: 'smooth'
          });
        }
      });
      let phoneCount = 1;
      let majorCount = 1;
      let minorCount = 1;
      let cioCount = 1;

      if ($('#cio_exec_no').is(':checked')) {
        $('#cioContainer').hide();
        $('#addCIO').hide();
      }

      $('#addCIO').click(function() {
        cioCount++;
        const html = `
                  <div class="form-group mt-5">
                    <label for="cio${cioCount}">CIO ${cioCount}</label>
                    <select class="form-control" id="cio${cioCount}_name" name="cio_ids[]" required></select>
                    <label for="cio1_role">Role</label>
                    <input type="text" class="form-control" id="cio1_role" name="cio_roles[]" required>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-cio mt-2">Remove</button>
                  </div>
              `;
        $('#cioContainer').append(html);

        $.each(list_of_cios, function(index, cio) {
          const option = `<option value="${cio.cio_id}">${cio.cio_name}</option>`;
          $(`#cio${cioCount}_name`).append(option);
        });
      });

      $('#cioContainer').on('click', '.remove-cio', function() {
        $(this).closest('.form-group').remove();
      });

      $('input[name="cio_exec"]').change(function() {
        if ($(this).val() === 'yes') {
          $.each(list_of_cios, function(index, cio) {
            const html = `<option value="${cio.cio_id}">${cio.cio_name}</option>`;
            $('#cio1_name').append(html);
          });
          $('#cioContainer').slideDown();
          $('#addCIO').slideDown();
        } else {
          $('#cioContainer').slideUp();
          $('#addCIO').slideUp();
        }
      });


      $('#addPhone').click(function() {
        phoneCount++;
        const html = `
                  <div class="form-group mt-2 d-flex align-items-start" data-phone-index="${phoneCount}">
                      <div style="flex:1">
                        <label for="phonenumber${phoneCount}">Phone Number ${phoneCount}</label>
                        <input type="text" class="form-control" id="phonenumber${phoneCount}" name="phonenumbers[]">
                      </div>
                      <div class="ms-2" style="margin-top: 30px;">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-phone">Remove</button>
                      </div>
                  </div>
              `;
        $('#phoneContainer').append(html);
      });

      // Delegated handler to remove a phone input
      $('#phoneContainer').on('click', '.remove-phone', function() {
        $(this).closest('.form-group').remove();
      });

      $('#addMajor').click(function() {
        majorCount++;
        const html = `
                  <div class="form-group mt-2 d-flex align-items-start" data-major-index="${majorCount}">
                      <div style="flex:1">
                        <label for="major${majorCount}">Major ${majorCount}</label>
                        <input type="text" class="form-control" id="major${majorCount}" name="majors[]">
                      </div>
                      <div class="ms-2" style="margin-top: 30px;">
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
                        <label for="minor${minorCount}">Minor ${minorCount}</label>
                        <input type="text" class="form-control" id="minor${minorCount}" name="minors[]">
                      </div>
                      <div class="ms-2" style="margin-top: 30px;">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-minor">Remove</button>
                      </div>
                  </div>
              `;
        $('#minorContainer').append(html);
      });

      // Delegated handler to remove a minor input
      $('#minorContainer').on('click', '.remove-minor', function() {
        $(this).closest('.form-group').remove();
      });
    });
  </script>

</body>



</html>