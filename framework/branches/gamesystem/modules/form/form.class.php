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

	public $id;

	public $name;
	public $method;

	public $initial;
	public $formelements = array();

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

		$this->id = 'form_' . uniqid(rand(), true);
		$this->initial = false;
	}


	/**
	 * Loads a definition of a form
	 *
	 * @param string $configfile filename of the configuratio with the form definition
	 *
	 * @return boolean
	 */
	public function load($configfile)
	{
		$this->debug->guard();

		$this->configuration->loadConfiguration($this->id, $configfile);

		$this->name = $this->configuration->getConfiguration($this->id, 'form', 'name');
		$this->method = $this->configuration->getConfiguration($this->id, 'form', 'method');

		if (!$this->_setupForm())
		{
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Validate a form
	 *
	 * @param array $parameters array with parameters of the call
	 *
	 * @return boolean
	 */
	public function validate($parameters=array())
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

		$valid = true;
		foreach ($this->formelements as $elementname => $elementdata)
		{
			$elementdata->valid = $this->_validateElement($elementname, $elementdata, $formdata);

			if ($elementdata->valid)
			{
				if (empty($formdata[$elementname])) $formdata[$elementname] = '';

				if ($elementdata->stripslashes == 'true')
				{
					$formdata[$elementname] = stripslashes($formdata[$elementname]);
				}

				if ($elementdata->escape == 'true')
				{
					$formdata[$elementname] = mysql_real_escape_string($formdata[$elementname]);
				}

				$elementdata->value = $formdata[$elementname];
			}
			else
			{
				$valid = false;
				$elementdata->value = '';
			}
		}

		$this->debug->unguard($valid);
		return $valid;
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
			$this->formelements[$elementname]->valid = $validation;
		}
		else
		{
			$this->debug->write('Problem setting the status of the element: element not found in form configuration', 'warning');
			$this->messages->setMessage('Problem setting the status of the element: element not found in form configuration', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Gets the value of a given element
	 *
	 * @param string $elementname name of the element
	 *
	 * @return boolean
	 */
	public function getElementValue($elementname)
	{
		$this->debug->guard();

		if (empty($this->formelements[$elementname]))
		{
			$this->debug->write('Problem getting the value of the element: element not found in form configuration', 'warning');
			$this->messages->setMessage('Problem getting the value of the element: element not found in form configuration', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = $this->formelements[$elementname]->value;

		$this->debug->unguard($ret);
		return $ret;
	}


	/**
	 * Sets the value of a given element
	 *
	 * @param string $elementname name of the element
	 * @param string $value value to insert
	 *
	 * @return boolean
	 */
	public function setElementValue($elementname, $value)
	{
		$this->debug->guard();

		if (!empty($this->formelements[$elementname]))
		{
			$this->formelements[$elementname]->value = $value;
		}
		else
		{
			$this->debug->write('Problem setting the value of the element: element not found in form configuration', 'warning');
			$this->messages->setMessage('Problem setting the value of the element: element not found in form configuration', 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Inserts status and values of the form into a template
	 *
	 * @return boolean
	 */
	public function insert(&$template)
	{
		$this->debug->guard();

		foreach ($this->formelements as $elementname => $elementdata)
		{
			$template->assign($elementname . ':value', $elementdata->value);

			if ( ($elementdata->valid == false) && (!$this->initial) )
			{
				$template->assign($elementname . ':errormessage', $elementdata->errormsg);
				$template->insertBlock($elementname . ':errorblock');
			}
			else
			{
				$template->assign($elementname . ':errormessage', '');
			}
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
		$elements = $this->configuration->getConfiguration($this->id, 'elements');

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
				$this->formelements[$elementname] = new zgFormelement();
				if (!empty($elementdata['value'])) $this->formelements[$elementname]->value = $elementdata['value'];
				if (!empty($elementdata['required'])) $this->formelements[$elementname]->required = $elementdata['required'];
				if (!empty($elementdata['expected'])) $this->formelements[$elementname]->expected = $elementdata['expected'];
				if ( (!empty($elementdata['escape'])) && ($elementdata['escape'] == 'true') ) $this->formelements[$elementname]->escape = true;
				if ( (!empty($elementdata['stripslashes'])) && ($elementdata['stripslashes'] == 'true') ) $this->formelements[$elementname]->stripslashes = true;
				if (!empty($elementdata['errormsg'])) $this->formelements[$elementname]->errormsg = $elementdata['errormsg'];
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
	
}


class zgFormelement
{
	public $value;
	public $required;
	public $expected;
	public $escape;
	public $stripslashes;
	public $errormsg;

	public $valid;

	public function __construct()
	{
		$this->value = '';
		$this->required = 0;
		$this->expected = '';
		$this->errormsg = '';

		$this->escape = false;
		$this->stripslashes = false;
		$this->valid = false;
	}

}

?>