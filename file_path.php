<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    //Variable to be used in other files for webpage routing
    
    // Variable used on the production server. Uncomment for production 
    $FILE_PATH = "https://eecs.oregonstate.edu/education/scheduleit/";
    
    /* Variable used in development on OSU flip servers. 
    *  Replace $DEV_ONID with your onid. 
    *  Comment out code when moved to production server.*/
    //$DEV_ONID = "ohsa"; // for dev env
    //$FILE_PATH = "http://web.engr.oregonstate.edu/~" . $DEV_ONID . "/scheduleit/";  // for dev env

    //urlPrefix variable for use in assets/js/event.js and other javascript files where paths are used
    // Commented out because this introduced a bug where pages would not load
    //echo "<script>\n";
    //echo "var urlPrefix = " . json_encode($FILE_PATH) . ";";
    //echo "</script>";
?>
