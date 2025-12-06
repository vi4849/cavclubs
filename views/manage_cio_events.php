<?php
require("connect-db.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$computingid = $_SESSION['username'];

$query = "SELECT c.cio_id, c.cio_name 
          FROM cio c 
          JOIN cio_executive ce ON c.cio_id = ce.cio_id
          WHERE ce.computing_id = :cid";
$stmt = $db->prepare($query);
$stmt->execute([':cid' => $computingid]);
$cios = $stmt->fetchAll();

$isExec = count($cios) > 0;
if (!$isExec) {
    echo "<h3 style='margin-top:30px;text-align:center;color:red;'>You are not a CIO Executive</h3>";
    exit();
}

$cio_ids = array_column($cios, 'cio_id');

function searchCIOEvents($cioIds, $keyword = '', $date = '')
{
    global $db;

    $inClause = implode(',', array_fill(0, count($cioIds), '?'));

    $query = "
        SELECT e.*, c.cio_name
        FROM event e
        JOIN cio c ON e.cio_id = c.cio_id
        WHERE e.cio_id IN ($inClause)
    ";

    $params = $cioIds;

    //keyword search
    if (!empty($keyword)) {
        $query .= " AND (e.title LIKE ? OR e.description LIKE ? OR c.cio_name LIKE ?)";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }

    //specific date
    if (!empty($date)) {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if ($dt) {
            $query .= " AND e.year_date = ? AND e.month_date = ? AND e.day_date = ?";
            $params[] = (int)$dt->format('Y');
            $params[] = (int)$dt->format('n');
            $params[] = (int)$dt->format('j');
        }
    }

    $query .= " ORDER BY e.year_date DESC, e.month_date DESC, e.day_date DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$isSearch = false;
$events = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $keyword = trim($_POST['keyword'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $blank = ($keyword === '' && $date === '');

    if ($blank) {
        foreach ($cio_ids as $cid) {
            $stmt = $db->prepare("CALL fetchEventsByCIO(?)");
            $stmt->execute([$cid]);
            $rows = $stmt->fetchAll();
            $events = array_merge($events, $rows);
            while ($stmt->nextRowset()) {}
            $stmt->closeCursor();
        }
    } else {
        $isSearch = true;
        $events = searchCIOEvents($cio_ids, $keyword, $date);
    }

} else {
    foreach ($cio_ids as $cid) {
        $stmt = $db->prepare("CALL fetchEventsByCIO(?)");
        $stmt->execute([$cid]);
        $rows = $stmt->fetchAll();
        $events = array_merge($events, $rows);
        while ($stmt->nextRowset()) {}
        $stmt->closeCursor();
    }
}

function eventRsvpCounts($id, $db) {
    $c = ["Going"=>0,"Maybe"=>0,"Not Going"=>0];
    $q = "SELECT status, COUNT(*) AS cnt 
          FROM rsvp 
          WHERE event_id = :id 
          GROUP BY status";
    $s = $db->prepare($q);
    $s->execute([":id"=>$id]);
    foreach($s->fetchAll() as $r) {
        $c[$r['status']] = $r['cnt'];
    }
    return $c;
}
?>

<?php require("base.php"); ?>

<body class="bg-light">
<div class="container mt-5">

    <h2 class="fw-bold">Manage CIO Events</h2>
    <p class="text-muted">You are an executive for:</p>

    <?php foreach($cios as $c): ?>
        <span class="badge bg-primary me-1"><?php echo htmlspecialchars($c['cio_name']); ?></span>
    <?php endforeach; ?>

    <hr>

    <form method="POST" action="" class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text"
                           class="form-control"
                           name="keyword"
                           placeholder="Search events or CIO name..."
                           value="<?php echo htmlspecialchars($_POST['keyword'] ?? ''); ?>">
                </div>

                <div class="col-md-5">
                    <input type="date"
                           class="form-control"
                           name="date"
                           value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Search</button>
                </div>
            </div>
        </div>
    </form>

    <?php if(count($events) == 0): ?>
        <div class="alert alert-info">No events found.</div>

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
                    
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>

</div>
</body>
</html>