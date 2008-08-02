<?php

define('ZG_DB_DBSERVER', 'localhost');
define('ZG_DB_USERNAME', 'root');
define('ZG_DB_USERPASS', '');
define('ZG_DB_DATABASE', 'zg_test');
define('ZG_DB_CONFIGURATIONCACHE', 'configurationcache');
	
class testDatabase extends UnitTestCase
{

	function test_init()
	{
		$database = new zgDatabase();

		$this->assertNotNull($database);
    }

	function test_connect()
	{
		$database = new zgDatabase();
		$ret = $database->connect();
		$this->assertTrue($ret);
		unset($database);

		$database = new zgDatabase();
		$ret = $database->connect('localhost', 'root', '', 'zg_test');
		$this->assertTrue($ret);
    }

	function test_close()
	{
		$database = new zgDatabase();
		$ret = $database->close();
		$this->assertFalse($ret);
		unset($ret);
		unset($database);

		$database = new zgDatabase();
		$ret = $database->connect();
		$ret = $database->close();
		$this->assertTrue($ret);
		unset($ret);
		unset($database);

		$database = new zgDatabase();
		$ret = $database->connect('localhost', 'root', '', 'zg_test', true);
		$ret = $database->close();
		$this->assertFalse($ret);
    }
	
	function test_query()
	{
		$database = new zgDatabase();
		$ret = $database->connect();
		$ret = $database->query('false');
		$this->assertFalse($ret);

		$ret = $database->query('SELECT * FROM users');
		$this->assertTrue($ret);
		$ret = $database->close();
    }	
}

?>
