<?php

class testLrgamecardfunctions extends UnitTestCase
{
	public $database;
	public $miscfunctions;
	
	function test_init()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		$this->miscfunctions  = new miscFunctions();

		$gamecardfunctions = new lrGamecardfunctions();
		$this->assertNotNull($gamecardfunctions);
		unset($gamecardfunctions);
    }
	
	function test_addGamecard()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE gamecards_to_users');

		$player1 = rand(100,500);
		$player2 = rand(501,1000);

		$user = zgUserhandler::init();
		$user->setLoginStatus(true);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $player1);

		$ret = $gamecardfunctions->addGamecard('1');
		$ret = $gamecardfunctions->addGamecard('2');

		$res = $this->database->query("SELECT * FROM gamecards_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$gamecardfunctions->addGamecard('1');
		$res = $this->database->query("SELECT * FROM gamecards_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 2);

		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['usergamecard_gamecard'], '1');		
		$this->assertEqual($ret['usergamecard_user'], $player1);		
		$this->assertEqual($ret['usergamecard_count'], '2');		
	}


	function test_removeGamecard()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE gamecards_to_users');

		$player1 = rand(100,500);
		$player2 = rand(501,1000);

		$user = zgUserhandler::init();
		$user->setLoginStatus(true);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $player1);

		$ret = $gamecardfunctions->removeGamecard('1');
		$this->assertFalse($ret);

		$ret = $gamecardfunctions->addGamecard('1');
		$ret = $gamecardfunctions->addGamecard('2');
		
		$ret = $gamecardfunctions->removeGamecard('2');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM gamecards_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);

		$ret = $gamecardfunctions->addGamecard('1');
		$ret = $gamecardfunctions->removeGamecard('1');
		$this->assertTrue($ret);

		$res = $this->database->query("SELECT * FROM gamecards_to_users");
		$ret = $this->database->numRows($res);
		$this->assertEqual($ret, 1);
		
		$ret = $this->database->fetchArray($res);
		$this->assertEqual($ret['usergamecard_gamecard'], '1');		
		$this->assertEqual($ret['usergamecard_user'], $player1);		
		$this->assertEqual($ret['usergamecard_count'], '1');
	}


	function test_checkRights()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE gamecards_to_users');

		$player1 = rand(100,500);
		$player2 = rand(501,1000);

		$user = zgUserhandler::init();
		$user->setLoginStatus(true);
		$session = zgSession::init();
		$session->setSessionVariable('user_id', $player1);

		$ret = $gamecardfunctions->addGamecard('1');
		
		$ret = $gamecardfunctions->checkRights('2');
		$this->assertFalse($ret);

		$ret = $gamecardfunctions->checkRights('1');
		$this->assertTrue($ret);		
	}


	function test_getGamecardData()
	{
		$gamecardfunctions = new lrGamecardfunctions();
		$this->database->query('TRUNCATE TABLE gamecards');

		$ret = $gamecardfunctions->getGamecardData(1);
		$this->assertFalse($ret);

		$sql = "INSERT INTO gamecards (gamecard_id, gamecard_name, gamecard_description, gamecard_image, gamecard_roundoffset, gamecard_playerrange) ";
		$sql .= "VALUES (1, 'Test', 'Simple Gamecard Test', '', '1', '0')";
		$res = $this->database->query($sql);
		
		$ret = $gamecardfunctions->getGamecardData(1);
		$this->assertTrue(is_array($ret));
		$this->assertEqual($ret['gamecard_name'], 'Test');
	}

}

?>
