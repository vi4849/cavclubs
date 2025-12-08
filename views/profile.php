<?php 
require("connect-db.php"); 
require("request-db.php");
include("base.php"); 


if (isset($_SESSION['notification_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['notification_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';

    unset($_SESSION['notification_message']);
}

$user = getUserByComputingID($_SESSION['username']);
$user_phonenumbers = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_phone', 'phone_number');
if (!is_array($user_phonenumbers))
  $user_phonenumbers = [$user_phonenumbers];

$user_majors = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_major', 'major_name');
if (!is_array($user_majors))
  $user_majors = [$user_majors];

$user_minors = fetchMultiAttributeByComputingID($_SESSION['username'], 'student_minor', 'minor_name');
if (!is_array($user_minors))
  $user_minors = [$user_minors];

?>

<!DOCTYPE html>
<html>

<body class="bg-light">
    <div class="container py-5">
        <?php if (isset($_SESSION['username'])): ?>
            <div class="d-flex justify-content-center">
                <div class="card shadow-sm" style="max-width:720px; width:100%;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h3 class="mb-1"><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                                <p class="text-muted mb-1"><?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : ''; ?></p>
                                <?php if (isset($_SESSION['member_since'])): ?>
                                    <p class="text-muted small mb-0">Member since: <?php echo htmlspecialchars($_SESSION['member_since']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="btn-group">
                                <a href="index.php?page=update_profile" class="btn btn-primary btn-sm">Edit Profile</a>
                                <a href="index.php?page=delete_user" class="btn btn-danger btn-sm">Delete Account</a>
                            </div>
                        </div>
                        <hr>
                        <p><strong>Email:</strong> <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'email@example.com'; ?></p>
                        <p><strong>Year:</strong> <?php echo isset($user['year']) ? htmlspecialchars($user['year']) : ''; ?></p>          
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars(date("m/d/Y", strtotime($user['DOB']))); ?></p>   
                        <p><strong>Address:</strong> <?php  echo htmlspecialchars($user['street_address']) . ', ' . htmlspecialchars($user['city_address']) . ', ' . htmlspecialchars($user['state_address']) . ' ' . htmlspecialchars($user['zipcode_address']);  ?></p>        
                        <p><strong>Phone Number(s):</strong>
                            <?php
                                if (!empty($user_phonenumbers)) {
                                    echo htmlspecialchars(implode(', ', $user_phonenumbers));
                                }
                            ?>
                        </p>    
                        <p><strong>Major(s):</strong>
                            <?php
                                if (!empty($user_majors)) {
                                    echo htmlspecialchars(implode(', ', $user_majors));
                                } 
                            ?>
                        </p>   
                        <p><strong>Minor(s):</strong>
                            <?php
                                if (!empty($user_minors)) {
                                    echo htmlspecialchars(implode(', ', $user_minors));
                                } 
                            ?>
                        </p>              
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Please <a href="index.php?page=login">log in</a> to view your profile.</div>
        <?php endif; ?>
    </div>
</body>

</html>