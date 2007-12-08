<?php
/**
 * Zeitgeist Browsergame Framework
 * http://www.zeitgeist-framework.com
 *
 * Dataserver class
 * 
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 * 
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 * 
 * @package ZEITGEIST
 * @subpackage ZEITGEIST MESSAGES
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgDataserver
{
	protected $debug;
	protected $messages;
	protected $database;
	protected $managedDatabase;
	protected $configuration;
	
	/**
	 * Class constructor
	 * 
	 * The constructor is set to private to prevent files from calling the class as a class instead of a singleton.
	 */
	public  function __construct()
	{
		$this->debug = zgDebug::init();
		$this->messages = zgMessages::init();
		$this->configuration = zgConfiguration::init();
		
		$this->database = new zgDatabase();
		$this->database->connect();
		
		$mdb_server = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_server');
		$mdb_username = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_username');
		$mdb_userpw = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_userpw');
		$mdb_database = $this->configuration->getConfiguration('administrator', 'databases', 'manageddb_database');
		$this->managedDatabase = new zgDatabase();
		$this->managedDatabase->connect($mdb_server, $mdb_username, $mdb_userpw, $mdb_database);		
	}

	
	/**
	 * Creates an xml dataset from an sql query
	 * 
	 * @param string $sql string with the sql statement
	 * @param string $encoding the encoding of the final xml
	 * @param string $rootElement root element of the final xml
	 * 
	 * @return string 
	 */
	public function createXMLDatasetFromSQL($sql, $encoding='UTF-8', $rootElement='container')
	{
		$this->debug->guard();
		
	    $xmlDataset = '<?xml version="1.0" encoding="' . $encoding . "\" ?>\n";
	    $xmlDataset .= '<' . $rootElement . ">\n";

		$res = $this->managedDatabase->query($sql);
	
		$i = 1;
	    while ($row = $this->managedDatabase->fetchArray($res))
	    {
	        $xmlDataset .= "\t<element id=\"" . $i . "\">\n";

	        foreach($row as $key=>$value)
	        {
	            $value = htmlspecialchars($value);
	            $xmlDataset .= "\t\t<{$key}>{$value}</{$key}>\n";
	        }

	        $xmlDataset .= "\t</element>\n";
	        $i++;
	    }

		$xmlDataset .= '</' . $rootElement . ">\n";
	    
		$this->debug->unguard(true);
	    return $xmlDataset;
	}

	
	/**
	 * Streams an xml dataset to the browser
	 * Note: Headers should not be set at this point
	 * 
	 * @param string $xmlData xml data to stream
	 * 
	 * @return boolean 
	 */
	public function streamXMLDataset($xmlData)
	{
		$this->debug->guard();
		
		header('Content-type: text/xml');
		header('Pragma: public');
		header('Cache-control: private');
		header('Expires: -1');
		echo $xmlData;
		
		$this->debug->unguard(true);
		return true;		
	}

}
?>
