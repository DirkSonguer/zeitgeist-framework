<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Entitysystem class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ENTITYSYSTEM
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgEntitysystem
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

		$sql = "INSERT INTO entities(entity_name) VALUES('" . $name . "')";
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

		$sql = "DELETE FROM entities WHERE entity_id='" . $entity . "'";
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
	 *
	 * @param int $component id of the component that should be added to the entity
	 * @param int $entity id of the entity to add the component to
	 *
	 * @return boolean
	 */
	public function addComponentToEntity($component, $entity)
	{
		$this->debug->guard();

		$sql = "INSERT INTO component_" . $component . "() VALUES()";
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

		$sql = "INSERT INTO entity_components(entitycomponent_entity, entitycomponent_component, entitycomponent_componentdata) ";
		$sql .= "VALUES('" . $entity . "', '" . $component . "', '" . $componentDataId . "')";
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
	 * @param int $component id of the component that should be removed from the entity
	 * @param int $entity id of the entity to delete the component from
	 *
	 * @return boolean
	 */
	public function removeComponentFromEntity($component, $entity)
	{
		$this->debug->guard();

		$sql = "DELETE FROM component_" . $component . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->messages->setMessage('Problem removing a component from an entity: could not delete component data', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$sql = "DELETE FROM entity_components ";
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

		$sql = "SELECT assemblagecomponent_component FROM assemblage_components WHERE assemblagecomponent_assemblage = '" . $assemblage . "'";
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
	 * Gets the data from a component of a specific entity
	 *
	 * @param int $component id of the component to get the data from
	 * @param int $entity id of the entity the component is bound to
	 *
	 * @return array
	 */
	public function getComponentData($component, $entity)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM component_" . $component . " WHERE id=(";
		$sql .= "SELECT entitycomponent_componentdata FROM entity_components ";
		$sql .= "WHERE entitycomponent_entity='" . $entity . "' AND entitycomponent_component='" . $component . "')";
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
	 * @param int $component id of the component to set the data to
	 * @param int $entity id of the entity the component is bound to
	 *
	 * @return array
	 */
	public function setComponentData($component, $entity, $componentData)
	{
		$this->debug->guard();
		
		$sqlData = '';
		foreach($componentData as $componentKey => $componentValue)
		{
			$sqlData .= $componentKey . "='" . $componentValue . "',";
		}
		$sqlData = substr($sqlData, 0, -1);

		$sql = "UPDATE component_" . $component . " SET ".$sqlData;
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

		$sql .= "SELECT entitycomponent_component, entitycomponent_componentdata FROM entity_components ";
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
}

?>