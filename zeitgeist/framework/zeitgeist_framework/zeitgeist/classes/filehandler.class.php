<?php
/**
 * Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * Filehandler class
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package ZEITGEIST
 * @subpackage ZEITGEIST FILEHANDLER
 */

defined('ZEITGEIST_ACTIVE') or die();

class zgFilehandler
{
	private static $instance = false;

	protected $debug;
	protected $messages;
	protected $configuration;

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
	}


	/**
	 * Returns the content of the given file
	 *
	 * @param string $filename name of the file to get content from
	 *
	 * @return binary
	 */
	public function getFileContent($filename)
	{
		$this->debug->guard(true);

		$filehandle = fopen ($filename, "r");
		$content = fread ($filehandle, filesize($filename));
		fclose ($filehandle);

		$this->debug->unguard($content);
		return $content;
	}


	/**
	 * Returns the content / filelist of a given directory
	 *
	 * @param string $path system path of the directoy to read
	 *
	 * @return array
	 */
	public function getDirectoryListing($path)
	{
		$this->debug->guard(true);
		
		$dirContents = array();
		$directoryhandle = opendir($path);

		if (!$directoryhandle)
		{
			$this->debug->write('Could not open directory: "' . $path . '"', 'warning');
			$this->messages->setMessage('Could not open directory: "' . $path . '"', 'warning');
			$this->debug->unguard(false);
			return false;
		}
				
		while($dirEntry = readdir($directoryhandle))
		{
			$dirContents[] = $dirEntry;
		}
		
		closedir($directoryhandle);

		$this->debug->unguard($dirContents);
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
	public function storeUploadedFile($path, $overwrite=false)
	{
		$this->debug->guard(true);

		if (count($_FILES) == 0)
		{
			$this->debug->write('Could not find uploaded file', 'warning');
			$this->messages->setMessage('Could not find uploaded file', 'warning');
			$this->debug->unguard(false);
			return false;
	    }
		
		if ($_FILES["file"]["error"] > 0)
		{
			$this->debug->write('File was uploaded with errors', 'warning');
			$this->messages->setMessage('File was uploaded with errors', 'warning');
			$this->debug->unguard(false);
			return false;
	    }

		if ( (file_exists($path . $_FILES["file"]["name"])) && ($overwrite == false) )
		{
			$this->debug->write('File ' . $_FILES["file"]["name"] . ' already exists at ' . $path, 'warning');
			$this->messages->setMessage('File ' . $_FILES["file"]["name"] . ' already exists at ' . $path, 'warning');
			$this->debug->unguard(false);
			return false;
		}

		$ret = move_uploaded_file($_FILES["file"]["tmp_name"], $path . $_FILES["file"]["name"]);

		$this->debug->unguard($ret);
		return $ret;
	}

}
?>