<?php
require("connect-db.php");
require("request-db.php");
session_start();

$current = $_SESSION['username'] ?? $_SESSION['computingid'] ?? null;
if (!$current) {
    header('Location: index.php?page=login');
    exit();
}

$message = '';
$error = '';

// handle delete of an RSVP — POST provides event_id and we use current user's computing ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_event_id'])) {
    $event_id = intval($_POST['delete_event_id']);
    try {
        deleteRsvp($current, $event_id);
        $message = 'RSVP deleted.';
    } catch (Exception $e) {
        $error = 'Failed to delete RSVP: ' . htmlspecialchars($e->getMessage());
    }
}

$rsvps = getRsvpsByUser($current);
?>

<!DOCTYPE html>
<html lang="en">
<?php require("base.php"); ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="fw-bold">My RSVPs</h1>
            <p class="text-muted">Your RSVP history and options to edit or remove responses.</p>
        </div>

        <?php if (!empty($message)): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

        <?php if (count($rsvps) === 0): ?>
            <div class="alert alert-info">You have no RSVPs yet. Browse events to RSVP.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($rsvps as $r): ?>
                    <div class="list-group-item d-flex align-items-start">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($r['title']); ?></h5>
                            <p class="mb-1 text-muted"><?php echo htmlspecialchars("{$r['month_date']}/{$r['day_date']}/{$r['year_date']}"); ?></p>
                            <p class="mb-1"><strong>Status:</strong> <?php echo htmlspecialchars($r['status']); ?></p>
                            <p class="small text-muted">Responded: <?php echo htmlspecialchars($r['rsvp_timestamp']); ?></p>
                        </div>
                        <div class="ms-auto d-flex gap-2">
                            <a href="index.php?page=rsvp&event_id=<?php echo htmlspecialchars($r['event_id']); ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            <form method="post" action="" onsubmit="return confirm('Delete this RSVP?');">
                                <input type="hidden" name="delete_event_id" value="<?php echo htmlspecialchars($r['event_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php?page=browse_events" class="btn btn-secondary">Browse Events</a>
        </div>
    </div>
</body>

</html>