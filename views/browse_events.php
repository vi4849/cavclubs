<?php
session_start();
require("connect-db.php");
require("request-db.php");

// helper to display times in 12-hour format with AM/PM
function format_time_for_display($timeStr) {
    if (empty($timeStr)) return '';
    // try to parse common formats
    $ts = strtotime($timeStr);
    if ($ts === false) {
        return htmlspecialchars($timeStr);
    }
    return date('g:i A', $ts);
}

$eventsPerPage = 12;
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($page - 1) * $eventsPerPage;

$totalEvents = 0;

$events = [];

$deleteMessage = '';
$deleteError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
    $delId = $_POST['delete_event_id'];
    $stmt = $db->prepare("SELECT computing_ID FROM event WHERE event_id = :id");
    $stmt->execute([':id' => $delId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        $deleteError = 'Event not found.';
    } else {
        $owner = $row['computing_ID'];
        $current = $_SESSION['username'] ?? $_SESSION['computingid'] ?? null;
        $isAdmin = !empty($_SESSION['is_admin']);
        if ($isAdmin || ($current && $current === $owner)) {
            try {
                $d = $db->prepare("DELETE FROM event WHERE event_id = :id");
                $d->execute([':id' => $delId]);
                $deleteMessage = 'Event deleted.';
            } catch (Exception $e) {
                $deleteError = 'Failed to delete event: ' . htmlspecialchars($e->getMessage());
            }
        } else {
            $deleteError = 'You do not have permission to delete this event.';
        }
    }
    $events = getEvents($eventsPerPage, $offset);
    $totalEvents = countEvents();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $keyword = trim($_POST['keyword'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $date = trim($_POST['date'] ?? '');

        $isBlankSearch =
            ($keyword === '' && $category === '' && $date === '');

        if ($isBlankSearch) {
            //treat blank search as normal browse with pagination
            $isSearch = false;
            $totalEvents = countEvents();
            $events = getEvents($eventsPerPage, $offset);
        } else {
            //search has actual filters -> NO pagination
            $isSearch = true;
            $events = searchEvents($keyword, $category, $date, null, null);
            $totalEvents = count($events);
        }

    } else {
        //normal browse with pagination
        $isSearch = false;
        $totalEvents = countEvents();
        $events = getEvents($eventsPerPage, $offset);
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<?php require("base.php"); ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Search & Browse</h1>
            <p class="text-muted">Discover events and organizations on campus</p>
        </div>

        <form method="POST" action="" class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <input
                            type="text"
                            class="form-control"
                            name="keyword"
                            placeholder="Search events, CIOs, or description..."
                            value="<?php echo htmlspecialchars($_POST['keyword'] ?? ''); ?>">
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <?php
                            $cats = $db->query("SELECT category_name FROM category ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($cats as $cat):
                                $selected = (isset($_POST['category']) && $_POST['category'] == $cat['category_name']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($cat['category_name']) . "' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <input
                            type="date"
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
        <?php if (!empty($deleteMessage)): ?>
            <div class="alert alert-success text-center"><?php echo htmlspecialchars($deleteMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($deleteError)): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($deleteError); ?></div>
        <?php endif; ?>

        <?php if (count($events) > 0): ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 position-relative">
                            <?php
                            //show delete button only to owner or admin
                            $current = $_SESSION['username'] ?? $_SESSION['computingid'] ?? null;
                            $isAdmin = !empty($_SESSION['is_admin']);
                            if ($current && ($isAdmin || $current === $event['computing_ID'])): ?>
                                <form method="post" action="" onsubmit="return confirm('Delete this event? This cannot be undone.');" class="position-absolute" style="right:0; top:0; z-index:10;">
                                    <input type="hidden" name="delete_event_id" value="<?php echo htmlspecialchars($event['event_id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger m-2" title="Delete event">&times;</button>
                                </form>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">

                                <span class="badge bg-secondary text-uppercase mb-2">
                                    <?php echo htmlspecialchars($event['category_name'] ?? 'General'); ?>
                                </span>

                                <h5 class="card-title fw-bold">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </h5>

                                <p class="text-muted mb-2">
                                    <?php echo htmlspecialchars($event['cio_name'] ?? 'Unknown Organization'); ?>
                                </p>

                                <div class="mt-auto">
                                    <p class="mb-1">
                                        <i class="bi bi-calendar-event"></i>
                                        <?php echo htmlspecialchars("{$event['month_date']}/{$event['day_date']}/{$event['year_date']}"); ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-clock"></i>
                                            <?php echo htmlspecialchars(format_time_for_display($event['start_time'])); ?>
                                            – <?php echo htmlspecialchars(format_time_for_display($event['end_time'])); ?>
                                    </p>
                                    <p class="mb-3">
                                        <i class="bi bi-geo-alt"></i>
                                        <?php echo htmlspecialchars($event['building_location'] ?? 'TBA'); ?>
                                        <?php if (!empty($event['room_location'])): ?>, <?php echo htmlspecialchars($event['room_location']); ?>
                                    <?php endif; ?>
                                    </p>

                                    <a href="index.php?page=rsvp&event_id=<?php echo htmlspecialchars($event['event_id']); ?>" class="btn btn-dark w-100">RSVP</a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center mt-4">No events found.</div>
        <?php endif; ?>

        <?php
        $totalPages = ceil($totalEvents / $eventsPerPage);

        if (!$isSearch && $totalPages > 1):
        ?>
            <nav>
                <ul class="pagination justify-content-center mt-4">

                    <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=browse_events&p=<?php echo $page - 1; ?>">Previous</a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=browse_events&p=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=browse_events&p=<?php echo $page + 1; ?>">Next</a>
                    </li>

                </ul>
            </nav>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="index.php?page=home" class="btn btn-secondary">Back to Home</a>
        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</body>

</html>