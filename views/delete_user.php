<?php
include 'connect-db.php'; // uses $db (PDO connection)

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;

    if (!$user_id) {
        $message = "Missing user ID.";
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $user_id]);
            $message = "User deleted successfully.";
        } catch (PDOException $e) {
            $message = "Error deleting user: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<h2>Delete User</h2>

<?php if (!empty($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>User ID:</label>
    <input type="number" name="user_id" required>
    <button type="submit">Delete User</button>
</form>

<p><a href="index.php?page=home">Back to Home</a></p>
