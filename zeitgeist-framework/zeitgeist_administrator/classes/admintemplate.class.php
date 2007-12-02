<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Template class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST TEMPLATE
 */

defined('ZGADMIN_ACTIVE') or die();

class adminTemplate extends zgTemplate
{
	private $user;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->user = zgUserhandler::init();
		
		parent::__construct();
	}
	
	
	/**
	 * Loads a template file
	 * 
	 * @param string $filename name of the file to load
	 * 
	 * @return boolean
	 */	
	public function load($filename)
	{
		$this->debug->guard();
		
		$ret = parent::load($filename);
		
		$this->debug->unguard($ret);
		return $ret;
	}
	

	/**
	 * Shows the template buffer
	 * 
	 * @return boolean 
	 */
	public function show()
	{
		$this->debug->guard();
		
		$ret = true;
		if ($this->user->isLoggedIn())
		{
			$ret = parent::insertBlock('mainmenu');
		}
		
		$ret = parent::show();
		
		$this->debug->unguard($ret);
		return $ret;
	}
	
}

?>
