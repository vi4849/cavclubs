<?php 
require("connect-db.php");
require("request-db.php");

$events = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword  = trim($_POST['keyword'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date     = trim($_POST['date'] ?? '');

    $events = searchEvents($keyword, $category, $date);
} else {
    $events = getEvents();
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
                  echo "<option value='".htmlspecialchars($cat['category_name'])."' $selected>".htmlspecialchars($cat['category_name'])."</option>";
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
    
    <?php if (count($events) > 0): ?>
      <div class="row g-4">
        <?php foreach ($events as $event): ?>
          <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
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
                    <?php echo htmlspecialchars($event['start_time']); ?>
                    – <?php echo htmlspecialchars($event['end_time']); ?>
                  </p>
                  <p class="mb-3">
                    <i class="bi bi-geo-alt"></i>
                    <?php echo htmlspecialchars($event['building_location'] ?? 'TBA'); ?>
                    <?php if (!empty($event['room_location'])): ?>
                      , <?php echo htmlspecialchars($event['room_location']); ?>
                    <?php endif; ?>
                  </p>

                  <a href="#" class="btn btn-dark w-100">RSVP</a>

                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center mt-4">No events found.</div>
    <?php endif; ?>

    <div class="text-center mt-5">
      <a href="index.php?page=home" class="btn btn-secondary">Back to Home</a>
    </div>

  </div>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</body>
</html>