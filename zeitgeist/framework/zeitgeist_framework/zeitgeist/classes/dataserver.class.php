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

		$this->database = new zgDatabase();
		$this->database->connect();
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
	public function createXMLDatasetFromSQL($sql, $database=null, $encoding='UTF-8', $rootElement='container')
	{
		$this->debug->guard();

		if ($database == null)
		{
			$database = $this->database;
		}

		$xmlDataset = '<?xml version="1.0" encoding="' . $encoding . "\" ?>\n";
		$xmlDataset .= '<' . $rootElement . ">\n";

		$res = $database->query($sql);

		$i = 1;
		while ($row = $database->fetchArray($res))
		{
			$xmlDataset .= "\t<element id=\"" . $i . "\">\n";

			foreach($row as $key => $value)
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
	 * Creates an xml dataset from an array
	 *
	 * @param string $array array with the dataset
	 * @param string $encoding the encoding of the final xml
	 * @param string $rootElement root element of the final xml
	 *
	 * @return string
	 */
	public function createXMLDatasetFromArray($arrDataset, $encoding='UTF-8', $rootElement='container')
	{
		$this->debug->guard();

		if (!is_array($arrDataset))
		{
			return false;
		}

		$xmlDataset = '<?xml version="1.0" encoding="' . $encoding . "\" ?>\n";
		$xmlDataset .= '<' . $rootElement . ">\n";

		$xmlArraydata = $this->_transformArrayRecursive($arrDataset);
		$xmlDataset .= $xmlArraydata;

		$xmlDataset .= '</' . $rootElement . ">\n";

		$this->debug->unguard(true);
		return $xmlDataset;
	}


	/**
	 * converts an array into xml
	 *
	 * @param array $array array to convert
	 * @param boolean $recursive true if called recursively
	 *
	 * @return string
	 */
	private function _transformArrayRecursive($array, $recursive=false)
	{
		static $depth;
		static $xmlData;

		if (!$recursive)
		{
			$xmlData = '';
		}

		foreach($array as $key => $value)
		{
			if (!is_array($value))
			{
				$tabs = '';
				for ($i=1; $i<=$depth+1; $i++)
				{
					$tabs .= "\t";
				}

				$attribute = '';
				if (preg_match("/^[0-9]\$/", $key))
				{
					$attribute = ' id="' . $key . '"';
					$key = 'element';
				}

				$xmlData .= $tabs . '<'. $key . $attribute . '>' . $value . '</' . $key . ">\n";
			}
			else
			{
				$depth += 1;
				$tabs = '';
				for ($i=1; $i<=$depth; $i++)
				{
					$tabs .= "\t";
				}

				$attribute = '';
				if (preg_match("/^[0-9]\$/", $key))
				{
					$attribute = ' id="' . $key . '"';
					$key = 'element';
				}
				else
				{
					$key = $key;
				}

				$xmlData .= $tabs . '<' . $key.$attribute . ">\n";
				$this->_transformArrayRecursive($value, true);
				$xmlData .= $tabs . '</' . $key . ">\n";
				$depth -= 1;
			}
		}

		return $xmlData;
	}


	/**
	 * Streams a given xml dataset to the browser as xml content
	 * Note: Headers should not be set at this point
	 *
	 * @param string $xmldata string containing the xml data
	 *
	 * @return boolean
	 */
	public function streamXMLDataset($xmldata)
	{
		$this->debug->guard();

		header('Content-type: text/xml');
		header('Pragma: public');
		header('Cache-control: private');
		header('Expires: -1');
		echo $xmldata;

		$this->debug->unguard(true);
		return true;
	}

}
?>
