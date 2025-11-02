<?php session_start(); ?>

<header>  
  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">            
      <a class="navbar-brand  d-flex align-items-center" href="/"> 
        <img src="images/logo.png" alt="CavClubs Logo" width="40" height="40" class="d-inline-block align-text-top">
        <span style="color: #dbdbdb">CavClubs</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-controls="collapsibleNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- TODO: fill in placeholders + link pages once created  --> 
      <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ms-auto">
          <!-- check if currently logged in, display Log out button 
               otherwise, display sign up and log in buttons -->
          <?php if (!isset($_SESSION['username'])) { ?>              
            <li class="nav-item">
              <a class="nav-link" href="signin.php">Login</a>
            </li>              
          <?php  } else { ?>                    
            <li class="nav-item">                  
              <a class="nav-link" href="signout.php">Logout</a>
            </li>
          <?php } ?>
        
          <li class="nav-item">
            <a class="nav-link" href="#">Placeholder 1</a>
          </li>            
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="dropdown01" role="button" data-bs-toggle="dropdown" aria-expanded="false">Dropdown</a>
            <ul class="dropdown-menu" aria-labelledby="dropdown01">
              <li><a class="dropdown-item" href="#">1</a></li>
              <li><a class="dropdown-item" href="#">2</a></li>
              <li><a class="dropdown-item" href="#">3</a></li>
              <li><a class="dropdown-item" href="#">4</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Placeholder 2</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Placeholder 3</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>    