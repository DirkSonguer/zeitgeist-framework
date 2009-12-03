<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Gamedata class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST GAMESYSTEM
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgGamedata
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
	 * Creates a new entity
	 * The new entity will be empty in the sense that no components will be added at this stage
	 * Returns the id of the new entity if successful or false if not
	 *
	 * @param string $entity_name name of the entity (only used for debugging)
	 *
	 * @return int|boolean
	 */
	public function createEntity($name='', $template=false)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_entities(entity_name) VALUES('" . $entityname . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new entity: could not insert entity into database', 'warning');
			$this->messages->setMessage('Problem creating new entity: could not insert entity into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$entitiy = $this->database->insertId();
		if (!$ret)
		{
			$this->debug->write('Problem creating new entity: could not get entity id', 'warning');
			$this->messages->setMessage('Problem creating new entity: could not get entity id', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if ($template)
		{
			$this->addTemplateComponentsToEntity($template, $entitiy);
		}

		$this->debug->unguard($entitiy);
		return $entitiy;
	}


	/**
	 * Deletes a given entity.
	 *
	 * @param string $configfile filename of the configuratio with the form definition
	 *
	 * @return boolean
	 */
	public function deleteEntity($entityid)
	{
		$this->debug->guard();

		$sql = "DELETE FROM game_entities WHERE entity_id='" . $entityid . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem deleting an entity: could not delete entity from database', 'warning');
			$this->messages->setMessage('Problem deleting an entity: could not delete entity from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
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
	public function addComponentToEntity($componentId, $entityId)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_component_" . $componentId . "() VALUES()";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->messages->setMessage('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$componentDataId = $this->database->insertId();
		if (!$componentDataId)
		{
			$this->debug->write('Problem adding a new component to an entity: could not get the component data id', 'warning');
			$this->messages->setMessage('Problem adding a new component to an entity: could not get the component data id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO game_entity_components(entitycomponent_entity, entitycomponent_component, entitycomponent_componentdata) ";
		$sql .= "VALUES('" . $entityId . "', '" . $componentId . "', '" . $componentDataId . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->messages->setMessage('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard($componentDataId);
		return $componentDataId;
	}


	/**
	 * Removes a component from an existing entity
	 *
	 * @param int $componentId id of the component that should be removed from the entity
	 * @param int $entityId id of the entity to delete the component from
	 *
	 * @return boolean
	 */
	public function removeComponentFromEntity($componentId, $entityId)
	{
		$this->debug->guard();

		$sql = "DELETE FROM game_component_" . $componentId . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entityId . "' AND entitycomponent_component='" . $componentId . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->messages->setMessage('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "DELETE FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entityId . "' AND entitycomponent_component='" . $componentId . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem removing a component from an entity: could not delete component to entity mapping', 'warning');
			$this->messages->setMessage('Problem removing a component from an entity: could not delete component to entity mapping', 'warning');
			$this->debug->unguard(false);
			return false;
		}		
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets the data from a component of a specific entity
	 *
	 * @param int $componentId id of the component to get the data from
	 * @param int $entityId id of the entity the component is bound to
	 *
	 * @return array
	 */
	public function getComponentData($componentId, $entityId)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM game_component_" . $componentId . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entityId . "' AND entitycomponent_component='" . $componentId . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting data for the component of an entity: could not get component data', 'warning');
			$this->messages->setMessage('Problem getting data for the component of an entity: could not get component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Sets the data from a component of a specific entity
	 *
	 * @param int $componentId id of the component to set the data to
	 * @param int $entityId id of the entity the component is bound to
	 *
	 * @return array
	 */
	public function setComponentData($componentId, $entityId, $componentData)
	{
		$this->debug->guard();
		
		$sqlData = '';
		foreach($componentData as $componentKey => $componentValue)
		{
			$sqlData .= $componentKey . "='" . $componentValue . "',";
		}
		$sqlData = substr($sqlData, 0, -1);

		$sql = "UPDATE game_component_" . $componentId . " SET ".$sqlData;
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem setting data for the component of an entity: could not write component data', 'warning');
			$this->messages->setMessage('Problem setting data for the component of an entity: could not write component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Gets the list of component for a specific entity
	 * Returns an array with the component id as key and the component
	 * data id as value
	 *
	 * @param int $entityId id of the entity
	 *
	 * @return array
	 */
	public function getComponentListForEntity($entityId)
	{
		$this->debug->guard();

		$sql .= "SELECT entitycomponent_component, entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entityId . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting data for the component of an entity: could not get component data', 'warning');
			$this->messages->setMessage('Problem getting data for the component of an entity: could not get component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$componentList = array();
		while ($row = $this->database->fetchArray($res))
		{
			$componentList[$row['entitycomponent_component']] = $row['entitycomponent_component'];
		}

		$this->debug->unguard($componentList);
		return $componentList;
	}
}

?>