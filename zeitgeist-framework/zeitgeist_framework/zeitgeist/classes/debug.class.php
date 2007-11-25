<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Debug class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
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
	
	protected $startTime;

	protected $debugMessages;
	protected $guardMessages;
	protected $guardStack;
	
	public $showInnerLoops;	// Set this to true to show inner loops in the guard-output
	
	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debugMessages = array();
		$this->guardMessages = array();
		$this->guardStack = array();

		$this->showInnerLoops = false;

		$this->startTime = microtime();		
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
		$newDebugMessage = array();
		
		$newDebugMessage['executionTime'] = $this->_getExecutionTime();
		$newDebugMessage['message'] = $message;
		$newDebugMessage['type'] = $type;

		$backtrace = array_shift(debug_backtrace());
		$newDebugMessage['filename'] = array_pop( explode('\\', $backtrace['file']) );
		$newDebugMessage['function'] = $backtrace['function'];
		$newDebugMessage['line'] = $backtrace['line'];
		
		$this->debugMessages[] = $newDebugMessage;
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
		$newGuardMessage = array();

		$newGuardMessage['type'] = 'GUARD';
		$newGuardMessage['executionTime'] = $this->_getExecutionTime();

		if (function_exists('memory_get_usage'))
		{
			$newGuardMessage['currentMemoryUsage'] = memory_get_usage() / 1024;
		}
		else
		{
			$newGuardMessage['currentMemoryUsage'] = '-';
		}

		$newGuardMessage['isInnerLoop'] = $innerLoop;
		array_push($this->guardStack, $innerLoop);
		
		$backtrace = debug_backtrace();
		$backtrace = $backtrace[1];
		
		if (!empty($backtrace['file']))
		{
			$newGuardMessage['filename'] = basename( array_pop( explode('\\', $backtrace['file']) ) );
		}
		
		if (!empty($backtrace['class']))
		{
			$newGuardMessage['class'] = $backtrace['class'];
		}
		else
		{
			$newGuardMessage['class'] = '';
		}
		
		foreach($backtrace['args'] as $parameter)
		{
			$newGuardMessage['args'][] = $parameter;
		}
		
		if (!empty($backtrace['function']))
		{
			$newGuardMessage['function'] = $backtrace['function'];
		}
		
		if (!empty($backtrace['line']))
		{
			$newGuardMessage['line'] = $backtrace['line'];
		}
		
		$this->guardMessages[] = $newGuardMessage;
	}


	/**
	 * Ends guarding a function
	 * In Zeitgeist (and its applications), every function should guard/ unguard itself to get a complete image of the construction of a page.
	 * 
	 * @param variant $returnValue the return value of the guarded function (if it has one)
	 */	
	public function unguard($returnValue)
	{
		$newGuardMessage = array();

		$newGuardMessage['type'] = 'UNGUARD';
		$newGuardMessage['executionTime'] = $this->_getExecutionTime();
		$newGuardMessage['currentMemoryUsage'] = memory_get_usage() / 1024;
		
		$newGuardMessage['isInnerLoop'] = array_pop($this->guardStack);

		$backtrace = debug_backtrace();
		$backtrace = $backtrace[1];

		if (!empty($backtrace['file']))
		{
			$newGuardMessage['filename'] = basename( array_pop( explode('\\', $backtrace['file']) ) );
		}
		
		if (!empty($backtrace['class']))
		{
			$newGuardMessage['class'] = $backtrace['class'];
		}
		else
		{
			$newGuardMessage['class'] = '';
		}
		
		if (!empty($backtrace['function']))
		{
			$newGuardMessage['function'] = $backtrace['function'];
		}
		
		if (!empty($backtrace['line']))
		{
			$newGuardMessage['line'] = $backtrace['line'];
		}

		$newGuardMessage['returnValue'] = $returnValue;
		
		$this->guardMessages[] = $newGuardMessage;
	}

	
	/**
	 * Shows all the debug messages as a table
	 */
	public function showDebugMessages()
	{
		echo '<div class="debug">';
		echo '<h1>DebugMessages</h1>';
		echo '<table border="1" class="debugMessages">';
		echo '<tr><th>ID</th><th>Time</th><th>Type</th><th>File</th><th>Message</th></tr>';

		foreach ($this->debugMessages as $debugID => $debugMessage)
		{
			echo '<tr class="' . $debugMessage['type'] . '">';
			$currentDebugLine = '';

			$currentDebugLine .= '<td class="debugMessageLine">' . $debugID . '</td>';
			$currentDebugLine .= '<td class="debugMessageLine">' . $debugMessage['executionTime'] . '</td>';
			$currentDebugLine .= '<td class="debugMessageLine">' . strtoupper($debugMessage['type']) . '</td>';
			$currentDebugLine .= '<td class="debugMessageLine">[' . $debugMessage['line'] . '] <strong>' . $debugMessage['filename'] . '</strong></td>';
			$currentDebugLine .= '<td class="debugMessageLine">' . $debugMessage['message'] . '</td>';

			echo($currentDebugLine);
			echo '</tr>';
		}
		
		echo '</table></div>';
	}


	/**
	 * Shows all the guard messages as a table
	 */
	public function showGuardMessages()
	{
		echo '<div class="debug">';
		echo '<h1>GuardMessages</h1>';
		
		echo '<table border="1" class="guardMessages">';
		echo '<tr><th>ID</th><th>Time</th><th>Memory</th><th>Type</th><th>File</th></tr>';

		foreach ($this->guardMessages as $guardID => $guardMessage)
		{
			if (($this->showInnerLoops) || ($guardMessage['isInnerLoop'] != true))
			{
				if ($guardMessage['type'] == 'GUARD')
				{
					echo '<tr class="guardLine">';
				}
				else
				{
					echo '<tr class="unguardLine">';
				}
				
				$currentGuardLine = '';
	
				$currentGuardLine .= '<td class="guardMessageLine">' . $guardID . '</td>';
				$currentGuardLine .= '<td class="guardMessageLine">' . $guardMessage['executionTime'] . '</td>';
				$currentGuardLine .= '<td class="guardMessageLine">' . number_format($guardMessage['currentMemoryUsage'], 2) . '</td>';
				$currentGuardLine .= '<td class="guardMessageLine">' . $guardMessage['type'] . '</td>';
				
				$currentGuardLine .= '<td class="guardMessageLine">';
					
				if (!empty($guardMessage['filename'])) $currentGuardLine .= '[<span class="guardFile">' . $guardMessage['filename'] . '</span> ';
					else $currentGuardLine .= '[ ';

				if (!empty($guardMessage['line'])) $currentGuardLine .= '(<span class="guardLine">' . $guardMessage['line'] . '</span>)] ';
					else $currentGuardLine .= ' ]';
					
				if (!empty($guardMessage['filename'])) $currentGuardLine .= '<span class="guardClass">' . $guardMessage['class'] . '-&gt;</span>';
					else $currentGuardLine .= ' ';
					
				if (!empty($guardMessage['function'])) $currentGuardLine .= '<span class="guardFunction">' . $guardMessage['function'] . '</span>';
					else $currentGuardLine .= ' ';
				
				if ($guardMessage['type'] == 'GUARD')
				{
					$argstring = '';
					if ( (!empty($guardMessage['args'])) && (is_array($guardMessage['args'])) )
					{
						for ($i=0; $i<count($guardMessage['args']); $i++)
						{
							$guardMessage['args'][$i] = "<span class=\"guardArgument\">'" . $guardMessage['args'][$i] . "</span>'";
						}
						
						$argstring = implode(',', $guardMessage['args']);
					}
					
					$currentGuardLine .= '('.$argstring . ')';
				}
				else
				{
					$currentGuardLine .= "() returned with: <span class=\"guardArgument\">'" . $guardMessage['returnValue'] . "'</span>";
				}
				
				$currentGuardLine .= '</td>';
				
				echo($currentGuardLine);
				echo '</tr>';
			}
		}
		
		echo '</table></div>';		
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
		$filename = ZEITGEIST_ROOTDIRECTORY . 'configuration/' . $stylesheet;
		
		if (!file_exists($filename))
		{
			return false;
		}
		
		$filehandle = fopen($filename, "r");
		$filecontent = fread($filehandle, filesize($filename));
		fclose($filehandle);
		echo $filecontent;
		
		return true;
	}

	/**
	 * Gets the current execution time
	 * 
	 * @return integer 
	 */	
	protected function _getExecutionTime()
	{
		$currentTime = microtime();
		return round($currentTime - $this->startTime, 4);
	}

	
}
?>