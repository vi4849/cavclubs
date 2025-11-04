<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';
    $location = $_POST['location'] ?? '';
    $created_by = $_POST['created_by'] ?? ''; // user_id of the creator

    if (!$title || !$date || !$location || !$created_by) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO events (title, description, date, location, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $date, $location, $created_by);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create event']);
    }

    $stmt->close();
    $conn->close();
}
?>
