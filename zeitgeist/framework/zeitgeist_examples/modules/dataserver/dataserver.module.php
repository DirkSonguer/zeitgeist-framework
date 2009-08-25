<?php

defined('ZGEXAMPLES_ACTIVE') or die();

class dataserver
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

		// The dataserver is a class that helps you creating suitable
		// output for AJAX requests
		$dataserver = new zgDataserver();
		
		// Use it to create an XML dataset from an array
		$testarray = array();
		$testarray['key1'] = 'value1';
		$testarray['key2'] = 'value2';
		$xmlcontent = $dataserver->createXMLDatasetFromArray($testarray);
		echo $xmlcontent.'<br /><br />';

		// Or use it to create an XML set from a database query
		$xmlcontent = $dataserver->createXMLDatasetFromSQL('SELECT * FROM actions');
		echo $xmlcontent.'<br /><br />';

		// Use this to stream XML data - it sets the headers accordingly
		// and just streams the XML
		// Won't work here because the header are already set
		//$dataserver->streamXMLDataset($xmlcontent);

		$this->debug->unguard(true);		
		return true;
	}

}
?>
