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
	
	public $pagination_items = 6;

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
	 * Gets the total number of cards available
	 *
	 * @return integer
	 */
	public function getNumberOfCards()
	{
		$this->debug->guard();

		$sql = "SELECT count(*) as numcards FROM cards";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get number of cards: could not read card table', 'warning');
			$this->messages->setMessage('Could not get number of cards: could not read card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$ret = $this->database->fetchArray($res);

		$this->debug->unguard($ret['numcards']);
		return $ret['numcards'];
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

		$sql = "SELECT ud.*, u.user_username FROM userdata ud LEFT JOIN users u ON ud.userdata_user = u.user_id WHERE userdata_user='" . $author . "'";
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

		$sql = "SELECT *, DATE_FORMAT(card_timestamp, '%d.%m.%Y, %H:%m') as card_date FROM cards WHERE card_user='" . $author . "' ORDER BY card_timestamp DESC";
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


	/**
	 * Gets all cards
	 * Pagination will be handled by the parameter
	 * Returns an array with the card information
	 *
	 * @param integer $page pagination
	 * 
	 * @return array
	 */
	public function getAllCards($page)
	{
		$this->debug->guard();
		
		$pagination = ($this->pagination_items*($page-1)).', '.$this->pagination_items;

		$sql = "SELECT c.*, DATE_FORMAT(c.card_timestamp, '%d.%m.%Y, %H:%m') as card_date, ud.userdata_username FROM cards c LEFT JOIN userdata ud ON c.card_user = ud.userdata_user ORDER BY card_timestamp DESC LIMIT ".$pagination;
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


	/**
	 * Adds a card
	 * Assumes a valid card file as FILE
	 *
	 * @param array $carddata array with card data
	 * 
	 * @return boolean
	 */
	public function addCard($carddata)
	{
		$this->debug->guard();

		if ( (empty($_FILES)) || (empty($_FILES['addcard'])) )
		{
			$this->debug->write('Could not add new card: no uploaded card found', 'warning');
			$this->messages->setMessage('Could not add new card: no uploaded card found', 'warning');
			$this->debug->unguard(false);
			return false;
		}
			
		if ( ($_FILES['addcard']['type']['card_file'] != 'image/jpeg') && ($_FILES['addcard']['type']['card_file'] != 'image/png') )
		{
			$this->debug->write('Could not add new card: image not of the right type', 'warning');
			$this->messages->setMessage('Could not add new card: image not of the right type', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$image_id = uniqid();
		
		$original_filename = $_FILES['addcard']['name']['card_file'];
		$original_filename = explode('.', $original_filename);
		$original_filetype = $original_filename[count($original_filename)-1];
		$target_file = $image_id . '.' . $original_filetype;
		move_uploaded_file($_FILES['addcard']['tmp_name']['card_file'], 'uploads/' . $target_file);
		
		$sql = "INSERT INTO cards(card_user, card_title, card_description, card_filename) ";
		$sql .= "VALUES('" . $this->user->getUserID() . "', '" . $carddata['card_title'] . "', '" . $carddata['card_description'] . "', '" . $target_file . "')";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not get author cards: could not read card table', 'warning');
			$this->messages->setMessage('Could not get author cards: could not read card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Deletes a given card
	 * A user can only delete cards uploaded by him
	 *
	 * @param integer $card id of the card to delete
	 * 
	 * @return array
	 */
	public function deleteCard($card)
	{
		$this->debug->guard();

		$sql = "SELECT card_filename FROM cards WHERE card_id='" . $card . "' and card_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete card: could not read card table', 'warning');
			$this->messages->setMessage('Could not delete card: could not read card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$check = $this->database->numRows($res);
		if ($check < 1)
		{
			$this->debug->write('Could not delete card: card not owned by user', 'warning');
			$this->messages->setMessage('Could not delete card: card not owned by user', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$filename = $this->database->fetchArray($res);
		
		$sql = "DELETE FROM cards WHERE card_id='" . $card . "' and card_user='" . $this->user->getUserID() . "'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not delete card: could not edit card table', 'warning');
			$this->messages->setMessage('Could not delete card: could not edit card table', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		if (!unlink(getcwd() . '/uploads/'.$filename['card_filename']))
		{
			$this->debug->write('Could not delete card: could not delete card from filesystem', 'warning');
			$this->messages->setMessage('Could not delete card: could not delete card from filesystem', 'warning');
			$this->debug->unguard(false);
			return false;
		}
		
		$this->debug->unguard(true);
		return true;
	}

	
	/**
	 * Searches the card title and description for a given keyword
	 * Returns an array with the found cards
	 *
	 * @param string $search search string
	 * 
	 * @return array
	 */
	public function searchCards($search)
	{
		$this->debug->guard();
		
		mysql_real_escape_string($search);

		$sql = "SELECT c.*, DATE_FORMAT(c.card_timestamp, '%d.%m.%Y, %H:%m') as card_date, ud.userdata_username FROM cards c LEFT JOIN userdata ud ON c.card_user = ud.userdata_user WHERE c.card_title LIKE '%" . $search . "%' or c.card_description LIKE '%" . $search . "%'";
		$res = $this->database->query($sql);
		if (!$res)
		{
			$this->debug->write('Could not search for cards: could not read card table', 'warning');
			$this->messages->setMessage('Could not search for cards: could not read card table', 'warning');
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
