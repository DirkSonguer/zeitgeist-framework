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
		$this->database->query('TRUNCATE TABLE workflows_to_groups');

		$ret = $taskfunctions->addTask(false);
		$this->assertFalse($ret);

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

		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");

		$ret = $taskfunctions->addTask($taskdata);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM tasks");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['task_name'], 'test');

		unset($taskfunctions);
    }

	function test_updateTask()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '3', '2')");

		$taskfunctions->addTask($taskdata);

		$ret = $taskfunctions->updateTask(false);
		$this->assertFalse($ret);
		
		$taskdata['task_id'] = '2';
		$ret = $taskfunctions->updateTask(false);
		$this->assertFalse($ret);

		$taskdata['task_id'] = '1';
		$taskdata['task_name'] = 'anothertest';
		$ret = $taskfunctions->updateTask($taskdata);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM tasks");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['task_name'], 'anothertest');

		unset($taskfunctions);
    }

	function test_deleteTask()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");

		$taskfunctions->addTask($taskdata);
		
		$ret = $taskfunctions->deleteTask(false);
		$this->assertFalse($ret);
		
		$ret = $taskfunctions->deleteTask(2);
		$this->assertFalse($ret);

		$ret = $taskfunctions->deleteTask(1);
		$this->assertTrue($ret);
		
		$res = $this->database->query("SELECT * FROM tasks");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($taskfunctions);
    }
	
	function test_getTaskInformation()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");

		$taskfunctions->addTask($taskdata);
		
		$ret = $taskfunctions->getTaskInformation(false);
		$this->assertFalse($ret);
		
		$ret = $taskfunctions->getTaskInformation(2);
		$this->assertFalse($ret);

		$ret = $taskfunctions->getTaskInformation(1);
		$this->assertTrue($ret);
		$this->assertEqual($ret['task_name'], 'test');

		unset($taskfunctions);
    }
	
	function test_getNumberofUsertasks()
	{
		$taskfunctions = new tkTaskfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');
		$this->database->query('TRUNCATE TABLE tasks_to_users');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test1';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");
		$taskfunctions->addTask($taskdata);

		$taskdata['task_name'] = 'test2';
		$taskfunctions->addTask($taskdata);

		$_SESSION['user_userid'] = '9';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);

		$this->database->query("INSERT INTO tasks_to_users(taskusers_task, taskusers_user) VALUES('1', '9')");		
		$ret = $taskfunctions->getNumberofUsertasks();
		$this->assertEqual($ret, 1);

		$this->database->query("INSERT INTO tasks_to_users(taskusers_task, taskusers_user) VALUES('2', '9')");
		$ret = $taskfunctions->getNumberofUsertasks();
		$this->assertEqual($ret, 2);
		
		unset($taskfunctions);
    }
	
	function test_getNumberofGrouptasks()
	{
		$taskfunctions = new tkTaskfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');
		$this->database->query('TRUNCATE TABLE tasks_to_users');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test1';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");
		$taskfunctions->addTask($taskdata);

		$taskdata['task_name'] = 'test2';
		$taskfunctions->addTask($taskdata);

		$_SESSION['user_userid'] = '9';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);

		$ret = $taskfunctions->getNumberofGrouptasks();
		$this->assertEqual($ret, 2);

		$this->database->query("INSERT INTO tasks_to_users(taskusers_task, taskusers_user) VALUES('2', '9')");
		$ret = $taskfunctions->getNumberofGrouptasks();
		$this->assertEqual($ret, 1);
		
		unset($taskfunctions);
    }
	
	function test_storeTags()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tags');
		$this->database->query('TRUNCATE TABLE tags_to_tasks');

		$ret = $taskfunctions->storeTags('test', 3);
		$this->assertFalse($ret);

		$ret = $taskfunctions->storeTags(false, 1);
		$this->assertFalse($ret);

		$ret = $taskfunctions->storeTags('test1,test2,test3', 1);
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM tags");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 3);
		
		unset($taskfunctions);
    }
	
	function test_acceptTask()
	{
		$taskfunctions = new tkTaskfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');
		$this->database->query('TRUNCATE TABLE tasks_to_users');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test1';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");
		$taskfunctions->addTask($taskdata);

		$_SESSION['user_userid'] = '9';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);

		$ret = $taskfunctions->acceptTask(false);
		$this->assertFalse($ret);

		$ret = $taskfunctions->acceptTask('3');
		$this->assertFalse($ret);

		$ret = $taskfunctions->acceptTask('1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM tasks_to_users");
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['taskusers_user'], '9');

		unset($taskfunctions);
    }
	
	function test_declineTask()
	{
		$taskfunctions = new tkTaskfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');
		$this->database->query('TRUNCATE TABLE tasks_to_users');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test1';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");
		$taskfunctions->addTask($taskdata);

		$_SESSION['user_userid'] = '9';
		$_SESSION['user_key'] = '1';
		$_SESSION['user_username'] = 'testuser';
		$_SESSION['user_instance'] = '1';
		$user->setLoginStatus(true);
		
		$taskfunctions->acceptTask('1');

		$ret = $taskfunctions->declineTask(false);
		$this->assertFalse($ret);

		$ret = $taskfunctions->declineTask('3');
		$this->assertFalse($ret);

		$ret = $taskfunctions->declineTask('1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM tasks_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 0);

		unset($taskfunctions);
    }

	function test_addAdhoc()
	{
		$taskfunctions = new tkTaskfunctions();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE tasklogs');

		$ret = $taskfunctions->addAdhoc(false);
		$this->assertFalse($ret);

		$taskdata = array();
		$ret = $taskfunctions->addAdhoc($taskdata);
		$this->assertFalse($ret);

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_name'] = 'test';
		$taskdata['task_description'] = 'testtask';
		
		$ret = $taskfunctions->addAdhoc($taskdata);
		$this->assertTrue($ret);

		unset($taskfunctions);
    }

	function test_getWorkflowgroup()
	{
		$taskfunctions = new tkTaskfunctions();
		$user = zgUserhandler::init();

		$this->database->query('TRUNCATE TABLE tasks');
		$this->database->query('TRUNCATE TABLE workflows_to_groups');

		$taskdata = array();
		$taskdata['task_hoursplanned'] = '1';
		$taskdata['task_begin'] = '01.01.1970';
		$taskdata['task_end'] = '01.02.1970';
		$taskdata['task_notes'] = '';
		$taskdata['task_workflow'] = '1';
		$taskdata['task_name'] = 'test1';
		$taskdata['task_description'] = 'testtask';
		$taskdata['task_priority'] = '1';
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test1', '1', '2', '1')");
		$this->database->query("INSERT INTO workflows_to_groups(workflowgroup_title, workflowgroup_workflow, workflowgroup_group, workflowgroup_order) VALUES('test2', '1', '3', '2')");
		$taskfunctions->addTask($taskdata);
		
		$ret = $taskfunctions->getWorkflowgroup(false);
		$this->assertFalse($ret);

		$ret = $taskfunctions->getWorkflowgroup('3');
		$this->assertFalse($ret);

		$ret = $taskfunctions->getWorkflowgroup('1');
		$this->assertTrue($ret);
		$this->assertEqual($ret, 1);

		unset($taskfunctions);
    }

}

?>
