<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class templates
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

		// Create a new object of type template
		$tpl = new zgTemplate();
		
		// Load the example template
		// Open it and take a look at how it works
		$tpl->load('_additional_files/example_template.tpl.html');

		// Assign a value to a template var
		$tpl->assign('examplecontent', 'Hello, Template!');
		
		// The createLink() method can be used to create a complete link
		// All paths etc. are added automatically
		// The parameters are createLink(MODULE, ACTION)
		$tpl->assign('manuallink', $tpl->createLink('main', 'index'));

		// You can also assign the contents of an array automatically to
		// the template. The keys are mapped to the template vars, the
		// values are assigned as content
		$contentarray = array();
		$contentarray['hello'] = 'Hello';
		$contentarray['template'] = 'Template';
		$tpl->assignDataset($contentarray);
		
		// Blocks do not show up by default, they have to be inserted first
		$tpl->assign('blockcontent', 'Hello, Block 1');
		$tpl->insertBlock('exampleblock');
		
		// However blocks can be inserted multiple times. the contents of the
		// template vars inside the blocks are parsed when a block is inserted
		$tpl->assign('blockcontent', 'Hello, Block 2');
		$tpl->insertBlock('exampleblock');

		// Sends the contents of the template buffer to the output
		$tpl->show();

		$this->debug->unguard(true);		
		return true;
	}

}
?>
