<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testDatabase extends UnitTestCase
{

	function test_init()
	{
		$database = new zgDatabase();
		$this->assertNotNull($database);
		unset($database);
    }


	// Test connect to database
	function test_connect()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$this->assertTrue($ret);
		unset($ret);

		unset($database);
    }


	function test_close()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		if (!defined('MULTITEST'))
		{
			$ret = $database->close();
			$this->assertTrue($ret);
		}
		
		unset($ret);
		unset($database);
    }	


	// Test an illegal query to the database
	function test_query_illegalquery()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$ret = $database->query('false');
		$this->assertFalse($ret);
		unset($ret);

		$database->close();
		unset($database);
    }


	// Test create, insert, select and drop
	function test_query_createinsertselectdrop()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$ret = $database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query("INSERT INTO test(id, test) VALUES('1', 'test')");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query("SELECT * FROM test");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query("DROP TABLE test");
		$this->assertTrue($ret);
		unset($ret);
		
		$database->close();
		unset($database);
    }


	// Test fetching array without query
	function test_fetchArray_withoutquery()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$ret = $database->fetchArray('');
		$this->assertFalse($ret);		
		unset($ret);

		$database->close();
		unset($database);
    }


	// Test fetching array
	function test_fetchArray()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $database->query("SELECT * FROM test");

		$ret = $database->fetchArray($res);
		$testarray = array('test' => 'test1', 'id' => '1');
		$this->assertEqual($ret, $testarray);
		unset($ret);

		$ret = $database->fetchArray($res);
		$testarray = array('test' => 'test2', 'id' => '2');
		$this->assertEqual($ret, $testarray);
		unset($ret);

		$database->query("DROP TABLE test");

		$database->close();
		unset($database);
    }

	// Test counting rows without query
	function test_numRows_withoutquery()
	{
		$database = new zgDatabase();
		$ret = $database->connect();
		
		$ret = $database->numRows('');
		$this->assertFalse($ret);
		unset($ret);

		$database->close();
		unset($database);
    }

	
	// Test counting rows
	function test_numRows()
	{
		$database = new zgDatabase();
		$ret = $database->connect();
		
		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $database->query("SELECT * FROM test");
		
		$ret = $database->numRows($res);
		$this->assertEqual($ret, 2);
		unset($ret);

		$database->query("DROP TABLE test");
		
		$database->close();
		unset($database);
    }
	
	
	// Test affected rows without query
	function test_affectedRows_withoutquery()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$ret = $database->affectedRows();
		$this->assertEqual($ret, 0);
		unset($ret);

		$database->close();
		unset($database);
    }
	
	
	// Test affected rows without query
	function test_affectedRows()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");

		$res = $database->query("DELETE FROM test");
		$ret = $database->affectedRows();
		$this->assertEqual($ret, 2);
		unset($ret);

		$database->query("DROP TABLE test");

		$database->close();
		unset($database);
    }

	
	// Test insert id
	function test_insertId()
	{
		$database = new zgDatabase();
		$ret = $database->connect();

		$database->query("CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), test VARCHAR(30))");
		$database->query("INSERT INTO test(test) VALUES('test1')");
		$database->query("INSERT INTO test(test) VALUES('test2')");

		$ret = $database->insertId();
		$this->assertEqual($ret, 2);
		unset($ret);

		$database->query("DROP TABLE test");

		$database->close();
		unset($database);		
    }

}

?>
