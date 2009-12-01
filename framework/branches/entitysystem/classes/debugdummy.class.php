<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Debug class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST DEBUG
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgDebug::init();
 */
class zgDebug
{
	private static $instance = false;

	public $showInnerLoops;	// Set this to true to show inner loops in the guard-output

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
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
			self::$instance = new zgDebug();
		}

		return self::$instance;
	}


	/**
	 * Write a debug message to the cache
	 *
	 * @param string $message debug message to print
	 * @param integer $level level of the message. 0 = important,.. , 3 = unimportant
	 */
	public function write($message, $type='message')
	{
	}


	/**
	 * Starts the timer for the sql execution timer
	 */
	public function beginSQLStatement()
	{
	}


	/**
	 * Write an sql query message to the cache
	 *
	 * @param string $query original query
	 * @param resource $result result
	 */
	public function storeSQLStatement($query, $result)
	{
	}


	/**
	 * Starts guarding a function
	 * In Zeitgeist (and its applications), every function should guard/ unguard itself to get a complete image of the construction of a page.
	 *
	 * There are somewhat 4 levels of importance:
	 * 0 = core
	 * 1 = public module/ class function
	 * 2 = private/ protected module/ class function
	 * 3 = inner loop function
	 *
	 * This is by no means the only way to describe the levels, but we found it worked.
	 *
	 * @param boolean $innerLoop set true if the calling function is an inner loop
	 */
	public function guard($innerLoop = false)
	{
	}


	/**
	 * Ends guarding a function
	 * In Zeitgeist (and its applications), every function should guard/ unguard itself to get a complete image of the construction of a page.
	 *
	 * @param variant $returnValue the return value of the guarded function (if it has one)
	 */
	public function unguard($returnValue)
	{
	}


	/**
	 * Shows somemisc information
	 */
	public function showMiscInformation()
	{
	}


	/**
	 * Shows all the debug messages as a table
	 */
	public function showDebugMessages()
	{
	}


	/**
	 * Shows all the query messages as a table
	 */
	public function showQueryMessages()
	{
	}


	/**
	 * Shows all the guard messages as a table
	 */
	public function showGuardMessages()
	{
	}


	/**
	 * Loads a stylesheet to use with debug output
	 *
	 * @param string $stylesheet name of the stylesheet to load
	 *
	 * @return boolean
	 */
	public function loadStylesheet($stylesheet)
	{
	}


	/**
	 * Saves debug information to file
	 *
	 * @param string $filename file to save the debug information to
	 *
	 * @return boolean
	 */
	public function saveToFile($filename)
	{
	}


	/**
	 * Gets the current execution time
	 *
	 * @return integer
	 */
	protected function _getExecutionTime()
	{
	}

}
?>