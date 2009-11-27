<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Entitysetup class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ENTITYSYSTEM
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgEntitysetup
{
	protected $debug;
	protected $messages;
	protected $database;


	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();

		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Creates a new component
	 * The name of the new component. This is mostly for debugging reasons
	 * Returns the id of the new component
	 *
	 * @param string $name name of the new component
	 * @param string $description description of the new component
	 *
	 * @return int|boolean
	 */
	public function createComponent($name, $description='')
	{
		$this->debug->guard();

		$sql = "INSERT INTO components(component_name, component_description) ";
		$sql .= "VALUES('" . $name . "', '" . $description . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new component: could not insert component into database', 'warning');
			$this->messages->setMessage('Problem creating new component: could not insert component into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$componentid = $this->database->insertId();
		if (!$componentid)
		{
			$this->debug->write('Problem creating new component: could not get component id', 'warning');
			$this->messages->setMessage('Problem creating new component: could not get component id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "CREATE TABLE component_". $componentid ." ";
		$sql .= "(`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = MYISAM";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new component: could not create component table', 'warning');
			$this->messages->setMessage('Problem creating new component: could not create component table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($componentid);
		return $componentid;
	}


	/**
	 * Delete a given component
	 *
	 * @param string $component id of the component to delete
	 *
	 * @return boolean
	 */
	public function deleteComponent($component)
	{
		$this->debug->guard();


		$sql = "DELETE FROM components WHERE component_id='" . $component . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting component: could not delete component from database', 'warning');
			$this->messages->setMessage('Problem deleting component: could not delete component from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "DROP TABLE component_". $component ." ";
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


	/**
	 * Creates a new assemblage
	 * The name of the new assemblage. This is mostly for debugging reasons
	 * Returns the id of the new assemblage
	 *
	 * @param string $name name of the new assemblage
	 * @param string $description description of the new assemblage
	 *
	 * @return int|boolean
	 */
	public function createAssemblage($name, $description='')
	{
		$this->debug->guard();

		$sql = "INSERT INTO assemblages(assemblage_name, assemblage_description) ";
		$sql .= "VALUES('" . $name . "', '" . $description . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new assemblage: could not insert assemblage into database', 'warning');
			$this->messages->setMessage('Problem creating new assemblage: could not insert assemblage into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$assemblageid = $this->database->insertId();
		if (!$assemblageid)
		{
			$this->debug->write('Problem creating new assemblage: could not get assemblage id', 'warning');
			$this->messages->setMessage('Problem creating new assemblage: could not get assemblage id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($assemblageid);
		return $assemblageid;
	}


	/**
	 * Adds a new component to an assemblage
	 *
	 * @param int $component id of the component that should be added to the assemblage
	 * @param int $assemblage id of the assemblage to add the component to
	 *
	 * @return boolean
	 */
	public function addComponentToAssemblage($component, $assemblage)
	{
		$this->debug->guard();

		$sql = "INSERT INTO assemblage_components(assemblagecomponent_component, assemblagecomponent_assemblage) ";
		$sql .= "VALUES('" . $component . "', '" . $assemblage . "'')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding a new component to an assemblage: could not insert data into database', 'warning');
			$this->messages->setMessage('Problem adding a new component to an assemblage: could not insert data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Removes a component from an existing assemblage
	 *
	 * @param int $component id of the component that should be removed from the assemblage
	 * @param int $assemblage id of the assemblage to delete the component from
	 *
	 * @return boolean
	 */
	public function removeComponentFromEntity($component, $assemblage)
	{
		$this->debug->guard();

		$sql = "DELETE FROM assemblage_components WHERE ";
		$sql .= "assemblagecomponent_component='" . $component . "' AND ";
		$sql .= "assemblagecomponent_assemblage='" . $assemblage . "'')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem removing a component from an assemblage: could not delete component data', 'warning');
			$this->messages->setMessage('Problem removing a component from an assemblage: could not delete component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}

}

?>