<?php
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