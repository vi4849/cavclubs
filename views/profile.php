<?php 
require("connect-db.php"); 
include("base.php"); 

if (isset($_SESSION['notification_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['notification_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';

    unset($_SESSION['notification_message']);
}
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
                        
                        <!-- Full Address -->
                        <p><strong>Address:</strong><br>
                            <?php 
                            $addressParts = [];
                            if (!empty($_SESSION['street_address'])) $addressParts[] = htmlspecialchars($_SESSION['street_address']);
                            if (!empty($_SESSION['city_address'])) $addressParts[] = htmlspecialchars($_SESSION['city_address']);
                            if (!empty($_SESSION['state_address'])) $addressParts[] = htmlspecialchars($_SESSION['state_address']);
                            if (!empty($_SESSION['zipcode_address'])) $addressParts[] = htmlspecialchars($_SESSION['zipcode_address']);
                            echo !empty($addressParts) ? implode(", ", $addressParts) : "Address not provided.";
                            ?>
                        </p>

                        <!-- Major/Minor -->
                        <p><strong>Major(s):</strong>
                            <?php
                            if (!empty($_SESSION['majors'])) {
                                echo htmlspecialchars(implode(", ", $_SESSION['majors']));
                            } else {
                                echo "No majors added.";
                            }
                            ?>
                        </p>
                        <p><strong>Minor(s):</strong>
                            <?php
                            if (!empty($_SESSION['minors'])) {
                                echo htmlspecialchars(implode(", ", $_SESSION['minors']));
                            } else {
                                echo "No minors added.";
                            }
                            ?>
                        </p>

                        <!-- Phone Number -->
                        <p><strong>Phone number(s):</strong>
                            <?php
                            if (!empty($_SESSION['phone_numbers'])) {
                                echo htmlspecialchars(implode(", ", $_SESSION['phone_numbers']));
                            } else {
                                echo "No phone numbers added.";
                            }
                            ?>
                        </p>
                        
                        <p><strong>Bio:</strong><br><?php echo isset($_SESSION['bio']) ? nl2br(htmlspecialchars($_SESSION['bio'])) : 'This user has not added a bio yet.'; ?></p>
                        <p><strong>Clubs joined:</strong></p>
                        <ul>
                            <li>Club A (placeholder)</li>
                            <li>Club B (placeholder)</li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Please <a href="index.php?page=login">log in</a> to view your profile.</div>
        <?php endif; ?>
    </div>
</body>

</html>