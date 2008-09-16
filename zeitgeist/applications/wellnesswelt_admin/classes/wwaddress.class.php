<?php

defined('WELLNESSWELT_ACTIVE') or die();

class wwAddress
{
	private $user;
	private $basepath;
	private $templatepath;
	private $database;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->user = zgUserhandler::init();
		
		parent::__construct();

		$this->basepath = $this->configuration->getConfiguration('wellnesswelt', 'application', 'basepath');
		$this->templatepath = $this->basepath . '/templates/' . $this->configuration->getConfiguration('wellnesswelt', 'application', 'templatepath');
	}



}

?>
