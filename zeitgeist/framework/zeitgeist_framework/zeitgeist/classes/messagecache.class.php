<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Messagecache class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST MESSAGECACHE
 */

defined('ZEITGEIST_ACTIVE') or die();

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgMessages::init();
 */
class zgMessagecache
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct()
	{
		$this->debug = zgDebug::init();
		$this->session = zgSession::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();
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
			self::$instance = new zgMessagecache();
		}

		return self::$instance;
	}


	/**
	 * Loads the messages from the message cache
	 *
	 * @return boolean
	 */
	public function loadMessagesFromDatabase()
	{
		$this->debug->guard();

		$messagecacheTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_messagecache');
		$res = $this->database->query("SELECT messagecache_content FROM " . $messagecacheTablename . " WHERE messagecache_session = '" . $this->session->getSessionId() . "'");
		if ($this->database->numRows($res) == 1)
		{
			$row = $this->database->fetchArray($res);

			$serializedMessages = $row['messagecache_content'];
			$serializedMessages = base64_decode($serializedMessages);
			$messages = unserialize($serializedMessages);

			if ( ($messages === false) || (!is_array($messages)) )
			{
				$this->debug->write('Error unserializing message content from the database', 'error');
				$this->messages->setMessage('Error unserializing message content from the database', 'error');
				$this->debug->unguard(false);
				return false;
			}

			$this->messages->importMessages($messages);
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


	/**
	 * Save all messages of the user into the database
	 *
	 * @return boolean
	 */
	public function saveMessagesToDatabase()
	{
		$this->debug->guard();

		$messagecacheTablename = $this->configuration->getConfiguration('zeitgeist','tables','table_messagecache');

		$messages = $this->messages->getAllMessages();
		$serializedMessages = serialize($messages);
		if ($serializedMessages == '')
		{
			$this->debug->unguard(false);
			return false;
		}

		$serializedMessages = base64_encode($serializedMessages);
		if ($serializedMessages === false)
		{
			$this->debug->unguard(false);
			return false;
		}

		$sql = "INSERT INTO " . $messagecacheTablename . "(messagecache_session, messagecache_content) ";
		$sql .= "VALUES('" . $this->session->getSessionId() . "', '" . $serializedMessages . "') ";
		$sql .= "ON DUPLICATE KEY UPDATE messagecache_content='" . $serializedMessages . "'";
		$res = $this->database->query($sql);

		$this->debug->unguard(true);
		return true;
	}

}
?>