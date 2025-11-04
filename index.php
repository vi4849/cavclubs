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
    default:
        echo "404 - Page not found";
        break;
}
?>
