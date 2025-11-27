<?php
ob_start(); // starts output buffering so headers (like redirects) can be sent even if login.php outputs HTML
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

switch ($page) {
    case 'login':
        include('views/login.php');
        break;
    case 'home':
        include('views/home.php');
        break;
    case 'create_account':
        include('views/create_account.php');
        break;
    case 'update_profile':
        include('views/update_profile.php');
        break;
    case 'create_event':
        include('views/create_event.php');
        break;
    case 'browse_events':
        include('views/browse_events.php');
        break;
    case 'rsvp':
        include('views/rsvp.php');
        break;
    case 'rsvp_history':
        include('views/rsvp_history.php');
        break;
    case 'profile':
        include('views/profile.php');
        break;
    case 'about':
        include('views/about.php');
        break;
    case 'delete_user':
        include('views/delete_user.php');
        break;
    case 'signout':
        include('views/signout.php');
        break;
    default:
        echo "404 - Page not found";
        break;
}
