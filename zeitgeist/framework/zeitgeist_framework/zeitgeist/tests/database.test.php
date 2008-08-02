<?php

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
		$database->connect();
		$ret = $database->close();
		$this->assertTrue($ret);
		unset($ret);
		unset($database);

		$database = new zgDatabase();
		$database->connect('localhost', 'root', '', 'zg_test', true);
		$ret = $database->close();
		$this->assertFalse($ret);
    }
	
	function test_query()
	{
		$database = new zgDatabase();
		$database->connect();
		$ret = $database->query('false');
		$this->assertFalse($ret);

		$ret = $database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query("INSERT INTO test(id, test) VALUES('1', 'test')");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query("SELECT * FROM test");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $database->query(" DROP TABLE test");
		$this->assertTrue($ret);

		$database->close();
    }

	function test_fetchArray()
	{
		$database = new zgDatabase();
		$database->connect();
		$ret = $database->fetchArray('');
		$this->assertFalse($ret);

		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $database->query("SELECT * FROM test");
		$ret = $database->fetchArray($res);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['test'], 'test1');
		$this->assertEqual($ret['id'], '1');
		unset($ret);

		$ret = $database->fetchArray($res);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['test'], 'test2');
		$this->assertEqual($ret['id'], '2');

		$database->query(" DROP TABLE test");
		$database->close();
    }

	function test_numRows()
	{
		$database = new zgDatabase();
		$database->connect();
		$ret = $database->numRows('');
		$this->assertFalse($ret);

		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $database->query("SELECT * FROM test");
		$ret = $database->numRows($res);
		$this->assertEqual($ret, 2);

		$database->query(" DROP TABLE test");
		$database->close();
    }

	function test_affectedRows()
	{
		$database = new zgDatabase();
		$database->connect();
		$ret = $database->affectedRows();
		$this->assertEqual($ret, 0);

		$database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $database->query("DELETE FROM test");
		$ret = $database->affectedRows();
		$this->assertEqual($ret, 2);

		$database->query(" DROP TABLE test");
		$database->close();	
    }
	
	function test_insertId()
	{
		$database = new zgDatabase();
		$database->connect();
		$ret = $database->insertId();
		$this->assertEqual($ret, 0);

		$database->query("CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), test VARCHAR(30))");
		$database->query("INSERT INTO test(test) VALUES('test1')");
		$database->query("INSERT INTO test(test) VALUES('test2')");
		$ret = $database->insertId();
		$this->assertEqual($ret, 2);

		$database->query(" DROP TABLE test");
		$database->close();	
    }
}

?>
