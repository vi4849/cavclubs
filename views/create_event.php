<?php
include 'connect-db.php'; // Use the same connection naming convention as other files

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';
    $location = $_POST['location'] ?? '';
    $created_by = $_POST['created_by'] ?? ''; // computing_ID or user_id of the creator

    if (!$title || !$date || !$location || !$created_by) {
        $message = "Missing required fields. Please fill out all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO events (title, description, date, location, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $date, $location, $created_by);

        if ($stmt->execute()) {
            $message = "Event created successfully!";
        } else {
            $message = "Failed to create event. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!-- Frontend UI -->
<h2>Create a New Event</h2>

<?php if (!empty($message)): ?>
    <p><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="4" cols="40"></textarea><br><br>

    <label>Date:</label><br>
    <input type="date" name="date" required><br><br>

    <label>Location:</label><br>
    <input type="text" name="location" required><br><br>

    <label>Created By (User ID):</label><br>
    <input type="number" name="created_by" required><br><br>

    <button type="submit">Create Event</button>
</form>

<p><a href="index.php?page=home">Back to Home</a></p>
