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
	public function __construct( $dsn, $username = "", $passwd = "", $options = array( ) )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		parent::__construct( $dsn, $username, $passwd, $options );
		parent::setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		parent::setAttribute( PDO::ATTR_STATEMENT_CLASS, array( 'zgDatabasePDOStatement', array( $this ) ) );
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
	public function prepare( $statement, $driver_options = array( ) )
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
	private $currentQueryParameters;


	protected function __construct( $dbh )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );

		$this->dbh = $dbh;

		$this->currentQueryParameters = array( );
	}


	/**
	 * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
	 * Binds a parameter to the specified variable name
	 * @link http://php.net/manual/en/pdostatement.bindparam.php
	 * @param mixed $parameter <p>
	 * Parameter identifier. For a prepared statement using named
	 * placeholders, this will be a parameter name of the form
	 * :name. For a prepared statement using
	 * question mark placeholders, this will be the 1-indexed position of
	 * the parameter.
	 * </p>
	 * @param mixed $variable <p>
	 * Name of the PHP variable to bind to the SQL statement parameter.
	 * </p>
	 * @param int $data_type [optional] <p>
	 * Explicit data type for the parameter using the PDO::PARAM_*
	 * constants.
	 * To return an INOUT parameter from a stored procedure,
	 * use the bitwise OR operator to set the PDO::PARAM_INPUT_OUTPUT bits
	 * for the data_type parameter.
	 * </p>
	 * @param int $length [optional] <p>
	 * Length of the data type. To indicate that a parameter is an OUT
	 * parameter from a stored procedure, you must explicitly set the
	 * length.
	 * </p>
	 * @param mixed $driver_options [optional] <p>
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function bindParam( $parameter, &$variable, $data_type = null, $length = null, $driver_options = null )
	{
		$this->debug->guard( );

		// fill the parameter log
		// this will be used for debugging when the query is executed
		$this->currentQueryParameters[ $parameter ] = $variable;

		$return = parent::bindParam( $parameter, $variable, $data_type, $length, $driver_options );

		$this->debug->unguard( $return );
		return $return;
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
	public function execute( $input_parameters = array( ) )
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
			$return = false;
			$sqlMessage = $e->getMessage( );
		}

		// print out sql information
		// this includes the parameters (from the parameter cache) and possible error messages
		$this->debug->storeSQLStatement( 'EXECUTE( ' . implode( $this->currentQueryParameters, ', ' ) . ' ' . $sqlMessage . ' )', $return );

		// clear the query buffer
		$this->currentQueryParameters = array( );

		$this->debug->unguard( $return );
		return $return;
	}
}
