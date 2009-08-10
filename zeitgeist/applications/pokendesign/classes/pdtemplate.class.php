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
 * @package POKENDESIGN
 * @subpackage POKENDESIGN TEMPLATE
 */

defined('POKENDESIGN_ACTIVE') or die();

class pdTemplate extends zgTemplate
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
		$this->user = zgFacebookUserhandler::init();
		$this->session = zgSession::init();

		$this->locale = zgLocale::init();

		parent::__construct();

		$this->basepath = 'http://' . $_SERVER["SERVER_NAME"] . $this->configuration->getConfiguration('pokendesign', 'application', 'basepath');
		$this->localpath = 'templates/' . $this->configuration->getConfiguration('pokendesign', 'application', 'templatepath');
		$this->templatepath = $this->basepath . '/templates/' . $this->configuration->getConfiguration('pokendesign', 'application', 'templatepath');

		$language = $this->session->getSessionVariable('language');
		if (!$language)
		{
			$url = $_SERVER["SERVER_NAME"];
			if (strpos(strtolower($url), 'design.de') > 0)
			{
				$this->session->setSessionVariable('language', '_de');
				$language = '_de';
			}
			else
			{
				$this->session->setSessionVariable('language', '_en');
				$language = '_en';
			}
		}

				$this->session->setSessionVariable('language', '_de');
				$language = '_de';

		$this->locale->loadLocale($language, 'configuration/locales.ini');
		$this->locale->setLocale($language);
		$this->localpath .= $language;
		$this->templatepath .= $language;
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
			$this->debug->write('login 1', 'message');
			$this->assign('username', $this->user->getUserdata('userdata_username'));
			parent::insertBlock('userloggedin');
		}
		else
		{
			$this->debug->write('login 0', 'message');
			parent::insertBlock('userunknown');
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
