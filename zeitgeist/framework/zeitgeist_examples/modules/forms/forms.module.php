<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class forms
{
	public $debug;

	public function __construct()
	{
		$this->debug = zgDebug::init();
	}
	

	// This is the main action for this module
	public function index($parameters=array())
	{
		$this->debug->guard();

		// We use templates in this example. You should check the template
		// example beforehand to know what is going on
		$tpl = new zgTemplate();
		$tpl->load('_additional_files/example_form.tpl.html');
		
		// Get a new form object
		$exampleform = new zgForm();

		// Load a form definition. The file defines the fields of the form
		// Take a look at the file, it's basically derived from the module
		// ini file definiton
		$exampleform->load('_additional_files/example.form.ini');

		// This validates the REQUEST input against the form definition
		// Note that the parameters have to be validated by the controller
		// to get this far, so they are wrapped in an array
		$valid = $exampleform->validate($parameters);
		
		// If all form fields are as expected, the validate method returns
		// true
		if ($valid) echo '<p><b>All form fields are valid</b></p>';

		// This inserts the validated fields and enters the error messages
		// in the template fields
		$exampleform->insert($tpl);

		$tpl->show();

		$this->debug->unguard(true);		
		return true;
	}

}
?>
