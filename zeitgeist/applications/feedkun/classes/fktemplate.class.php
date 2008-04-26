<?php

defined('FEEDKUN_ACTIVE') or die();

class fkTemplate extends zgTemplate
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
	public function show($showmenu=true)
	{
		$this->debug->guard();

		$ret = true;
		parent::insertUsermessages();

		$versioninfo = $this->configuration->getConfiguration('feedkun', 'application', 'versioninfo');
		$this->assign('versioninfo', $versioninfo);

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
