<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    session_start();
    session_unset();
    session_destroy();
    $_SESSION = array();
    header("Location: https://login.oregonstate.edu/idp/profile/cas/logout");
?>