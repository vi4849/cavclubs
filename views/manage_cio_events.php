<?php
require("connect-db.php");
require("request-db.php");

$computingid = $_SESSION['username'];

// Verify user is CIO exec
$query = "SELECT c.cio_id, c.cio_name 
          FROM cio c JOIN cio_executive ce 
               ON c.cio_id = ce.cio_id
          WHERE ce.computing_id = :cid";
$stmt = $db->prepare($query);
$stmt->execute([':cid' => $computingid]);
$cios = $stmt->fetchAll();

$isExec = count($cios) > 0;
if (!$isExec) {
    echo "<h3 style='margin-top:30px;text-align:center;color:red;'>You are not a CIO Executive</h3>";
    exit();
}

// build list of CIO ids
$cio_ids = array_column($cios, 'cio_id');
$placeholders = implode(',', array_fill(0, count($cio_ids), '?'));

$sql = "SELECT e.*, c.cio_name 
        FROM event e JOIN cio c ON e.cio_id = c.cio_id
        WHERE e.cio_id IN ($placeholders)
        ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC";

$stmt = $db->prepare($sql);
$stmt->execute($cio_ids);
$events = $stmt->fetchAll();

// helper for RSVP numbers
function eventRsvpCounts($id, $db) {
    $c = ["Going"=>0,"Maybe"=>0,"Not Going"=>0];
    $q = "SELECT status, COUNT(*) AS cnt FROM rsvp WHERE event_id = :id GROUP BY status";
    $s = $db->prepare($q);
    $s->execute([":id"=>$id]);
    foreach($s->fetchAll() as $r) $c[$r['status']] = $r['cnt'];
    return $c;
}
?>

<?php require("base.php"); ?>

<body class="bg-light">
<div class="container mt-5">

    <h2 class="mb-3">Manage CIO Events</h2>
    <p class="text-muted">You are an executive for:</p>

    <?php foreach($cios as $c): ?>
        <span class="badge bg-primary me-1"><?php echo htmlspecialchars($c['cio_name']); ?></span>
    <?php endforeach; ?>

    <hr>

    <?php if(count($events) == 0): ?>
        <div class="alert alert-info">No events yet for your CIOs.</div>
        <a href="index.php?page=browse_events" class="btn btn-primary">Browse Events</a>
    <?php else: ?>

    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>CIO</th>
                <th>RSVP Counts</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($events as $ev): 
                $r = eventRsvpCounts($ev['event_id'], $db);
                $date = "{$ev['month_date']}/{$ev['day_date']}/{$ev['year_date']}";
            ?>
            <tr>
                <td><?= htmlspecialchars($ev['title']) ?></td>
                <td><?= $date ?></td>
                <td><?= htmlspecialchars($ev['cio_name']) ?></td>
                <td>
                    <small>Going: <?= $r['Going'] ?></small><br>
                    <small>Maybe: <?= $r['Maybe'] ?></small><br>
                    <small>Not Going: <?= $r['Not Going'] ?></small>
                </td>
                <td>
                    <a href="index.php?page=browse_events" class="btn btn-sm btn-outline-primary">View</a>
                    <a href="index.php?page=rsvp&event_id=<?= $ev['event_id'] ?>" class="btn btn-sm btn-dark">View RSVPs</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>
</body>
</html>
