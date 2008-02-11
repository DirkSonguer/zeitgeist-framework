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

class zgForm
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $configuration;

	protected $method;
	protected $enctype;
	protected $action;
	protected $premessage;
	protected $postmessage;
	protected $initial;
	protected $width;

	protected $groups;
	protected $formtemplate;

	public $formid;
	public $name;
	public $formelements = array();

	/**
	 * Class constructor
	 *
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	public function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();

		$this->database = new zgDatabase();
		$this->database->connect();

		$this->formtemplate = new zgTemplate();

		$this->formid = 'form_'.rand(10000,1000000000);
		$this->initial = false;
	}


	/**
	 * Loads a definition of a form
	 *
	 * @param string $filename filename of the configuration
	 *
	 * @return boolean
	 */
	public function load($filename)
	{
		$this->debug->guard();

		$this->configuration->loadConfiguration($this->formid, $filename);

		$this->name = $this->configuration->getConfiguration($this->formid, 'form', 'name');
		$this->method = $this->configuration->getConfiguration($this->formid, 'form', 'method');
		$this->enctype = $this->configuration->getConfiguration($this->formid, 'form', 'enctype');
		$this->action = $this->configuration->getConfiguration($this->formid, 'form', 'action');
		$this->premessage = $this->configuration->getConfiguration($this->formid, 'form', 'premessage');
		$this->postmessage = $this->configuration->getConfiguration($this->formid, 'form', 'postmessage');
		$this->width = $this->configuration->getConfiguration($this->formid, 'form', 'width');

		$this->groups = array();

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
	public function create()
	{
		$this->debug->guard();

		$link = '';
		$linksource = explode('.', $this->action);
		$link = $this->formtemplate->createLink($linksource[0], $linksource[1]);

		$this->formtemplate->load('forms/tableforms.tpl.html');

		$formdata = array();

		$formdata['form_name'] = $this->name;
		$formdata['form_method'] = $this->method;
		$formdata['form_enctype'] = $this->enctype;
		$formdata['form_action'] = $this->action;
		$formdata['form_premessage'] = $this->premessage;
		$formdata['form_postmessage'] = $this->postmessage;
		$formdata['form_width'] = $this->width;

		foreach ($this->groups as $group)
		{
			$formstring = '';
			foreach ($this->formelements as $elementname => $elementdata)
			{
				if ($elementdata->group == $group)
				{
					switch($elementdata->type)
					{
						case 'text':
							$formstring .= $this->_createTextelement($elementname, $elementdata);
							break;

						case 'password':
							$formstring .= $this->_createPasswordelement($elementname, $elementdata);
							break;

						case 'static':
							$formstring .= $this->_createStaticelement($elementname, $elementdata);
							break;

						case 'submit':
							$formstring .= $this->_createSubmitelement($elementname, $elementdata);
							break;
					}
				}
			}

			$this->formtemplate->assign('form_formdata', $formstring);
			$this->formtemplate->assignDataset($formdata);
			$this->formtemplate->insertBlock('formdata');
		}

		$this->formtemplate->insertBlock('formtemplate');

		$formstring = $this->formtemplate->getContent();

		$this->debug->unguard(true);
		return $formstring;
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
	 * Retrieves all parameters that are safe for the current module and action
	 * Returns an array with all parameters found safe
	 * Also creates an object in the objectcache with parameters found unsafe
	 *
	 * @param string $module name of the current module
	 * @param string $action name of the current action
	 *
	 * @return array
	 */
	public function assignDataset($dataset=array())
	{
		$this->debug->guard();

		if (!is_array($dataset))
		{
			$this->debug->write('Problem assigning dataset to form: given dataset is not an array', 'warning');
			$this->messages->setMessage('Problem assigning dataset to form: given dataset is not an array', 'warning');

			$this->debug->unguard(true);
			return true;
		}

		foreach ($dataset as $elementname => $elementvalue)
		{
			if (!empty($this->formelements[$elementname]))
			{
				$this->formelements[$elementname]->value = $elementvalue;
			}
		}

		$this->debug->unguard(true);
		return true;
	}


	/**
	 * Setup the form, basically doing the initializing
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
				$this->formelements[$elementname] = new zgFormelement();
				if (!empty($elementdata['pretext'])) $this->formelements[$elementname]->pretext = $elementdata['pretext'];
				if (!empty($elementdata['posttext'])) $this->formelements[$elementname]->posttext = $elementdata['posttext'];
				if (!empty($elementdata['type'])) $this->formelements[$elementname]->type = $elementdata['type'];
				if (!empty($elementdata['value'])) $this->formelements[$elementname]->value = $elementdata['value'];
				if (!empty($elementdata['required'])) $this->formelements[$elementname]->required = $elementdata['required'];
				if (!empty($elementdata['minlength'])) $this->formelements[$elementname]->minlength = $elementdata['minlength'];
				if (!empty($elementdata['maxlength'])) $this->formelements[$elementname]->maxlength = $elementdata['maxlength'];
				if (!empty($elementdata['expected'])) $this->formelements[$elementname]->expected = $elementdata['expected'];
				if (!empty($elementdata['style'])) $this->formelements[$elementname]->style = $elementdata['style'];

				if (!empty($elementdata['group']))
				{
					$this->formelements[$elementname]->group = $elementdata['group'];
					$this->groups[$elementdata['group']] = $elementdata['group'];
				}

				if (!empty($elementdata['errormsg']))
				{
					$this->formelements[$elementname]->errormsg = explode('||', $elementdata['errormsg']);
				}
			}
			else
			{
				$this->debug->write('Problem reading out elementin form defintion (' . $this->name . '->' . $elementname . ')', 'warning');
				$this->messages->setMessage('Problem reading out elementin form defintion (' . $this->name . '->' . $elementname . ')', 'warning');
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
			if ($elementdata->type != 'static')
			{
				$valid = $this->_validateElement($elementname, $elementdata, $formdata);

				$elementdata->valid = $valid;
				if ($valid)
				{
					$elementdata->value = $formdata[$elementname];
				}
				else
				{
					$formvalid = false;
					$elementdata->value = '';
				}
			}
		}

		$this->debug->unguard($formvalid);
		return $formvalid;
	}


	/**
	 * Creates a text element and returns the code to put into the template
	 *
	 * @access protected
	 *
	 * @param string $elementname name of the element to create
	 * @param array $elementdata data of the element to create
	 *
	 * @return string
	 */
	protected function _createTextelement($elementname, $elementdata)
	{
		$this->debug->guard(true);

		$elementcontent = array();
		$elementcontent['element_pretext'] = $elementdata->pretext;
		if ($elementdata->required == 1) $elementcontent['element_required'] = ' *';
		if ($elementdata->maxlength > 0) $elementcontent['element_maxlength='] = $elementdata->maxlength;
		$elementcontent['element_name'] = $this->name . '[' . $elementname . ']';
		$elementcontent['element_value'] = $elementdata->value;

		$this->formtemplate->assignDataset($elementcontent);

		if ($elementdata->posttext != '')
		{
			$this->formtemplate->assign('element_posttext', $elementdata->posttext);
			$this->formtemplate->insertBlock('text_posttext');
		}
		else
		{
			$this->formtemplate->assign('element_posttext', '');
		}

		if ( ($elementdata->valid == false) && ($this->_showError($elementdata) != '') && (!$this->initial) )
		{
			$this->formtemplate->assign('element_formerror', ' class="formerror"');
			$this->formtemplate->assign('element_errormsg', $this->_showError($elementdata));
			$this->formtemplate->insertBlock('text_errormsg');
		}
		else
		{
			$this->formtemplate->assign('element_formerror', '');
			$this->formtemplate->assign('element_errormsg', '');
		}

		$ret = $this->formtemplate->getBlockContent('textelement');

		$this->debug->unguard(true);
		return $ret;
	}


	/**
	 * Creates a password element and returns the code to put into the template
	 *
	 * @access protected
	 *
	 * @param string $elementname name of the element to create
	 * @param array $elementdata data of the element to create
	 *
	 * @return string
	 */
	protected function _createPasswordelement($elementname, $elementdata)
	{
		$this->debug->guard(true);

		$elementcontent = array();
		$elementcontent['element_pretext'] = $elementdata->pretext;
		if ($elementdata->required == 1) $elementcontent['element_required'] = ' *';
		if ($elementdata->maxlength > 0) $elementcontent['element_maxlength='] = $elementdata->maxlength;
		$elementcontent['element_name'] = $this->name . '[' . $elementname . ']';

		$this->formtemplate->assignDataset($elementcontent);

		if ($elementdata->posttext != '')
		{
			$this->formtemplate->assign('element_posttext', $elementdata->posttext);
			$this->formtemplate->insertBlock('password_posttext');
		}
		else
		{
			$this->formtemplate->assign('element_posttext', '');
		}

		if ( ($elementdata->valid == false) && ($this->_showError($elementdata) != '') && (!$this->initial) )
		{
			$this->formtemplate->assign('element_formerror', ' class="formerror"');
			$this->formtemplate->assign('element_errormsg', $this->_showError($elementdata));
			$this->formtemplate->insertBlock('password_errormsg');
		}
		else
		{
			$this->formtemplate->assign('element_formerror', '');
			$this->formtemplate->assign('element_errormsg', '');
		}

		$ret = $this->formtemplate->getBlockContent('passwordelement');

		$this->debug->unguard(true);
		return $ret;
	}


	/**
	 * Creates a submit element and returns the code to put into the template
	 *
	 * @access protected
	 *
	 * @param string $elementname name of the element to create
	 * @param array $elementdata data of the element to create
	 *
	 * @return string
	 */
	protected function _createSubmitelement($elementname, $elementdata)
	{
		$this->debug->guard(true);

		$elementcontent = array();
		$elementcontent['element_name'] = $this->name . '[' . $elementname . ']';
		$elementcontent['element_value'] = $elementdata->value;

		$this->formtemplate->assignDataset($elementcontent);

		$ret = $this->formtemplate->getBlockContent('submitelement');

		$this->debug->unguard(true);
		return $ret;
	}


	/**
	 * Creates a static element and returns the code to put into the template
	 *
	 * @access protected
	 *
	 * @param string $elementname name of the element to create
	 * @param array $elementdata data of the element to create
	 *
	 * @return string
	 */
	protected function _createStaticelement($elementname, $elementdata)
	{
		$this->debug->guard(true);
		$elementstring = '';

		$elementcontent = array();
		$elementcontent['element_pretext'] = $elementdata->pretext;
		$elementcontent['element_name'] = $this->name . '[' . $elementname . ']';
		$elementcontent['element_value'] = $elementdata->value;

		$this->formtemplate->assignDataset($elementcontent);
		if ($elementdata->posttext != '')
		{
			$this->formtemplate->assign('element_posttext', $elementdata->posttext);
			$this->formtemplate->insertBlock('static_posttext');
		}

		$ret = $this->formtemplate->getBlockContent('staticelement');

		$this->debug->unguard(true);
		return $ret;
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

class zgFormelement
{
	public $pretext;
	public $posttext;
	public $type;
	public $value;
	public $required;
	public $minlength;
	public $maxlength;
	public $expected;
	public $style;
	public $errormsg;
	public $group;
	public $currentErrormsg;

	public $valid;

	public function __construct()
	{
		$this->pretext = '';
		$this->posttext = '';
		$this->type = '';
		$this->value = '';
		$this->required = 0;
		$this->minlength = 0;
		$this->maxlength = 0;
		$this->expected = '';
		$this->style = '';
		$this->group = 0;
		$this->errormsg = '';
		$this->currentErrormsg = 0;

		$this->valid = false;
	}

}

?>