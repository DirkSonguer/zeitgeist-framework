<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Cards class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package POKENDESIGN
 * @subpackage POKENDESIGN CARDS
 */

defined('POKENDESIGN_ACTIVE') or die();

class pdCards
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $database;
	protected $configuration;
	protected $user;

	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjects::init();
		$this->configuration = zgConfiguration::init();
		$this->user = zgUserhandler::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
	}


	/**
	 * Gets a random card from the card pool
	 * Returns an array with the card information
	 *
	 * @return array
	 */
	public function getRandomCard()
	{
		$this->debug->guard();

		$sql = "SELECT c.*, ud.userdata_username FROM cards c LEFT JOIN userdata ud ON c.card_user = ud.userdata_user ORDER BY RAND() LIMIT 1";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get random card: could not read card table', 'warning');
			$this->messages->setMessage('Could not get random card: could not read card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Gets the userdata for a given author
	 * Returns an array with the author information
	 *
	 * @param integer $author id of the author to load data for
	 * 
	 * @return array
	 */
	public function getAuthorData($author)
	{
		$this->debug->guard();

		$sql = "SELECT * FROM userdata WHERE userdata_user='" . $author . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get author information: could not read userdata table', 'warning');
			$this->messages->setMessage('Could not get author information: could not read userdata table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$author = $this->database->fetchArray($res);

		$this->debug->unguard($author);
		return $author;
	}
	
	
	/**
	 * Gets all cards by a given author
	 * Returns an array with the card information
	 *
	 * @param integer $author id of the author to load data for
	 * 
	 * @return array
	 */
	public function getAuthorCards($author)
	{
		$this->debug->guard();

		$sql = "SELECT *, DATE_FORMAT(card_timestamp, '%d.%m.%Y, %h:%m') as card_date FROM cards WHERE card_user='" . $author . "' ORDER BY card_timestamp DESC";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get author cards: could not read card table', 'warning');
			$this->messages->setMessage('Could not get author cards: could not read card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$cards = array();
		while ($card = $this->database->fetchArray($res))
		{
			$cards[] = $card;
		}

		$this->debug->unguard($cards);
		return $cards;
	}
	
}

?>
