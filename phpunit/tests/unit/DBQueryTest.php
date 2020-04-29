<?php


class DBQueryTest extends \PHPUnit\Framework\TestCase
{
    public function test_db_connect_with_correct_creds()
    {
        require_once dirname(dirname(__FILE__)).'../../../database/dbconfig.php';
        
        echo dirname(dirname(__FILE__)).'../../../database/dbconfig.php';

        $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
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
