<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

/*
*    File name: project_lib.php
*    Purpose:   The intent of this file is to house the majority of php 
*               functions which can then be called from various php files,
*               after requiring this file in the calling file.
*/




/* ----------------------------------------------------------------------------
*    Function: is_user_logged_in
*    Description: Checks whether the session is set with loggedin.
*    Input: NONE
*    Output: boolean value of TRUE or FALSE based on if session is logged in 
-----------------------------------------------------------------------------*/
function is_user_logged_in(){
    session_start();  //resume existing session to access $_SESSION variable

    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {
        return FALSE;
    }
    else{
        return TRUE;
    }
}




/* ----------------------------------------------------------------------------
*    Function: route_use_tor_login
*    Description: Sends the user to the login page for OSU CAS authentication
*    Input: NONE
*    Output: User is routed to login.php
-----------------------------------------------------------------------------*/
function route_user_to_login(){
    header("Location: " . $FILE_PATH . "login.php");
}



?>
