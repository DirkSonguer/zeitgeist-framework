<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Formhandler class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FORMHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgStaticform
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


	/**
	 * Loads a definition of a form
	 *
	 * @param string $templatefile filename of the template to fill
	 * @param string $configfile filename of the configuratio with the form definition
	 *
	 * @return boolean
	 */
	public function load($configfile)
	{
		$this->debug->guard();

		$this->configuration->loadConfiguration($this->formid, $configfile);

		$this->name = $this->configuration->getConfiguration($this->formid, 'form', 'name');
		$this->method = $this->configuration->getConfiguration($this->formid, 'form', 'method');

		if (!$this->_setupForm())
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Processes a form, validating it and returns if it is valid
	 *
	 * @param array $parameters array with parameters of the call
	 *
	 * @return boolean
	 */
	public function process($parameters=array())
	{
		$this->debug->guard();

		if ( (empty($parameters[$this->name])) || (count($parameters[$this->name]) < 1) )
		{
			$this->initial = true;

			$this->debug->write('Problem processing the form data: no formdata found in parameters', 'warning');
			$this->messages->setMessage('Problem processing the form data: no formdata found in parameters', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$formdata = $parameters[$this->name];

		$valid = false;
		$valid = $this->_validateElements($formdata);

		$this->debug->unguard($valid);
		return $valid;
	}


	/**
	 * Creates a form according to its configuration
	 * Also handles error messages and validation errors of the processing
	 *
	 * @return string
	 */
	public function create(&$template)
	{
		$this->debug->guard();

		foreach ($this->formelements as $elementname => $elementdata)
		{

			$template->assign($elementname . ':value', $elementdata->value);

			if ( ($elementdata->valid == false) && ($this->_showError($elementdata) != '') && (!$this->initial) )
			{
				$template->assign($elementname . ':formerror', ' class="formerror"');
				$template->assign($elementname . ':errormsg', $this->_showError($elementdata));
				$template->insertBlock($elementname . ':errormsg');
			}
			else
			{
				$template->assign($elementname . ':formerror', '');
				$template->assign($elementname . ':errormsg', '');
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Validate a given element
	 *
	 * @param string $elementname name of the element to validate
	 * @param boolean $validation state of validation
	 *
	 * @return boolean
	 */
	public function validateElement($elementname, $validation=true)
	{
		$this->debug->guard();

		if (!empty($this->formelements[$elementname]))
		{
			$this->formelements[$elementname]->valid = false;
		}
		else
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Setup the form, basically doing the initialisation
	 *
	 * @access protected
	 *
	 * @return boolean
	 */
	protected function _setupForm()
	{
		$this->debug->guard();

		$elements = array();
		$elements = $this->configuration->getConfiguration($this->formid, 'elements');

		if (count($elements) < 1)
		{
			$this->debug->write('Problem loading the form: no form elements could be found', 'warning');
			$this->messages->setMessage('Problem loading the form: no form elements could be found', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		foreach ($elements as $elementname => $elementdata)
		{
			if (is_array($elementdata))
			{
				$this->formelements[$elementname] = new zgStaticFormelement();
				if (!empty($elementdata['type'])) $this->formelements[$elementname]->type = $elementdata['type'];
				if (!empty($elementdata['value'])) $this->formelements[$elementname]->value = $elementdata['value'];
				if (!empty($elementdata['required'])) $this->formelements[$elementname]->required = $elementdata['required'];
				if (!empty($elementdata['minlength'])) $this->formelements[$elementname]->minlength = $elementdata['minlength'];
				if (!empty($elementdata['maxlength'])) $this->formelements[$elementname]->maxlength = $elementdata['maxlength'];
				if (!empty($elementdata['expected'])) $this->formelements[$elementname]->expected = $elementdata['expected'];

				if (!empty($elementdata['errormsg']))
				{
					$this->formelements[$elementname]->errormsg = explode('||', $elementdata['errormsg']);
				}
			}
			else
			{
				$this->debug->write('Problem reading out element in form defintion (' . $this->name . '->' . $elementname . ')', 'warning');
				$this->messages->setMessage('Problem reading out element in form defintion (' . $this->name . '->' . $elementname . ')', 'warning');
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Validates a given element, checking the element against the expected value
	 *
	 * @access protected
	 *
	 * @param string $elementname name of the element to validate
	 * @param array $elementdata data of the element
	 * @param array $formdata data of the submitted form
	 *
	 * @return boolean
	 */
	protected function _validateElement($elementname, $elementdata, $formdata)
	{
		$this->debug->guard(true);

		if (!empty($formdata[$elementname]))
		{
			if ($elementdata->expected == '')
			{
				$this->debug->unguard(true);
				return true;
			}

			$ret = preg_match($elementdata->expected, $formdata[$elementname]);

			if ($ret === false)
			{
				$this->debug->unguard(false);
				return false;
			}

			if ($ret !== 0)
			{
				$this->debug->unguard(true);
				return true;
			}
		}
		else
		{
			if ($elementdata->required == 0)
			{
				$this->debug->unguard(true);
				return true;
			}
		}

		$this->debug->unguard(false);
		return false;
	}


	/**
	 * Validates all elements of a form
	 *
	 * @access protected
	 *
	 * @param array $formdata data of the submitted form
	 *
	 * @return boolean
	 */
	protected function _validateElements($formdata)
	{
		$this->debug->guard();

		$formvalid = true;
		foreach ($this->formelements as $elementname => $elementdata)
		{
			$valid = $this->_validateElement($elementname, $elementdata, $formdata);

			$elementdata->valid = $valid;
			if ($valid)
			{
				if (empty($formdata[$elementname])) $formdata[$elementname] = '';
				$elementdata->value = $formdata[$elementname];
			}
			else
			{
				$formvalid = false;
				$elementdata->value = '';
			}
		}

		$this->debug->unguard($formvalid);
		return $formvalid;
	}


	/**
	 * Fill the data structure with an error message
	 *
	 * @access protected
	 *
	 * @param array $elementdata data of the element to create
	 *
	 * @return string
	 */
	protected function _showError($elementdata)
	{
		$this->debug->guard(true);

		if (is_array($elementdata->errormsg))
		{
			$ret = $elementdata->errormsg[$elementdata->currentErrormsg];
		}
		else
		{
			$ret = $elementdata->errormsg;
		}

		$this->debug->unguard($ret);
		return $ret;
	}

}

class zgStaticFormelement
{
	public $type;
	public $value;
	public $required;
	public $minlength;
	public $maxlength;
	public $expected;
	public $errormsg;
	public $currentErrormsg;

	public $valid;

	public function __construct()
	{
		$this->type = '';
		$this->value = '';
		$this->required = 0;
		$this->minlength = 0;
		$this->maxlength = 0;
		$this->expected = '';
		$this->errormsg = '';
		$this->currentErrormsg = 0;

		$this->valid = false;
	}

}

?>