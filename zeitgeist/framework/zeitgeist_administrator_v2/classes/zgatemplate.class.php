<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Template class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST ADMINISTRATOR
 * @subpackage ZGA TEMPLATE
 */

defined('ZGADMIN_ACTIVE') or die();

class zgaTemplate extends zgTemplate
{
	protected $user;
	protected $basepath;
	protected $templatepath;
	protected $localpath;
	protected $session;
	protected $locale;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->user = zgUserhandler::init();
		$this->session = zgSession::init();

		$this->locale = zgLocale::init();

		parent::__construct();

		$this->basepath = $this->configuration->getConfiguration('administrator', 'application', 'basepath');
		$this->localpath = 'templates/' . $this->configuration->getConfiguration('administrator', 'application', 'templatepath');
		$this->templatepath = $this->basepath . '/templates/' . $this->configuration->getConfiguration('administrator', 'application', 'templatepath');
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
		
		$filename = $this->localpath . '/' . $filename;
		$ret = parent::load($filename);

		$this->assign('basepath', $this->basepath);
		$this->assign('templatepath', $this->templatepath);

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

		parent::insertUsermessages();

		if ($this->user->isLoggedIn())
		{
			parent::insertBlock('mainmenu');
		}
		else
		{
		}

		$ret = parent::show();

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Create a link for a given module and a given action
	 *
	 * @param string $module module to call
	 * @param string $action action to call
	 * @param array $parameter possible parameters
	 *
	 * @return string
	 */
	public function createLink($module, $action, $parameter=false)
	{
		$this->debug->guard();

		$linkurl = 'index.php';

		$link = array();
		if ($module != 'main') $link[0] = 'module='.$module;
		if ($action != 'index') $link[1] = 'action='.$action;
		if (count($link) > 0)
		{
			$linkparameters = implode($link, '&');
			$linkurl = $linkurl . '?' . $linkparameters;
		}

		if (is_array($parameter))
		{
			foreach ($parameter as $parameterkey => $parametervalue)
			$linkurl .= '&'.$parameterkey.'='.$parametervalue;
		}

		$linkurl = $this->basepath . '/' . $linkurl;

		return $linkurl;
		$this->debug->unguard($linkurl);
	}

}

?>
