<?php
require("connect-db.php");
require("request-db.php");


// Determine current user's computing ID from session
$computingID = null;
if (!empty($_SESSION['computingid'])) {
    $computingID = $_SESSION['computingid'];
} elseif (!empty($_SESSION['username'])) {
    $computingID = $_SESSION['username'];
    $_SESSION['computingid'] = $computingID; // keep compatibility
}

// If not logged in, redirect to login
if (empty($computingID)) {
    header('Location: index.php?page=login');
    exit();
}

$message = '';
$error = '';

// Load user to display
$user = getUserByComputingID($computingID);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Confirm deletion
    try {
        // Use helper to delete by computing ID
        deleteUser($computingID);

        // Destroy session and redirect to login
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        header('Location: index.php?page=login');
        exit();
    } catch (Exception $e) {
        $error = "Failed to delete account: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require("base.php"); ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="fw-bold">Delete Account</h1>
            <p class="text-muted">This action is permanent. Your account and all associated data will be removed.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mx-auto" style="max-width:720px;">
            <div class="card-body">
                <h5>Are you sure you want to delete your account?</h5>
                <?php if ($user): ?>
                    <p><strong>Computing ID:</strong> <?php echo htmlspecialchars($user['computing_ID']); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                <?php else: ?>
                    <p class="text-muted">User not found.</p>
                <?php endif; ?>

                <form method="post" action="" class="d-flex gap-2">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, delete my account</button>
                    <a href="index.php?page=profile" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

    </div>
</body>

</html>