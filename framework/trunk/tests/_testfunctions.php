<?php


class testFunctions
{
	public $database;
	private $sqldata;


	public function __construct( )
	{
		$this->database = new zgDatabase( );
		$this->database->connect( );

		$this->sqldata = '';
	}


	/**
	 * Creates a new zeitgeist table with the given name
	 * As a reference for creating the table the default database
	 * dump in /_additional_material/zeitgeist.sql will be used
	 * If the given table name is not found in the default dump,
	 * it will be ignored
	 *
	 * @param string $table name of the table to create
	 *
	 * @return boolean
	 */
	function createZeitgeistTable( $table )
	{
		if ( empty( $this->sqldata ) )
		{
			$filehandler = new zgFiles( );
			$this->sqldata = $filehandler->getFileContent( ZEITGEIST_ROOTDIRECTORY . '_additional_material/zeitgeist.sql' );
		}

		$startposition = strpos( $this->sqldata, 'CREATE TABLE IF NOT EXISTS `' . $table . '`' );
		$endposition = strpos( $this->sqldata, '-- Daten für Tabelle `' . $table . '`' );
		$endposition = $endposition - $startposition - 6;

		$sqlsnippet = substr( $this->sqldata, $startposition, $endposition );
		$this->database->query( $sqlsnippet );

		return true;
	}


	/**
	 * Drops an existing zeitgeist table with the given name
	 *
	 * @param string $table name of the table to drop
	 *
	 * @return boolean
	 */
	function dropZeitgeistTable( $table )
	{
		$this->database->query( 'DROP TABLE ' . $table );

		return true;
	}
}

?>