<?php

class testTktaskfunctions extends UnitTestCase
{
	public $database;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();

		$taskfunctions = new tkTaskfunctions();
		$this->assertNotNull($taskfunctions);
		unset($taskfunctions);
    }

	function test_addTask()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE taskworkflows');

		$taskdata = array();
		$ret = $taskfunctions->addTask($taskdata);
		$this->assertFalse($ret);

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$ret = $taskfunctions->addTask($taskdata);
		$this->assertFalse($ret);

		$this->database->query("INSERT INTO taskworkflows(taskworkflow_title, taskworkflow_tasktype, taskworkflow_group, taskworkflow_order) VALUES('test1', '1', '1', '1')");
		$this->database->query("INSERT INTO taskworkflows(taskworkflow_title, taskworkflow_tasktype, taskworkflow_group, taskworkflow_order) VALUES('test1', '1', '2', '2')");
		$this->database->query("INSERT INTO taskworkflows(taskworkflow_title, taskworkflow_tasktype, taskworkflow_group, taskworkflow_order) VALUES('test1', '1', '3', '3')");

		$ret = $taskfunctions->addTask($taskdata);
		$this->assertTrue($ret);

/*
		$groupdata = array();
		$ret = $groupfunctions->addGroup($groupdata);
		$this->assertFalse($ret);

		$groupdata['group_description'] = 'this is a testgroup';
		$groupdata['group_name'] = 'test';
		$ret = $groupfunctions->addGroup($groupdata);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM groups");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['group_name'], 'test');
 */
		unset($taskfunctions);
    }

}

?>
