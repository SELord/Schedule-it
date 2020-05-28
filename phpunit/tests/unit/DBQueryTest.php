<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


class DBQueryTest extends \PHPUnit\Framework\TestCase
{
    public function test_db_connect_with_correct_creds()
    {
        require_once dirname(dirname(__FILE__)).'../../../database/dbconfig.php';
        
        echo dirname(dirname(__FILE__)).'../../../database/dbconfig.php';

        $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
        
        $this->assertEquals(0,$mysqli->connect_errno);
    }

    public function test_db_does_not_connect_with_bad_creds()
    {
        require_once dirname(dirname(__FILE__)).'../../../database/dbconfig.php';
        
        $mysqli = new mysqli($dbhost, $dbuser, "hello", $dbname);
        
        $this->assertNotEquals(0,$mysqli->connect_errno);
        
    }
/*
    public function testEmailHasUserAndDomain()
    {
        
    }

    public function testEmailIsRFC5322Compliant()
    {
        
    }
    */
}
