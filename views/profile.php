<?php require("connect-db.php"); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="CavClubs">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include("header.php") ?>
    <div class="container mt-4">
        <h1>Profile</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="images/default-avatar.png" alt="Avatar" class="img-thumbnail" style="width:160px;height:160px;object-fit:cover;">
                </div>
                <div class="col-md-9">
                    <h3><?php echo htmlspecialchars($_SESSION['username']); ?></h3>
                    <p><strong>Full name:</strong> <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Full Name'; ?></p>
                    <p><strong>Email:</strong> <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'email@example.com'; ?></p>
                    <p><strong>Bio:</strong> <?php echo isset($_SESSION['bio']) ? nl2br(htmlspecialchars($_SESSION['bio'])) : 'This user has not added a bio yet.'; ?></p>
                    <p><strong>Member since:</strong> <?php echo isset($_SESSION['member_since']) ? htmlspecialchars($_SESSION['member_since']) : 'N/A'; ?></p>
                    <p><strong>Clubs joined:</strong></p>
                    <ul>
                        <li>Club A (placeholder)</li>
                        <li>Club B (placeholder)</li>
                    </ul>
                    <a class="btn btn-primary" href="index.php?page=update_profile">Edit Profile</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Please <a href="index.php?page=login">log in</a> to view your profile.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>