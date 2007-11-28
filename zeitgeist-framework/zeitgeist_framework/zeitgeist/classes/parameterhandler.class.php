<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Parameterhandler class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST PARAMETERHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgParameterhandler
{	
	protected $debug;
	protected $messages;
	protected $objects;
	protected $configuration;
	
	protected $rawParameters;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->objects = zgObjectcache::init();
		$this->configuration = zgConfiguration::init();
		
		$this->rawParameters = array();
		$this->rawParameters['GET'] = $_GET;
		$this->rawParameters['POST'] = $_POST;
		$this->rawParameters['COOKIE'] = $_COOKIE;
	}


	/**
	 * Retrieves all allowed parameters for the current module and action
	 * 
	 * @param string $module name of the current module
	 * @param string $action name of the current action
	 * 
	 * @return array 
	 */		
	protected function _getAllowedParameters($module, $action)
	{
		$this->debug->guard();
		
		$allowedParameters = array();
		
		$moduleConfiguration = $this->configuration->getConfiguration($module);
		
		if (!empty($moduleConfiguration[$action]['hasExternalParameters']))
		{
			foreach ($moduleConfiguration[$action] as $parametername => $parametervalue)
			{
				if ( (!is_array($parametervalue)) || (!array_key_exists('parameter', $parametervalue)) ) continue;
				
				$allowedParameters[$parametername] = $parametervalue;
			}
		}
		
		$this->debug->unguard($allowedParameters);
		return $allowedParameters;
	}
	
	
	/**
	 * This does the actual testing of a parameter against the expected regexp
	 * 
	 * @param string $parametername name of the parameter
	 * @param array $parameterdefinition array with the definition of the parameter
	 * 
	 * @return array 
	 */			
	protected function _checkParameter($parametername, $parameterdefinition)
	{
		$this->debug->guard(true);
		
		if ( (!isset($parameterdefinition['source'])) || (!isset($parameterdefinition['type'])) )
		{
			$this->debug->unguard('Problem checking parameter: could not get parameter definition for '.$parametername);
			return false;
		}

		if (isset($this->rawParameters[$parameterdefinition['source']][$parametername]))
		{
			if (preg_match($parameterdefinition['type'], $this->rawParameters[$parameterdefinition['source']][$parametername]) == 1)
			{
				$this->debug->unguard('Parameter appears to be safe: '.$parametername);
				return true;
			}
			else
			{
				$this->debug->unguard('Parameter not safe: '.$parametername);
				return false;
			}
		}
		else
		{
			$this->debug->unguard('Parameter not set: '.$parametername);
			return false;
		}
		
		$this->debug->unguard(false);
		return false;
	}
	
	
	/**
	 * Filters all parameters against the expected parameter values and returns the safe ones
	 * 
	 * @param array $allowedParameters array with allowed parameters
	 * 
	 * @return array 
	 */		
	protected function _filterParameters($allowedParameters)
	{
		$this->debug->guard();
		
		$safeParameters = array();
		
		$unsafeParameters = array();
		$unsafeParameters += $this->rawParameters['GET'];
		$unsafeParameters += $this->rawParameters['POST'];
		$unsafeParameters += $this->rawParameters['COOKIE'];	
		
		foreach($allowedParameters as $parametername => $parameterdefinition)
		{
			if ($this->_checkParameter($parametername, $parameterdefinition))
			{
				$safeParameters[$parametername] = $this->rawParameters[$parameterdefinition['source']][$parametername];
				unset($unsafeParameters[$parametername]);
			}
		}
		
		$this->objects->storeObject('unsafeParameters', $unsafeParameters);
		
		$this->debug->unguard($safeParameters);
		return $safeParameters;
	}
	
	
	/**
	 * Retrieves all parameters that are safe for the current module and action
	 * Returns an array with all parameters found safe
	 * Also creates an object in the objectcache with parameters found unsafe
	 * 
	 * @param string $module name of the current module
	 * @param string $action name of the current action
	 * 
	 * @return array 
	 */		
	public function getSafeParameters($module, $action)
	{
		$this->debug->guard();
		
		$allowedParameters = array();
		$allowedParameters = $this->_getAllowedParameters($module, $action);
		
		$safeParameters = array();
		if (count($allowedParameters) > 0)
		{
			$safeParameters = $this->_filterParameters($allowedParameters);
		}
		
		$this->debug->unguard($safeParameters);
		return $safeParameters;
	}
	
}
?>