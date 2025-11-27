<?php
require("connect-db.php");
require("request-db.php");
session_start();

// must be logged in
$current = $_SESSION['username'] ?? $_SESSION['computingid'] ?? null;
if (!$current) {
    header('Location: index.php?page=login');
    exit();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if (!$event_id) {
    echo "Invalid event.";
    exit();
}

// fetch event details
$stmt = $db->prepare("SELECT * FROM event WHERE event_id = :id");
$stmt->execute([':id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$event) {
    echo "Event not found.";
    exit();
}

$existing = getRsvpByUserAndEvent($current, $event_id);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rsvp_submit'])) {
    $status = $_POST['status'] ?? 'Going';

    try {
        if ($existing) {
            // update using composite key (computing_ID + event_id)
            updateRsvp($current, $event_id, $status);
            $message = 'RSVP updated.';
        } else {
            createRsvp($event_id, $current, $status);
            $message = 'RSVP recorded.';
        }
        // reload existing
        $existing = getRsvpByUserAndEvent($current, $event_id);
    } catch (Exception $e) {
        $error = 'Failed to save RSVP: ' . htmlspecialchars($e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<?php require("base.php"); ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="mb-3">RSVP: <?php echo htmlspecialchars($event['title']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars("{$event['month_date']}/{$event['day_date']}/{$event['year_date']}"); ?></p>

                        <?php if (!empty($message)): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                        <?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                        <form method="post" action="">
                            <div class="mb-3">
                                <label class="form-label">Your response</label>
                                <select name="status" class="form-select">
                                    <?php $sel = $existing['status'] ?? 'Going'; ?>
                                    <option value="Going" <?php if ($sel == 'Going') echo 'selected'; ?>>Going</option>
                                    <option value="Maybe" <?php if ($sel == 'Maybe') echo 'selected'; ?>>Maybe</option>
                                    <option value="Not Going" <?php if ($sel == 'Not Going') echo 'selected'; ?>>Not Going</option>
                                </select>
                            </div>

                            <!-- The rsvp table currently stores only status and timestamp. -->

                            <div class="d-flex gap-2">
                                <button type="submit" name="rsvp_submit" class="btn btn-dark">Save RSVP</button>
                                <a href="index.php?page=browse_events" class="btn btn-secondary">Back</a>
                                <a href="index.php?page=rsvp_history" class="btn btn-outline-secondary ms-auto">My RSVPs</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>