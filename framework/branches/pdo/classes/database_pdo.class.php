<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Database class
 *
 * A simple database class that extends the original PDO class
 * It serves as a small layer that adds function guarding
 * Most likely you will use your own or an already
 * existing database layer that you can extend with guarding
 * Just extend it as seen here
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST DATABASE
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgDatabasePDO extends PDO
{
	protected $debug;
	protected $messages;


	/**
	 * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
	 * Creates a PDO instance representing a connection to a database
	 * @link http://php.net/manual/en/pdo.construct.php
	 * @param $dsn
	 * @param $username
	 * @param $passwd
	 * @param $options [optional]
	 */
	public function __construct( $dsn, $username = "", $passwd = "", $options = array() )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		parent::__construct( $dsn, $username, $passwd, $options );
		parent::setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		parent::setAttribute( PDO::ATTR_STATEMENT_CLASS, array('zgDatabasePDOStatement', array($this)) );
	}


	/**
	 * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
	 * Prepares a statement for execution and returns a statement object
	 * @link http://php.net/manual/en/pdo.prepare.php
	 * @param string $statement <p>
	 * This must be a valid SQL statement for the target database server.
	 * </p>
	 * @param array $driver_options [optional] <p>
	 * This array holds one or more key=&gt;value pairs to set
	 * attribute values for the PDOStatement object that this method
	 * returns. You would most commonly use this to set the
	 * PDO::ATTR_CURSOR value to
	 * PDO::CURSOR_SCROLL to request a scrollable cursor.
	 * Some drivers have driver specific options that may be set at
	 * prepare-time.
	 * </p>
	 * @return PDOStatement If the database server successfully prepares the statement,
	 * PDO::prepare returns a
	 * PDOStatement object.
	 * If the database server cannot successfully prepare the statement,
	 * PDO::prepare returns false or emits
	 * PDOException (depending on error handling).
	 * </p>
	 * <p>
	 * Emulated prepared statements does not communicate with the database server
	 * so PDO::prepare does not check the statement.
	 */
	public function prepare( $statement, $driver_options = array() )
	{
		$this->debug->guard( );
		$this->debug->beginSQLStatement( );

		$result = parent::prepare( $statement, $driver_options );

		$this->debug->storeSQLStatement( 'PREPARE: ' . $statement, true );
		$this->debug->unguard( true );
		return $result;
	}
}


class zgDatabasePDOStatement extends PDOStatement
{
	public $dbh;


	protected function __construct( $dbh )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		$this->dbh = $dbh;
	}


	/**
	 * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
	 * Executes a prepared statement
	 * @link http://php.net/manual/en/pdostatement.execute.php
	 * @param array $input_parameters [optional] <p>
	 * An array of values with as many elements as there are bound
	 * parameters in the SQL statement being executed.
	 * All values are treated as PDO::PARAM_STR.
	 * </p>
	 * <p>
	 * You cannot bind multiple values to a single parameter; for example,
	 * you cannot bind two values to a single named parameter in an IN()
	 * clause.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function execute( $input_parameters = array() )
	{
		$this->debug->guard( );
		$this->debug->beginSQLStatement( );

		$sqlMessage = false;
		try
		{
			if ( count( $input_parameters ) < 1 )
			{
				$return = parent::execute( );
			}
			else
			{
				$return = parent::execute( $input_parameters );
			}
		}
		catch ( PDOException $e )
		{
			$sqlMessage = $e->getMessage( );
		}

		var_dump($this->debugDumpParams());

		$this->debug->storeSQLStatement( $sqlMessage, $return );
		$this->debug->unguard( $return );
		return $return;
	}
}
