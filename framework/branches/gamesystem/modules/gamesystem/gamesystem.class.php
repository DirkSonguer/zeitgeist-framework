<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Gamesystem class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMESYSTEM
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgGamesystem
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
	 * Creates a new entity. The new entity will be empty in the sense that no components
	 * will be added at this stage
	 *
	 * @return boolean
	 */
	public function createEntity()
	{
		$this->debug->guard();
		
		$sql = 'INSERT INTO game_entities() VALUES()';
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new entitye: could not insert entity into database', 'warning');
			$this->messages->setMessage('Problem creating new entitye: could not insert entity into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->insertId();
		if (!$ret)
		{
			$this->debug->write('Problem creating new entitye: could not get entity id', 'warning');
			$this->messages->setMessage('Problem creating new entitye: could not get entity id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Deletes a given entity.
	 *
	 * @param string $configfile filename of the configuratio with the form definition
	 *
	 * @return boolean
	 */
	public function deleteEntity($entity_id)
	{
		$this->debug->guard();


		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Adds a new component to an entity.
	 *
	 * @param int $component_id id of the component that should be added to the entity
	 * @param int $entity_id id of the entity to add the component to
	 *
	 * @return boolean
	 */
	public function addComponentToEntity($component_id, $entity_id)
	{
		$this->debug->guard();


		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Removes a component from an existing entity
	 *
	 * @param int $component_id id of the component that should be removed from the entity
	 * @param int $entity_id id of the entity to delete the component from
	 *
	 * @return boolean
	 */
	public function removeComponentFromEntity($component_id, $entity_id)
	{
		$this->debug->guard();


		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Creates a new component
	 *
	 * @param string $component_name name of the new component
	 * @param string $component_description description of the new component
	 *
	 * @return boolean
	 */
	public function createComponent($component_name, $component_description)
	{
		$this->debug->guard();


		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Delete a given component
	 *
	 * @param string $component_id id of the component to delete
	 *
	 * @return boolean
	 */
	public function deleteComponent($component_id)
	{
		$this->debug->guard();


		$this->debug->unguard(true);
		return true;
	}


}

?>