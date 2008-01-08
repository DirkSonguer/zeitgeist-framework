<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Messaging class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST MESSAGES
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgMessages::init();
 */
class zgMessages
{
	private static $instance = false;

	protected $debug;
	protected $messages;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	private function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = array();
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
			self::$instance = new zgMessages();
		}

		return self::$instance;
	}


	/**
	 * Puts a message to the message stack
	 *
	 * @param string $message message to store
	 * @param string $to the recipient of the message. This should be a filename. If left empty, the message is public
	 *
	 * @return boolean
	 */
	public function setMessage($message, $type='message', $to='')
	{
		$this->debug->guard(true);

		$newMessage = array();

		$newMessage['message'] = $message;
		$newMessage['type'] = $type;

		$backtrace = debug_backtrace();
		$backtraceSender = $backtrace[0];
		$newMessage['from'] = array_pop( explode('\\', $backtraceSender['file']) );
		$newMessage['to'] = $to;

		$this->messages[] = $newMessage;

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets all messages from the message stack for the caller
	 *
	 * @param string $from gets all messages from this sender
	 *
	 * @return array
	 */
	public function getMessagesForModule($from='')
	{
		$this->debug->guard();

		$retArray = array();

		$backtrace = debug_backtrace();
		$backtrace = $backtrace[0];
		$caller = array_pop( explode('\\', $backtrace['file']) );

		foreach ($this->messages as $message)
		{
			if ( ($from == '') || ($message['from'] == $from) )
			{
				if ($message['to'] == $caller)
				{
					$retArray[] = $message;
				}
			}
		}

		$this->debug->unguard($retArray);
		return $retArray;
	}


	/**
	 * Gets all messages with the given type from the message stack
	 *
	 * @param string $type gets all messages with this type
	 *
	 * @return array
	 */
	public function getMessagesByType($type='')
	{
		$this->debug->guard();

		$retArray = array();

		foreach ($this->messages as $message)
		{
			if ($message['type'] == $type)
			{
				$retArray[] = $message;
			}
		}

		$this->debug->unguard($retArray);
		return $retArray;
	}


	/**
	 * Gets all messages from the message stack
	 *
	 * @param string $from gets all messages from this sender
	 *
	 * @return array
	 */
	public function getAllMessages($from='')
	{
		$this->debug->guard();

		$retArray = array();

		foreach ($this->messages as $message)
		{
			if ( ($from == '') || ($message['from'] == $from) )
			{
				$retArray[] = $message;
			}
		}

		$this->debug->unguard($retArray);
		return $retArray;
	}


	/**
	 * Clears all messages from the message stack
	 *
	 * @return boolean
	 */
	public function clearAllMessages()
	{
		$this->debug->guard();

		$this->messages = array();

		$this->debug->unguard(true);
		return true;
	}

}
?>