<?php
include 'connect-db.php'; // uses $db (PDO connection)

try {
    // matches our table's actual column names
    $query = "SELECT 
                e.event_id, 
                e.title, 
                e.description, 
                e.month_date, 
                e.day_date, 
                e.year_date, 
                e.start_time, 
                e.end_time, 
                v.venue_id, 
                c.name AS cio_name, 
                e.computing_ID
              FROM event e
              LEFT JOIN venue v ON e.venue_id = v.venue_id
              LEFT JOIN cio c ON e.cio_id = c.cio_id
              ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC";

    $stmt = $db->query($query);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error fetching events: " . htmlspecialchars($e->getMessage()) . "</p>";
    $events = [];
}
?>

<h2>Browse Events</h2>

<?php if (count($events) > 0): ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Venue ID</th>
            <th>CIO</th>
            <th>Created By</th>
        </tr>
        <?php foreach ($events as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <?php echo htmlspecialchars("{$row['month_date']}/{$row['day_date']}/{$row['year_date']}"); ?>
                </td>
                <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                <td><?php echo htmlspecialchars($row['venue_id']); ?></td>
                <td><?php echo htmlspecialchars($row['cio_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($row['computing_ID']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No events found.</p>
<?php endif; ?>

<p><a href="index.php?page=home">Back to Home</a></p>
