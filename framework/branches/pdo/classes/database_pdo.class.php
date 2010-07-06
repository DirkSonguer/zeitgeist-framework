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
	public function __construct( $dsn, $username, $passwd, $options=NULL )
	{
        $this->debug = zgDebug::init( );
        $this->messages = zgMessages::init( );

        echo "here";

		parent::__construct( $dsn, $username, $passwd, $options );
	}

    public function execute( )
    {
        $this->debug->guard( );
        $this->debug->beginSQLStatement( );

        echo "there";

        $this->debug->write("test in class");

        $result = parent::execute( );

        $this->debug->storeSQLStatement( '', $result );
        $this->debug->unguard( $result );
        return $result;
    }


}
