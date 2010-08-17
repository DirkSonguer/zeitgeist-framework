<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Filehandler class
 *
 * A simple file handling class that wraps the most common functionalities
 * with function guarding
 *
 * @author Dirk Songür <dirk@zeitalter3.de>
 * @license MIT License <http://creativecommons.org/licenses/MIT/>
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FILEHANDLER
 */

defined( 'ZEITGEIST_ACTIVE' ) or die( );

class zgFiles
{
	protected $debug;
	protected $messages;
	protected $configuration;


	/**
	 * Class constructor
	 */
	public function __construct( )
	{
		$this->debug = zgDebug::init( );
		$this->messages = zgMessages::init( );
		$this->configuration = zgConfiguration::init( );
	}


	/**
	 * Checks if a given file exists
	 *
	 * @param string $filename name of the file to check for
	 *
	 * @return boolean
	 */
	protected function _fileAvailable( $filename )
	{
		$this->debug->guard( true );

		$ret = file_exists( $filename );

		$this->debug->unguard( $ret );
		return $ret;
	}


	/**
	 * Returns the content of the given file
	 *
	 * @param string $filename name of the file to get content from
	 *
	 * @return binary
	 */
	public function getFileContent( $filename )
	{
		$this->debug->guard( true );

		if ( !$this->_fileAvailable( $filename ) )
		{
			$this->debug->write( 'Could not open file: "' . $filename . '"', 'warning' );
			$this->messages->setMessage( 'Could not open file: "' . $filename . '"', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$filehandle = fopen( $filename, "r" );
		$content = @fread( $filehandle, filesize( $filename ) );
		fclose( $filehandle );

		$this->debug->unguard( $content );
		return $content;
	}


	/**
	 * Returns the content / filelist of a given directory
	 *
	 * @param string $path system path of the directoy to read
	 *
	 * @return array
	 */
	public function getDirectoryListing( $path )
	{
		$this->debug->guard( true );

		$dirContents = array( );
		$directoryhandle = @opendir( $path );

		if ( !$directoryhandle )
		{
			$this->debug->write( 'Could not open directory: "' . $path . '"', 'warning' );
			$this->messages->setMessage( 'Could not open directory: "' . $path . '"', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		while ( ( $dirEntry = readdir( $directoryhandle ) ) !== false )
		{
			$dirContents[ ] = $dirEntry;
		}

		closedir( $directoryhandle );

		$this->debug->unguard( $dirContents );
		return $dirContents;
	}


	/**
	 * stores an uploaded file in the given directory with the original name
	 *
	 * @param string $path system path of the directoy to store the files
	 * @param string $boolean if true, files in the trget dir can be overwritten
	 *
	 * @return boolean
	 */
	public function storeUploadedFile( $path, $overwrite = false )
	{
		$this->debug->guard( true );

		if ( count( $_FILES ) == 0 )
		{
			$this->debug->write( 'Could not find uploaded file', 'warning' );
			$this->messages->setMessage( 'Could not find uploaded file', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( $_FILES[ "file" ][ "error" ] > 0 )
		{
			$this->debug->write( 'File was uploaded with errors', 'warning' );
			$this->messages->setMessage( 'File was uploaded with errors', 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		if ( ( file_exists( $path . $_FILES[ "file" ][ "name" ] ) ) && ( $overwrite == false ) )
		{
			$this->debug->write( 'File ' . $_FILES[ "file" ][ "name" ] . ' already exists at ' . $path, 'warning' );
			$this->messages->setMessage( 'File ' . $_FILES[ "file" ][ "name" ] . ' already exists at ' . $path, 'warning' );
			$this->debug->unguard( false );
			return false;
		}

		$ret = move_uploaded_file( $_FILES[ "file" ][ "tmp_name" ], $path . $_FILES[ "file" ][ "name" ] );

		$this->debug->unguard( $ret );
		return $ret;
	}
}


?>