<?php
include 'db_connect.php';

$query = "SELECT e.id, e.title, e.description, e.date, e.location, u.username AS created_by
          FROM events e
          JOIN users u ON e.created_by = u.id
          ORDER BY e.date ASC";

$result = $conn->query($query);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode(['status' => 'success', 'events' => $events]);

$conn->close();
?>
