<?php 
require("connect-db.php"); 
include("base.php"); //base.php contains header.php  

if (isset($_SESSION['login_success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['login_success'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';

    unset($_SESSION['login_success']);
}
?>
<!DOCTYPE html>
<html>
  <body class="bg-light">  
    <br>
    <div class="container mt-4">
      <h1 class="mb-4 text-center"> <b>Welcome to CavClubs! </b></h1>
      <p style="font-size: 18px;" class="text-muted" >CavClubs is a website that allows UVA students to easily browse and RSVP to upcoming events organized by CIOs on Grounds. The platform also allows CIO executive members to create events and estimate headcount for upcoming events. Through this centralized platform, we hope to make event discovery and attendance a streamlined experience! </p>
      <br><br>
      <div class="row justify-content-center g-4">
        <div class="col">
          <a href="index.php?page=browse_events" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4">
              <i class="bi bi-calendar-event fs-1 mb-3 text-dark"></i>
              <h5 class="card-title text-dark">Browse Events</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="index.php?page=rsvp_history" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4">
              <i class="bi bi-clock fs-1 mb-3 text-dark"></i>
              <h5 class="card-title text-dark">My RSVPs</h5>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="index.php?page=profile" class="text-decoration-none">
            <div class="card shadow-sm h-100 text-center p-4">
              <i class="bi bi-person-circle fs-1 mb-3 text-dark"></i>
              <h5 class="card-title text-dark">Profile</h5>
            </div>
          </a>
        </div>
        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'cio_exec'){ ?>
          <div class="col">
            <a href="index.php?page=create_event" class="text-decoration-none">
              <div class="card shadow-sm h-100 text-center p-4">
                <i class="bi bi-gear fs-1 mb-3 text-dark"></i>
                <h5 class="card-title text-dark">Create CIO Event</h5>
              </div>
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </body>
</html>