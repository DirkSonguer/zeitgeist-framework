<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Objectcache class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.1 - 26.08.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST OBJECTCACHE
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgObjectcache::init();
 */
class zgObjectcache
{
	private static $instance = false;
	
	private $debug;
	private $messages;
	
	private $objects;

	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messags = zgMessages::init();
		
		$this->objects = array();
	}


	/**
	 * Initialize the singleton
	 * 
	 * @return object
	 */
	public static function init()
	{
		if (self::$instance === false)
		{
			self::$instance = new zgObjectcache();
		}

		return self::$instance;
	}
	

	/**
	 * Adds an object to the cache
	 * 
	 * @param string $name name of the object to store
	 * @param object $object whatever to store into the cache
	 * 
	 * @return boolean 
	 */
	public function storeObject($name, $object)
	{
		$this->debug->guard();
		
		if (!empty($this->objects[$name]))
		{
			$this->debug->write('An object of this name ("'.$name.'") already exists', 'error');
			$this->messages->setMessage('An object of this name ("'.$name.'") already exists', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->objects[$name] = $object;

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Reads out a stored object
	 * 
	 * @param string $name name of the object
	 * 
	 * @return object 
	 */
	public function getOject($name)
	{
		$this->debug->guard();

		if (empty($this->objects[$name]))
		{
			$this->debug->write('Object with name '.$name.' not found', 'error');
			$this->messages->setMessage('Object with name '.$name.' not found', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->objects[$name];
		
		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Deletes an object from the cache
	 * 
	 * @param string $name name of the object
	 * 
	 * @return boolean 
	 */
	public function deleteObject($name)
	{
		$this->debug->guard();

		if (empty($this->objects[$name]))
		{
			$this->debug->write('Object with name '.$name.' not found', 'error');
			$this->messages->setMessage('Object with name '.$name.' not found', 'error');
			$this->debug->unguard(false);
			return false;
		}
		
		unset($this->objects[$name]);
		
		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Clears the entire cache
	 * All objects will be lost
	 * 
	 * @return boolean 
	 */
	public function deleteAllObjects()
	{
		$this->debug->guard();

		$this->objects = array();
		
		$this->debug->unguard(true);
		return true;		
	}

}
?>
