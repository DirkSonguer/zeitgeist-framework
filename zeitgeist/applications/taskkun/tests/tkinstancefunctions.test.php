<?php

class testTkginstancefunctions extends UnitTestCase
{
	function test_init()
	{
		$instancefunctions = new tkInstancefunctions();
		$this->assertNotNull($instancefunctions);
		unset($instancefunctions);
    }

/*
	function test_connect()
	{
		$ret = $this->database->connect();
		$this->assertTrue($ret);
    }
	
	function test_query()
	{
		$ret = $this->database->query('false');
		$this->assertFalse($ret);

		$ret = $this->database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $this->database->query("INSERT INTO test(id, test) VALUES('1', 'test')");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $this->database->query("SELECT * FROM test");
		$this->assertTrue($ret);
		unset($ret);

		$ret = $this->database->query("DROP TABLE test");
		$this->assertTrue($ret);
    }

	function test_fetchArray()
	{
		$ret = $this->database->fetchArray('');
		$this->assertFalse($ret);

		$this->database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$this->database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $this->database->query("SELECT * FROM test");
		$ret = $this->database->fetchArray($res);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['test'], 'test1');
		$this->assertEqual($ret['id'], '1');
		unset($ret);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['test'], 'test2');
		$this->assertEqual($ret['id'], '2');

		$this->database->query(" DROP TABLE test");
    }

	function test_numRows()
	{
		$ret = $this->database->numRows('');
		$this->assertFalse($ret);

		$this->database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$this->database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $this->database->query("SELECT * FROM test");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$this->database->query(" DROP TABLE test");
    }

	function test_affectedRows()
	{
		$ret = $this->database->affectedRows();
		$this->assertEqual($ret, 0);

		$this->database->query("CREATE TABLE test(id INT, test VARCHAR(30))");
		$this->database->query("INSERT INTO test(id, test) VALUES('1', 'test1')");
		$this->database->query("INSERT INTO test(id, test) VALUES('2', 'test2')");
		$res = $this->database->query("DELETE FROM test");
		$ret = $this->database->affectedRows();
		$this->assertEqual($ret, 2);

		$this->database->query(" DROP TABLE test");
    }
	
	function test_insertId()
	{
		$this->database->query("CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), test VARCHAR(30))");
		$this->database->query("INSERT INTO test(test) VALUES('test1')");
		$this->database->query("INSERT INTO test(test) VALUES('test2')");
		$ret = $this->database->insertId();
		$this->assertEqual($ret, 2);

		$this->database->query(" DROP TABLE test");
    }
	
	function test_close()
	{
		$ret = $this->database->close();
		$this->assertTrue($ret);
		unset($ret);
    }
 */
}

?>
