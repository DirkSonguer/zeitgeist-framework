<?php

defined('TASKKUN_ACTIVE') or die();

class tkTemplate extends zgTemplate
{
	private $user;
	private $basepath;
	private $templatepath;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->user = zgUserhandler::init();
		
		parent::__construct();

		$this->basepath = $this->configuration->getConfiguration('taskkun', 'application', 'basepath');
		$this->templatepath = $this->basepath . '/templates/' . $this->configuration->getConfiguration('taskkun', 'application', 'templatepath');
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

		$templatestatus = parent::load($filename);

		$this->assign('basepath', $this->basepath);
		$this->assign('templatepath', $this->templatepath);

		$this->debug->unguard($templatestatus);
		return $templatestatus;
	}


	/**
	 * Shows the template buffer
	 *
	 * @param boolean $showmenu flag if the menu should be shown
	 *
	 * @return boolean
	 */
	public function show($showmenu=true)
	{
		$this->debug->guard();

		parent::insertUsermessages();

		if ( ($this->user->isLoggedIn()) && ($showmenu) )
		{
			if ($this->user->hasUserrole('Manager'))
			{
				$this->insertBlock('managernavigation');
			}
			elseif ($this->user->hasUserrole('Administrator'))
			{
				$this->insertBlock('adminnavigation');
				$this->insertBlock('managernavigation');
			}
			$this->insertBlock('navigation');
		}

		if ($this->user->isLoggedIn())
		{
			$this->insertBlock('logoutbutton');
		}

		$versioninfo = $this->configuration->getConfiguration('taskkun', 'application', 'versioninfo');
		$this->assign('versioninfo', $versioninfo);

		$showstatus = parent::show();

		$this->debug->unguard($showstatus);
		return $showstatus;
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
