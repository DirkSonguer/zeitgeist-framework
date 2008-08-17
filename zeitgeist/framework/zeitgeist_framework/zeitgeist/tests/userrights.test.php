<?php

class testUserrights extends UnitTestCase
{
	public $database;

	function test_init()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();

		$userrights = new zgUserrights();
		$this->assertNotNull($userrights);
		unset($userrights);
    }

	function test_setUserrights()
	{
		$userrights = new zgUserrights();
		$res = $this->database->query("TRUNCATE TABLE userrights");
		
		$testrights = array();
		$testrights['1'] = true;
		$testrights['5'] = true;
		
		$ret = $userrights->setUserrights('1', $testrights);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM userrights");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		unset($userrights);
    }

	function test_getUserrights()
	{
		$userrights = new zgUserrights();
		$res = $this->database->query("TRUNCATE TABLE userrights");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('1', '1')");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('1', '5')");
		
		$res = $this->database->query("TRUNCATE TABLE userroles_to_users");
		$res = $this->database->query("TRUNCATE TABLE userroles_to_actions");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('2', '2')");
		$this->database->query("INSERT INTO userrights(userright_user, userright_action) VALUES('2', '6')");
		$this->database->query("INSERT INTO userroles_to_users(userroleuser_user, userroleuser_userrole) VALUES('2', '1')");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('1', '3')");
		$this->database->query("INSERT INTO userroles_to_actions(userroleaction_userrole, userroleaction_action) VALUES('1', '7')");
		
		$ret = $userrights->getUserrights('1');
		$this->assertEqual(count($ret), 2);
		$this->assertEqual($ret['1'], true);
		$this->assertEqual($ret['5'], true);
		unset($ret);

		$ret = $userrights->getUserrights('2');
		$this->assertEqual(count($ret), 4);
		$this->assertEqual($ret['2'], true);
		$this->assertEqual($ret['6'], true);
		$this->assertEqual($ret['3'], true);
		$this->assertEqual($ret['7'], true);
		unset($ret);

		unset($userrights);
    }

}

?>
