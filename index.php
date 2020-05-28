<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    session_start();
    //If user is logged in, redirect to homepage
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"])
        header("Location: homepage.php");
    //If user is not logged in, redirect to login page
    else {
        session_destroy();
        header("Location: login.php");
    }
?>