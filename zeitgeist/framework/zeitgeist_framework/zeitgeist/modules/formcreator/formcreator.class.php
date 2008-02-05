<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Formhandler class
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
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

		$this->formid = 'form_'.rand(10000,1000000000);
		$this->initial = false;
	}


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


	public function process($parameters=array())
	{
		$this->debug->guard();

		if ( (empty($parameters[$this->name])) || (!is_array($parameters[$this->name])) )
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


	public function create()
	{
		$this->debug->guard();

		$link = '';
		$linksource = explode('.', $this->action);
		$tpl = new zgTemplate();
		$link = $tpl->createLink($linksource[0], $linksource[1]);

		$formstring = '';
		$formstring .= '<form method="' . $this->method . '" action="' . $link . '" name="' . $this->name . '" enctype="' . $this->enctype . "\">\n";

		$formstring .= "\t<table class=\"formdata\" cellpadding=\"5\" cellspacing=\"0\" width=\"" . $this->width . "\" border=\"0\">\n";
		$formstring .= 	"\t\t<tr>\n\t\t\t" . '<td colspan="2"><p>' . $this->premessage . '</p></td>' . "\n\t\t</tr>\n";
		$i = 1;
		foreach ($this->groups as $group)
		{
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

			if ($i < count($this->groups)) $formstring .= "<tr><td colspan=\"2\"><hr /></td></tr>";
			$i++;
		}
		$formstring .= 	"\t\t<tr>\n\t\t\t" . '<td colspan="2"><p>' . $this->postmessage . '</p></td>' . "\n\t\t</tr>\n";
		$formstring .= "\t</table>\n";
		$formstring .= "</form>\n";

		$this->debug->unguard($formstring);
		return $formstring;
	}


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


	protected function _createTextelement($elementname, $elementdata)
	{
		$this->debug->guard(true);
		$elementstring = '';

		$elementstring .= "\t\t<tr";
		if ( ($elementdata->valid == false) && (!$this->initial) ) $elementstring .= ' class="formerror"';
		$elementstring .= ">\n\t\t\t<td valign=\"top\"><p>";
		$elementstring .= $elementdata->pretext;
		if ($elementdata->required == 1) $elementstring .= ' *';
		$elementstring .= "</p></td>\n";
		$elementstring .= "\t\t\t<td><input type=\"text\"";
		if ($elementdata->maxlength > 0) $elementstring .= ' maxlength="' . $elementdata->maxlength . '"';
		$elementstring .= ' name="' . $this->name . '[' . $elementname . ']"';
		$elementstring .= ' value="' . $elementdata->value . '"';
		$elementstring .= ' class="' . $elementdata->style . '"';
		$elementstring .= " />\n";
		if ($elementdata->posttext != '') $elementstring .= "\t\t\t<br />" . '<span class="small">' . $elementdata->posttext . "</span>\n";
		if ( ($elementdata->valid == false) && ($this->_showError($elementdata) != '') ) $elementstring .= "\t\t\t<br />" . '<span class="formerrormsg">' . $this->_showError($elementdata) . "</span>\n";
		$elementstring .= "\t\t\t</td>\n\t\t</tr>\n";

		$this->debug->unguard(true);
		return $elementstring;
	}


	protected function _createPasswordelement($elementname, $elementdata)
	{
		$this->debug->guard(true);
		$elementstring = '';

		$elementstring .= "\t\t<tr";
		if ( ($elementdata->valid == false) && (!$this->initial) ) $elementstring .= ' class="formerror"';
		$elementstring .= ">\n\t\t\t<td valign=\"top\"><p>";
		$elementstring .= $elementdata->pretext;
		if ($elementdata->required == 1) $elementstring .= ' *';
		$elementstring .= "</p></td>\n";
		$elementstring .= "\t\t\t<td><input type=\"password\"";
		if ($elementdata->maxlength > 0) $elementstring .= ' maxlength="' . $elementdata->maxlength . '"';
		$elementstring .= ' name="' . $this->name . '[' . $elementname . ']"';
		$elementstring .= ' value="' . $elementdata->value . '"';
		$elementstring .= ' class="' . $elementdata->style . '"';
		$elementstring .= " />\n";
		if ($elementdata->posttext != '') $elementstring .= "\t\t\t<br />" . '<span class="small">' . $elementdata->posttext . "</span>\n";
		if ( ($elementdata->valid == false) && ($this->_showError($elementdata) != '') ) $elementstring .= "\t\t\t<br />" . '<span class="formerrormsg">' . $this->_showError($elementdata) . "</span>\n";
		$elementstring .= "\t\t\t</td>\n\t\t</tr>\n";

		$this->debug->unguard(true);
		return $elementstring;
	}


	protected function _createSubmitelement($elementname, $elementdata)
	{
		$this->debug->guard(true);
		$elementstring = '';

		$elementstring .= "\t\t<tr>\n\t\t\t<td>&nbsp;</td>\n";
		$elementstring .= "\t\t\t<td><input type=\"submit\"";
		$elementstring .= ' name="' . $this->name . '[' . $elementname . ']"';
		$elementstring .= ' value="' . $elementdata->value . '"';
		$elementstring .= ' class="' . $elementdata->style . '"';
		$elementstring .= " />\n";
		$elementstring .= "\t\t\t</td>\n\t\t</tr>\n";

		$this->debug->unguard(true);
		return $elementstring;
	}


	protected function _createStaticelement($elementname, $elementdata)
	{
		$this->debug->guard(true);
		$elementstring = '';

		$elementstring .= "\t\t<tr>\n\t\t\t<td valign=\"top\"><p>";
		$elementstring .= $elementdata->pretext;
		$elementstring .= "</p></td>\n";
		$elementstring .= "\t\t\t<td><p class=\"" . $elementdata->style . "\">" . $elementdata->value;
		if ($elementdata->posttext != '') $elementstring .= " " . '<span class="small">' . $elementdata->posttext . "</span></p></td>\n\t\t</tr>\n";

		$this->debug->unguard(true);
		return $elementstring;
	}


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