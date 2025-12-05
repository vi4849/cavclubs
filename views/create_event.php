<?php
require("connect-db.php");
require("request-db.php");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createEventBtn'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $month_date = $_POST['month_date'] ?? '';
    $day_date = $_POST['day_date'] ?? '';
    $year_date = $_POST['year_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $venue_id = $_POST['venue_id'] ?? '';
    $cio_id = $_POST['cio_id'] ?? '';
    $computing_ID = trim($_POST['computing_ID'] ?? '');

    //required fields check
    if (!$title || !$month_date || !$day_date || !$year_date || !$start_time || !$end_time || !$venue_id || !$cio_id || !$computing_ID) {
        $message = "Missing required fields. Please fill out all fields.";
    } 

    //enforcing end time must be after start time
    else if ($start_time >= $end_time) {
        $message = "End time must be after start time.";
    }

    //duplicate event check
    else {
        $stmt = $db -> prepare(
            "SELECT COUNT(*) FROM event
             WHERE title = :title
             AND month_date = :month_date
             AND day_date = :day_date
             AND year_date = :year_date
             AND start_time = :start_time
             AND end_time = :end_time
             AND cio_id = :cio_id"
        );
        
        $stmt -> execute([
            ':title' => $title,
            ':month_date' => $month_date,
            ':day_date' => $day_date,
            ':year_date' => $year_date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':cio_id' => $cio_id
        ]);

        if ($stmt -> fetchColumn() > 0){
            $message = "An event with the same title and time already exists for this organization.";
        }

        //venue/time conflict check
        else {
            $stmt = $db -> prepare(
                "SELECT COUNT(*) FROM event
                 WHERE venue_id = :venue_id
                 AND month_date = :month_date
                 AND day_date = :day_date
                 AND year_date = :year_date
                 AND :start_time < end_time
                 AND :end_time > start_time"
            );

            $stmt -> execute([
                ':venue_id' => $venue_id,
                ':month_date' => $month_date,
                ':day_date' => $day_date,
                ':year_date' => $year_date,
                ':start_time' => $start_time,
                ':end_time' => $end_time
            ]);

            if ($stmt -> fetchColumn() > 0){
                $message = "The selected venue is already booked during that specified date and time.";
            }

            //final insert
            else {
                try{
                    $stmt = $db -> prepare(
                        "INSERT INTO event (title, description, month_date, day_date, year_date, start_time, end_time, venue_id, cio_id, computing_ID)
                         VALUES (:title, :description, :month_date, :day_date, :year_date, :start_time, :end_time, :venue_id, :cio_id, :computing_ID)"
                    );

                    $stmt -> execute([
                        ':title' => $title,
                        ':description' => $description,
                        ':month_date' => $month_date,
                        ':day_date' => $day_date,
                        ':year_date' => $year_date,
                        ':start_time' => $start_time,
                        ':end_time' => $end_time,
                        ':venue_id' => $venue_id,
                        ':cio_id' => $cio_id,
                        ':computing_ID' => $computing_ID
                    ]);

                    $message = "Event created successfully.";
                } catch (PDOException $e) {
                    $message = "Failed to create event: " . htmlspecialchars($e->getMessage());
                }
            }
        }

    }
}

// Fetch venues and CIOs for select inputs
$venues = $db->query("SELECT venue_id, building_location, room_location FROM venue ORDER BY building_location ASC")->fetchAll(PDO::FETCH_ASSOC);
$cios = $db->query("SELECT cio_id, cio_name FROM cio ORDER BY cio_name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php require("base.php"); ?>

<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Create Event</h1>
            <p class="text-muted">Add a new campus event</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" action="" class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Event title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Event description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Month</label>
                        <input type="number" name="month_date" class="form-control" min="1" max="12" value="<?php echo htmlspecialchars($_POST['month_date'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Day</label>
                        <input type="number" name="day_date" class="form-control" min="1" max="31" value="<?php echo htmlspecialchars($_POST['day_date'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Year</label>
                        <input type="number" name="year_date" class="form-control" min="2024" value="<?php echo htmlspecialchars($_POST['year_date'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Start time</label>
                        <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($_POST['start_time'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End time</label>
                        <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($_POST['end_time'] ?? ''); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Venue</label>
                        <select name="venue_id" class="form-select" required>
                            <option value="">Select venue</option>
                            <?php foreach ($venues as $v): ?>
                                <option value="<?php echo htmlspecialchars($v['venue_id']); ?>" <?php echo (isset($_POST['venue_id']) && $_POST['venue_id'] == $v['venue_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v['building_location'] . (!empty($v['room_location']) ? (', ' . $v['room_location']) : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Organization (CIO)</label>
                        <select name="cio_id" class="form-select" required>
                            <option value="">Select organization</option>
                            <?php foreach ($cios as $c): ?>
                                <option value="<?php echo htmlspecialchars($c['cio_id']); ?>" <?php echo (isset($_POST['cio_id']) && $_POST['cio_id'] == $c['cio_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['cio_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Creator Computing ID</label>
                        <input type="text" name="computing_ID" class="form-control" placeholder="e.g., abc1yz" value="<?php echo htmlspecialchars($_POST['computing_ID'] ?? ''); ?>" required>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" name="createEventBtn" class="btn btn-dark">Create Event</button>
                            <a href="index.php?page=home" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="index.php?page=browse_events" class="btn btn-outline-secondary">Browse Events</a>
        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</body>

</html>