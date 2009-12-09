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
	 * Creates a new entity
	 * The new entity will be empty in the sense that no components will be added at this stage
	 * Returns the id of the new entity if successful or false if not
	 *
	 * @param string $name name of the entity
	 * @param int $assemblage assemblage used to add initial components to the entity
	 *
	 * @return int|boolean
	 */
	public function createEntity($name='', $assemblage=false)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_entities(entity_name) VALUES('" . $name . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem creating new entity: could not insert entity into database', 'warning');
			$this->messages->setMessage('Problem creating new entity: could not insert entity into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$entity = $this->database->insertId();
		if (!$entity)
		{
			$this->debug->write('Problem creating new entity: could not get entity id', 'warning');
			$this->messages->setMessage('Problem creating new entity: could not get entity id', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		if ($assemblage)
		{
			$this->addAssemblageToEntity($assemblage, $entity);
		}

		$this->debug->unguard($entity);
		return $entity;
	}


	/**
	 * Deletes a given entity
	 *
	 * @param int $entity the id of the entity to delete
	 *
	 * @return boolean
	 */
	public function deleteEntity($entity)
	{
		$this->debug->guard();

		$sql = "DELETE FROM game_entities WHERE entity_id='" . $entity . "'";
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
	 * Adds a new component to an entity
	 * Optionally the initial data of the component can be given as third parameter
	 *
	 * @param int $component id of the component that should be added to the entity
	 * @param int $entity id of the entity to add the component to
	 * @param array $componentdata the initial data for the new component as key / value pairs
	 *
	 * @return boolean
	 */
	public function addComponentToEntity($component, $entity, $componentdata=false)
	{
		$this->debug->guard();

		$sql = "INSERT INTO game_component_" . $component . "() VALUES()";
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
		$sql .= "VALUES('" . $entity . "', '" . $component . "', '" . $componentDataId . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->messages->setMessage('Problem adding a new component to an entity: could not insert component data into database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (is_array($componentdata))
		{
			if ($this->setComponentData($component, $entity, $componentdata))
			{
				$this->debug->write('Problem adding a new component to an entity: could not insert initial component data', 'warning');
				$this->messages->setMessage('Problem adding a new component to an entity: could not insert initial component data', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard($componentDataId);
		return $componentDataId;
	}


	/**
	 * Removes a component from an existing entity
	 *
	 * @param int $component id of the component that should be removed from the entity
	 * @param int $entity id of the entity to delete the component from
	 *
	 * @return boolean
	 */
	public function removeComponentFromEntity($component, $entity)
	{
		$this->debug->guard();

		$sql = "DELETE FROM game_component_" . $component . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->messages->setMessage('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "DELETE FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "'";
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
	 * Adds aassemblage components to a given entity
	 *
	 * @param int $assemblage id of the assemblage that should be applied to the entity
	 * @param int $entity id of the entity to add the assemblage components to
	 *
	 * @return boolean
	 */
	public function addAssemblageToEntity($assemblage, $entity)
	{
		$this->debug->guard();

		$sql = "SELECT assemblagecomponent_component FROM game_assemblage_components WHERE assemblagecomponent_assemblage = '" . $assemblage . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem adding assemblage components to entity: could not get assemblage data from database', 'warning');
			$this->messages->setMessage('Problem adding assemblage components to entity: could not get assemblage data from database', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		while ($component = $this->database->fetchArray($res))
		{
			if (!$this->addComponentToEntity($component['assemblagecomponent_component'], $entity))
			{
				$this->debug->write('Problem adding assemblage components to entity: could not add the component to the entity', 'warning');
				$this->messages->setMessage('Problem adding assemblage components to entity: could not add the component to the entity', 'warning');
				$this->debug->unguard(false);
				return false;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets all datasets for a component
	 * This may be filtered by the second parameter, containing
	 * the component attribute name as key and the supposed value
	 * as value
	 * The return value will be $return[component_id][key] = value
	 * 
	 *
	 * @param int $component id of the component to get the data from
	 * @param array $filter array containing the filter rules
	 *
	 * @return array
	 */
	public function getComponentData($component, $filter=false)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM game_component_" . $component . " ";
		
		if (is_array($filter))
		{
			$sql .= "WHERE ";
			foreach ($filter as $attribute => $value)
			{
				$sql .= $attribute . "='" . $value . "' AND ";
			}
			$sql = substr($sql, 0, -4);
		}

		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting data for component: could not get component data', 'warning');
			$this->messages->setMessage('Problem getting data for component: could not get component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = array();
		while ($row = $this->database->fetchArray($res))
		{
			$ret[$row['id']] = $row;
		}

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Gets the dataset from a component of a specific entity
	 * This obviously can be only one dataset, so the return value
	 * is $return[key] = value
	 *
	 * @param int $component id of the component to get the data from
	 * @param int $entity id of the entity the component is bound to
	 *
	 * @return array
	 */
	public function getComponentDataForEntity($component, $entity)
	{
		$this->debug->guard();
/*
		$sql = "SELECT * FROM game_component_" . $component . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "')";
*/
		$sql = "SELECT * FROM game_component_" . $component . " gc ";
		$sql .= "JOIN game_entity_components gec ON gc.id = gec.entitycomponent_componentdata ";
		$sql .= "WHERE gec.entitycomponent_entity='" . $entity . "' AND gec.entitycomponent_component='" . $component . "'";

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
	 * Sets the data from a component of a specific component data entry
	 *
	 * @param int $component id of the component to set the data to
	 * @param int $entity id of the entity the component is bound to
	 * @param array $componentdata new component data as key / value pairs
	 *
	 * @return array
	 */
	public function setComponentData($component, $entity, $componentdata)
	{
		$this->debug->guard();
		
		$sqlData = '';
		foreach($componentdata as $componentKey => $componentValue)
		{
			$sqlData .= $componentKey . "='" . $componentValue . "',";
		}
		$sqlData = substr($sqlData, 0, -1);

		$sql = "UPDATE game_component_" . $component . " gi SET ".$sqlData." WHERE gi.id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "')";
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
	 * Gets the list of components for a specific entity
	 * Returns an array with the component id as key and the component
	 * data id as value
	 *
	 * @param int $entity id of the entity
	 *
	 * @return array
	 */
	public function getComponentListForEntity($entity)
	{
		$this->debug->guard();

		$sql .= "SELECT entitycomponent_component, entitycomponent_componentdata FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "'";
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


	/**
	 * Gets the entity the owns a specific component dataset
	 *
	 * @param int $component id of the component
	 * @param int $dataset id of the dataset to get the entity for
	 *
	 * @return int
	 */
	public function getEntityForComponent($component, $componentdata)
	{
		$this->debug->guard();

		$sql .= "SELECT entitycomponent_entity FROM game_entity_components ";
		$sql .= "WHERE entitycomponent_component='" . $component . "' AND entitycomponent_componentdata='" . $componentdata . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem getting entity for component: could not get entity to component connection', 'warning');
			$this->messages->setMessage('Problem getting entity for component: could not get entity to component connection', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


}

?>