<?php


class miscFunctions
{
	public $database;
	
	public function __construct()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
    }

	function setupGame($numplayers=3)
	{
		$this->database->query('TRUNCATE TABLE races');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE race_to_users');
		$this->database->query('TRUNCATE TABLE race_events');
		$this->database->query('TRUNCATE TABLE race_actions');
		$this->database->query('TRUNCATE TABLE gamecards_to_users');
		
		// create race
		$raceid = rand(100,500);
		$sql = "INSERT INTO races(race_id, race_circuit, race_activeplayer, race_currentround, race_gamecardsallowed)";
		$sql .= "VALUES($raceid, 1, 1, 1, 1)";
		$res = $this->database->query($sql);
		
		// create random user ids for the players
		$player1 = rand(100,500);
		$player2 = rand(501,1000);
		$player3 = rand(1001,1500);
		$player4 = rand(1501,2000);

		// player1
		$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES($raceid, $player1, '1')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '1', '1', '150,370')";
		$res = $this->database->query($sql);
		$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '1', '1', '155,380')";
		$res = $this->database->query($sql);

		if ($numplayers>1)
		{			
			$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES($raceid, $player2, '2')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '2', '1', '175,380')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '2', '1', '170,370')";
			$res = $this->database->query($sql);
		}
		if ($numplayers>2)
		{			
			$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES($raceid, $player3, '3')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '3', '1', '190,370')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '3', '1', '195,380')";
			$res = $this->database->query($sql);
		}
		if ($numplayers>3)
		{			
			$sql = "INSERT INTO race_to_users(raceuser_race, raceuser_user, raceuser_order) VALUES($raceid, $player4, '4')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '4', '1', '210,370')";
			$res = $this->database->query($sql);
			$sql = "INSERT INTO race_actions(raceaction_race, raceaction_player, raceaction_action, raceaction_parameter) VALUES($raceid, '4', '1', '215,380')";
			$res = $this->database->query($sql);
		}

		$user = zgUserhandler::init();
		$user->setLoginStatus(true);

		$session = zgSession::init();
		$session->setSessionVariable('user_userid', $player1);

		return $raceid;
	}

}
?>

