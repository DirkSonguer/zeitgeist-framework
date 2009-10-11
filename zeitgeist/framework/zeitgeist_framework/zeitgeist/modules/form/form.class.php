<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Forms class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FORMS
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgForm
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;

	public $formid;
	public $name;
	public $formelements = array();
	public $initial;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->formid = 'form_' . uniqid(rand(), true);
		$this->initial = false;
	}


}

class zgFormelement
{
	public $value;
	public $required;
	public $expected;
	public $errormsg;
	public $currentErrormsg;

	public $valid;

	public function __construct()
	{
		$this->value = '';
		$this->required = 0;
		$this->type = '';
		$this->errormsg = array();
		$this->currentErrormsg = 0;

		$this->valid = false;
	}

}

?>