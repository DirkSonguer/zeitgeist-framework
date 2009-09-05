<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Messaging class
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
	protected $session;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->debug = zgDebug::init();
		$this->session = zgSession::init();
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
	public function setMessage($message, $type='message')
	{
		$this->debug->guard(true);

		$newMessage = new zgMessage;

		$newMessage->message = $message;
		$newMessage->type = $type;

		$backtrace = debug_backtrace();
		$backtraceSender = $backtrace[0];
		$newMessage->from = array_pop( explode('\\', $backtraceSender['file']) );

		$this->messages[] = $newMessage;

		$this->debug->unguard(true);
		return true;
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
			if ($message->type == $type)
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
			if ( ($from == '') || ($message->from == $from) )
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


	/**
	 * Imports messages and adds them to the current messages
	 * Beware: the structures of the messages have to be correct!
	 *
	 * @param array $messagearray new messages
	 *
	 * @return boolean
	 */
	public function importMessages($messagearray=array())
	{
		$this->debug->guard();

		if (!is_array($messagearray))
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->messages = array_merge($this->messages, $messagearray);

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Save all messages of the user into the session
	 *
	 * @return boolean
	 */
	public function saveMessagesToSession()
	{
		$this->debug->guard();

		$messages = $this->messages->getAllMessages();
		$serializedMessages = serialize($messages);
		if ($serializedMessages == '')
		{
			$this->debug->unguard(false);
			return false;
		}
		
		$this->session->setSessionVariable('messagecache_session', $serializedMessages);

		$this->debug->unguard(true);
		return true;
	}
	

	/**
	 * Loads the messages from the message cache
	 *
	 * @return boolean
	 */
	public function loadMessagesFromSession()
	{
		$this->debug->guard();

		if ($messagecache = $this->session->getSessionVariable('messagecache_session'))
		{
			$serializedMessages = $messagecache;
			$messages = unserialize($serializedMessages);

			if ( ($messages === false) || (!is_array($messages)) )
			{
				$this->debug->write('Error unserializing message content from the database', 'error');
				$this->messages->setMessage('Error unserializing message content from the database', 'error');
				$this->debug->unguard(false);
				return false;
			}

			$this->importMessages($messages);
		}
		else
		{
			$this->debug->write('No messagedata is stored in database for this user', 'warning');
			$this->messages->setMessage('No messagedata is stored in database for this user', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}
	
}


class zgMessage
{
	public $message;
	public $type;
	public $from;

	public function __construct()
	{
		$message = '';
		$type = '';
		$from = '';
	}

}
?>