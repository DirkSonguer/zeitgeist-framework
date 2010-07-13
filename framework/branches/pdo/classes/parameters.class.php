<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Parameter class
 *
 * The parameter class handles all incoming parameters from the client
 * (GET, POST, COOKIE) and validates them against given definitions
 * Acts as a security layer between your application and the client input
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST PARAMETERS
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgParameters
{
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	protected $rawParameters;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->objects = zgObjects::init( );
		$this->configuration = zgConfiguration::init( );

		$this->rawParameters = array( );
		$this->rawParameters[ 'GET' ] = $_GET;
		$this->rawParameters[ 'POST' ] = $_POST;
		$this->rawParameters[ 'COOKIE' ] = $_COOKIE;
	}


	/**
	 * Retrieves all parameters that are safe for the current module and action
	 * Returns an array with all parameters found safe
	 * Also creates an object in the objecthandler with parameters found unsafe
	 *
	 * @param string $module name of the current module
	 * @param string $action name of the current action
	 *
	 * @return array
	 */
	public function getSafeParameters( $module, $action )
	{
		$this->debug->guard( );

		$allowedParameters = array( );
		$allowedParameters = $this->_getAllowedParameters( $module, $action );

		$safeParameters = array( );
		if ( count( $allowedParameters ) > 0 )
		{
			$safeParameters = $this->_filterParameters( $allowedParameters );
		}

		$this->debug->unguard( $safeParameters );
		return $safeParameters;
	}


	/**
	 * Retrieves all allowed parameters for the current module and action
	 *
	 * @access protected
	 *
	 * @param string $module name of the current module
	 * @param string $action name of the current action
	 *
	 * @return array
	 */
	protected function _getAllowedParameters( $module, $action )
	{
		$this->debug->guard( );

		$allowedParameters = array( );

		$moduleConfiguration = $this->configuration->getConfiguration( $module );

		if ( ( !empty( $moduleConfiguration[ $action ][ 'hasExternalParameters' ] ) ) && ( $moduleConfiguration[ $action ][ 'hasExternalParameters' ] == 'true' ) )
		{
			foreach ( $moduleConfiguration[ $action ] as $parametername => $parametervalue )
			{
				if ( ( !is_array( $parametervalue ) ) || ( !array_key_exists( 'parameter', $parametervalue ) ) ) continue;

				$allowedParameters[ $parametername ] = $parametervalue;
			}
		}

		$this->debug->unguard( $allowedParameters );
		return $allowedParameters;
	}


	/**
	 * This does the actual testing of a parameter against the expected regexp
	 *
	 * @access protected
	 *
	 * @param string $parametername name of the parameter
	 * @param array $parameterdefinition array with the definition of the parameter
	 *
	 * @return array
	 */
	protected function _checkParameter( $parametername, $parameterdefinition )
	{
		$this->debug->guard( true );

		if ( ( !isset( $parameterdefinition[ 'source' ] ) ) || ( !isset( $parameterdefinition[ 'expected' ] ) ) )
		{
			$this->debug->unguard( 'Problem checking parameter: could not get parameter definition for ' . $parametername );
			return false;
		}

		if ( isset( $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ] ) )
		{
			if ( $parameterdefinition[ 'expected' ] == 'CONSTANT' )
			{
				if ( ( !empty( $parameterdefinition[ 'value' ] ) ) && ( $parameterdefinition[ 'value' ] == $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ] ) )
				{
					$this->debug->unguard( 'Parameter appears to be safe: ' . $parametername );
					return true;
				}
			}
			elseif ( $parameterdefinition[ 'expected' ] == 'ARRAY' )
			{
				if ( is_array( $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ] ) )
				{
					$this->debug->unguard( 'Parameter appears to be safe: ' . $parametername );
					return true;
				}
			}
			else
			{
				$ret = preg_match( $parameterdefinition[ 'expected' ], $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ] );

				if ( $ret === false )
				{
					$this->debug->unguard( 'Parameter could not be tested. There may be an error in the regexp definition: ' . $parameterdefinition[ 'expected' ] );
					return false;
				}

				if ( $ret !== 0 )
				{
					$this->debug->unguard( 'Parameter appears to be safe: ' . $parametername );
					return true;
				}
			}

			$this->debug->unguard( 'Parameter not safe: ' . $parametername . ' (value: ' . $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ] . ' tested against: ' . $parameterdefinition[ 'expected' ] . ')' );
			return false;
		}
		else
		{
			$this->debug->unguard( 'Parameter not set: ' . $parametername );
			return false;
		}

		$this->debug->unguard( false );
		return false;
	}


	/**
	 * Filters all parameters against the expected parameter values and returns the safe ones
	 *
	 * @access protected
	 *
	 * @param array $allowedParameters array with allowed parameters
	 *
	 * @return array
	 */
	protected function _filterParameters( $allowedParameters )
	{
		$this->debug->guard( );

		$safeParameters = array( );

		$unsafeParameters = array( );
		$unsafeParameters += $this->rawParameters[ 'GET' ];
		$unsafeParameters += $this->rawParameters[ 'POST' ];
		$unsafeParameters += $this->rawParameters[ 'COOKIE' ];

		foreach ( $allowedParameters as $parametername => $parameterdefinition )
		{
			if ( $this->_checkParameter( $parametername, $parameterdefinition ) )
			{
				$safeParameters[ $parametername ] = $this->rawParameters[ $parameterdefinition[ 'source' ] ][ $parametername ];

				// strip slashes
				if ( ( !empty( $parameterdefinition[ 'stripslashes' ] ) ) && ( $parameterdefinition[ 'stripslashes' ] == 'true' ) )
				{
					if ( is_array( $safeParameters[ $parametername ] ) )
					{
						foreach ( $safeParameters[ $parametername ] as $key => $value )
						{
							$safeParameters[ $parametername ][ $key ] = stripslashes( $value );
						}
					}
					else
					{
						$safeParameters[ $parametername ] = stripslashes( $safeParameters[ $parametername ] );
					}
				}

				// escape parameter
				if ( ( !empty( $parameterdefinition[ 'escape' ] ) ) && ( $parameterdefinition[ 'escape' ] == 'true' ) )
				{
					if ( is_array( $safeParameters[ $parametername ] ) )
					{
						foreach ( $safeParameters[ $parametername ] as $key => $value )
						{
							$safeParameters[ $parametername ][ $key ] = mysql_escape_string( $value );
						}
					}
					else
					{
						$safeParameters[ $parametername ] = mysql_escape_string( $safeParameters[ $parametername ] );
					}
				}

				unset( $unsafeParameters[ $parametername ] );
			}
		}

		$this->objects->storeObject( 'unsafeParameters', $unsafeParameters );

		$this->debug->unguard( $safeParameters );
		return $safeParameters;
	}
}

?>