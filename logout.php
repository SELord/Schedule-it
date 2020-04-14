<?php
    session_start();
    session_unset();
    session_destroy();
    $_SESSION = array();
    header("Location: https://login.oregonstate.edu/idp/profile/cas/logout");
?>