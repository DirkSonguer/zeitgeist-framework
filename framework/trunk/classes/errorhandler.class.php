<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Errorhandler class
 *
 * A minimal error handler
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST ERRORHANDLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

/**
 * NOTE: This class is a singleton.
 * Other classes or files may initialize it with zgErrorhandler::init();
 */
class zgErrorhandler
{
	private static $instance = false;
	protected $debug;
	protected $messages;
	protected $configuration;
	protected $previousErrorhandler;
	protected $outputLevel;


	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	protected function __construct( )
	{
		$this->errornames = array( E_ERROR => 'E_ERROR', E_WARNING => 'E_WARNING', E_PARSE => 'E_PARSE', E_NOTICE => 'E_NOTICE', E_CORE_ERROR => 'E_CORE_ERROR', E_CORE_WARNING => 'E_CORE_WARNING', E_COMPILE_ERROR => 'E_COMPILE_ERROR', E_COMPILE_WARNING => 'E_COMPILE_WARNING', E_USER_ERROR => 'E_UNEXPECTED_FAILURE', E_USER_WARNING => 'E_USER_WARNING', E_USER_NOTICE => 'E_USER_NOTICE' );

		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );

		$this->outputLevel = $this->configuration->getConfiguration( 'zeitgeist', 'errorhandler', 'error_reportlevel' );

		$this->previousErrorhandler = set_error_handler( array( $this, 'errorhandler' ) );
		if ( $this->previousErrorhandler === false )
		{
			$this->debug->write( 'Problem: could not set the new error handler', 'warning' );
		}
	}


	/**
	 * destructor
	 */
	function __destruct( )
	{
		restore_error_handler( );
	}


	/**
	 * Initialize the singleton
	 *
	 * @return zgErrorhandler
	 */
	public static function init( )
	{
		if ( self::$instance === false )
		{
			self::$instance = new zgErrorhandler( );
		}

		return self::$instance;
	}


	/**
	 * Function that acts as hook for the thrown errors
	 *
	 * @param string $errorNo errornumber, errorid can be given as define
	 * @param string $errorString actual errormessage
	 * @param string $errorFile file that threw the error
	 * @param string $errorLine line that threw the error
	 * @param string $errorContext full backtrace of the current objects
	 */
	public function errorhandler( $errorNo, $errorString, $errorFile, $errorLine, $errorContext )
	{
		if ( ( $this->outputLevel > 0 ) && ( array_key_exists( $errorNo, $this->errornames ) ) )
		{
			echo '<p style="background-color:#ffff00;">An error occured: ';
			echo "(" . $this->errornames[ $errorNo ] . ")";
			echo "In file " . print_r( $errorFile, true );
			echo " (line " . print_r( $errorLine, true ) . ")\n";
			echo "Message: " . print_r( $errorString, true ) . '</p>';

			echo '<p style="background-color:#ffff00;">Backtrace:<br />';
			$trace = debug_backtrace( );
			foreach ( $trace as $backtrace )
			{
				echo basename( $backtrace[ 'file' ] );
				if ( !empty( $backtrace[ 'line' ] ) )
				{
					echo "[" . $backtrace[ 'line' ] . "]";
				}
				echo " ::" . $backtrace[ 'function' ];
				if ( !empty( $backtrace[ 'args' ] ) )
				{
					echo "(";
					echo implode( ' -- ', $backtrace[ 'args' ] );
					echo ")";
				}
				echo "<br />";
			}
			echo '</p>';
		}

		if ( $this->outputLevel == 3 )
		{
			echo "\n\nContext:\n" . print_r( $errorContext, true ) . "\n";
		}
	}
}

?>
