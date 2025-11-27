<?php session_start(); ?>

<header>
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand  d-flex align-items-center" href="index.php?page=home">
        <img src="images/logo.png" alt="CavClubs Logo" width="40" height="40" class="d-inline-block align-text-top">
        <span style="color: #dbdbdb">CavClubs</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Navbar links: updated to point at view pages routed via index.php?page=... -->
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ms-auto flex-wrap">
          <!-- check if currently logged in, display Log out button 
               otherwise, display sign up and log in buttons -->
            <li class="nav-item">
              <a class="nav-link" href="index.php?page=about">About</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="index.php?page=browse_events">Browse Events</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=create_event">Create Event</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=rsvp_history">My RSVPs</a>
          </li>
            <?php if (!isset($_SESSION['username'])) { ?>
            <li class="nav-item">
              <a class="nav-link" href="index.php?page=login">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?page=create_account">Sign Up</a>
            </li>
          <?php  } else { ?>
            <li class="nav-item">
              <a class="nav-link" href="index.php?page=profile">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?page=signout">Logout</a>
            </li>
          <?php } ?>
          <li class="nav-item">
            <a class="nav-link" href="index.php?page=delete_user">Delete Account</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>