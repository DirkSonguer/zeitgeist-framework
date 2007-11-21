<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Errorhandler class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * @version 1.0.1 - 19.08.2007
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ERRORHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgErrorhandler::init();
 */
class zgErrorhandler
{
	private static $instance = false;
	
	protected $debug;
	protected $messages;
	protected $configuration;
	
	protected $previousErrorhandler;
	protected $outputLevel;
		
	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->errornames = array(
			E_ERROR => 'E_ERROR',
			E_WARNING => 'E_WARNING',
			E_PARSE => 'E_PARSE',
			E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR => 'E_CORE_ERROR',
			E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR => 'E_UNEXPECTED_FAILURE',
			E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE => 'E_USER_NOTICE'
		);		
		
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->outputLevel = $this->configuration->getConfiguration('zeitgeist', 'errorhandler', 'error_reportlevel');
		
		$this->previousErrorhandler = set_error_handler(array($this, 'errorhandler'));
		if ($this->previousErrorhandler === false)
		{
			$this->debug->write('Could not set the new error handler', 'error');
		}		
	}

	
	/**
	 * destructor
	 */
	function __destruct()
	{
		restore_error_handler();
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
			self::$instance = new zgErrorhandler();			
		}

		return self::$instance;
	}


	/**
	 * Function that acts as hook for the thrown errors
	 * 
	 * @param string $errno errornumber, errorid can be given as define
	 * @param string $errstr actual errormessage
	 * @param string $errfile file that threw the error
	 * @param string $errline line that threw the error
	 * @param string $errcontext full backtrace of the current objects
	 */
	public function errorhandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		if ( ($this->outputLevel > 0) && (array_key_exists($errno, $this->errornames)) )
		{
			echo '<p style="background-color:#ffff00;">An error occured: ';
			echo "(". $this->errornames[$errno].")";
			echo "In file ". print_r($errfile, true);
			echo " (line ". print_r( $errline, true).")\n";
			echo "Message: ". print_r( $errstr, true).'</p>';
		}
		
		if ($this->outputLevel == 3) echo "\n\nContext:\n".print_r( $errcontext, true)."\n";
	}	
	
}
?>
