<?php


class testFunctions
{
	public $database;
	private $sqldata;
	
	public function __construct()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
		
		$this->sqldata = '';
    }


	function createZeitgeistTable($table)
	{
		if (empty($this->sqldata))
		{
			$filehandler = new zgFiles();
			$this->sqldata = $filehandler->getFileContent(dirname(__FILE__).'/testdata/zeitgeist.sql');
		}
		
		$startposition = strpos($this->sqldata, 'CREATE TABLE IF NOT EXISTS `'.$table.'`');
		$endposition = strpos($this->sqldata, '-- Daten für Tabelle `'.$table.'`');
		$endposition = $endposition - $startposition - 6;
		
		$sqlsnippet = substr($this->sqldata, $startposition, $endposition);
		$this->database->query($sqlsnippet);

		return true;
	}


	function dropZeitgeistTable($table)
	{
		$this->database->query('DROP TABLE '.$table);

		return true;
	}
}
?>

