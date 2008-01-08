<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Database class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST DATABASE
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgDatabase
{
	protected $debug;
	protected $messages;

	protected $dblink;
	protected $persistent;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();

		$this->persistent = false;
		$this->dblink = false;
	}

	/**
	 * Class destructor
	 */
	public function __destruct()
	{
		if (($this->dblink) && ($this->persistent))
		{
			$this->close();
		}
	}


	/**
	 * Connect to the database
	 * If no connection info is given, the standard defines are used.
	 * These should be defined in the application config.
	 *
	 * @param string $servername servername to connect to
	 * @param string $username username to connect with
	 * @param string $userpass userpassword to connect with
	 * @param boolean $$persistent set true if connection should be persistent
	 * @param boolean $newconnection set true if new link should be created for this connection
	 *
	 * @return boolean
	 */
	public function connect($servername=ZG_DB_DBSERVER, $username=ZG_DB_USERNAME, $userpass=ZG_DB_USERPASS, $database=ZG_DB_DATABASE, $persistent=false, $newconnection=false)
	{
		$this->debug->guard();

		if ($persistent)
		{
			$this->persistent = true;
			$this->dblink = mysql_pconnect($servername, $username, $userpass);
		}
		else
		{
			$this->dblink = mysql_connect($servername, $username, $userpass, $newconnection);
		}

		if (!$this->dblink)
		{
			$this->debug->write('Error connecting to database server: '.mysql_error(), 'error');
			$this->messages->setMessage('Error connecting to database server: '.mysql_error(), 'error');
			$this->debug->unguard(false);
			return false;
		}

		if (!mysql_select_db($database, $this->dblink))
		{
			$this->debug->write('Error connecting to database: '.mysql_error(), 'error');
			$this->messages->setMessage('Error connecting to database: '.mysql_error(), 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->setDBCharset('utf8');

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Close the database connection
	 *
	 * @global string document the fact that this function uses $_myvar
	 * @staticvar integer $staticvar this is actually what is returned
	 *
	 * @param string $param1 name to declare
	 * @param string $param2 value of the name
	 *
	 * @return integer
	 */
	public function close()
	{
		$this->debug->guard();

		if (($this->dblink) && ($this->persistent))
		{
			mysql_close($this->dblink);
		}

		$this->debug->unguard(true);
	}


	/**
	 * Set the charset for connection, results and client
	 *
	 * @param string $charset name to declare
	 * @param string $param2 value of the name
	 *
	 * @return boolean
	 */
	public function setDBCharset($charset)
	{
		$this->debug->guard();

		$res = mysql_query("SET character_set_connection = ".$charset, $this->dblink);
		if (!$res)
		{
			$this->debug->write('Error setting charset for connection: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->messages->setMessage('Error setting charset for connection: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$res = mysql_query("SET character_set_results = ".$charset, $this->dblink);
		if (!$res)
		{
			$this->debug->write('Error setting charset for results: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->messages->setMessage('Error setting charset for results: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$res = mysql_query("SET character_set_client = ".$charset, $this->dblink);
		if (!$res)
		{
			$this->debug->write('Error setting charset for client: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->messages->setMessage('Error setting charset for client: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Send a query to the database
	 *
	 * @param string $query contains the query to be sent
	 *
	 * @return resource
	 */
	public function query($query)
	{
		$this->debug->guard();

		$result = mysql_query($query, $this->dblink);

		if (!$result)
		{
			$this->debug->write('Error executing query: '.mysql_error().' Query was: "' . $query . '"', 'error');
			$this->messages->setMessage('Error executing query: '.mysql_error().' Query was: "' . $query . '"', 'error');
		}

		$this->debug->unguard($result);
		return $result;
	}


	/**
	 * Fetches a row from the sql result
	 * Standard type is MYSQL_ASSOC, so an associative array is returned
	 *
	 * @param string $result resource id of the query
	 * @param string $type type of array to be fetched
	 *
	 * @return array
	 */
	public function fetchArray($result, $type=MYSQL_ASSOC)
	{
		$this->debug->guard(true);

		$ret = mysql_fetch_array($result, $type);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Returns the number of rows for a resource
	 *
	 * @param string $result resource id of the query
	 *
	 * @return integer
	 */
	public function numRows($result)
	{
		$this->debug->guard();

		$ret = mysql_num_rows($result);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Returns the number of affected rows by the last operation
	 *
	 * @return integer
	 */
	public function affectedRows()
	{
		$this->debug->guard();

		$ret = mysql_affected_rows($this->dblink);

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * gets the last id generated by an insert operation
	 *
	 * @return integer
	 */
	public function insertId()
	{
		$this->debug->guard();

		$ret = mysql_insert_id($this->dblink);

		if (!$ret)
		{
			$this->debug->write('Error geting the last insert id: '.mysql_error(), 'error');
			$this->messages->setMessage('Error geting the last insert id: '.mysql_error(), 'error');
		}

		$this->debug->unguard($ret);
		return $ret;
	}

}
?>