<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Gamesetup class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMESYSTEM
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgGamesetup
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;


	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Creates a new component
	 * The name of the new component has to be unique as it's the table key
	 *
	 * @param string $name name of the new component
	 * @param string $description description of the new component
	 *
	 * @return int|boolean
	 */
	public function createComponent($name, $description='')
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_components(component_name, component_description) ";
		$sql .= "VALUES('" . $name . "', '" . $description . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new component: could not insert component into database', 'warning');
			$this->messages->setMessage('Problem creating new component: could not insert component into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->database->insertId();
		if (!$ret)
		{
			$this->debug->write('Problem creating new entity: could not get entity id', 'warning');
			$this->messages->setMessage('Problem creating new entity: could not get entity id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "CREATE TABLE game_component_". $ret ." ";
		$sql .= "(`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = MYISAM";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new component: could not create component table', 'warning');
			$this->messages->setMessage('Problem creating new component: could not create component table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Delete a given component
	 *
	 * @param string $component_id id of the component to delete
	 *
	 * @return boolean
	 */
	public function deleteComponent($id)
	{
		$this->debug->guard();


		$sql = "DELETE FROM game_components WHERE component_id='" . $id . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting component: could not delete component from database', 'warning');
			$this->messages->setMessage('Problem deleting component: could not delete component from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DROP TABLE game_component_". $id ." ";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting component: could not delete component table', 'warning');
			$this->messages->setMessage('Problem deleting component: could not delete component table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}

}

?>