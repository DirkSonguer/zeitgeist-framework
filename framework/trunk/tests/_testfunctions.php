<?php


class testFunctions
{
	public $database;
	
	public function __construct()
	{
		$this->database = new zgDatabase();
		$this->database->connect();
    }

	function createZeitgeistTable($table)
	{
		
		$filehandler = new zgFiles();
		$sqldata = $filehandler->getFileContent(dirname(__FILE__).'/testdata/zeitgeist.sql');
		
		$startposition = strpos($sqldata, 'CREATE TABLE IF NOT EXISTS `'.$table.'`');
		$endposition = strpos($sqldata, '-- Daten für Tabelle `'.$table.'`');
		$endposition = $endposition - $startposition - 6;
		
		$sqldata = substr($sqldata, $startposition, $endposition);
		$this->database->query($sqldata);

		return true;
	}


	function dropZeitgeistTable($table)
	{
		
		$this->database->query('DROP TABLE '.$table);

		return true;
	}
}
?>

