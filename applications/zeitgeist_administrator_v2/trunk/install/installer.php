<?php

define('ZEITGEIST_SOURCE_LINK', 'http://zeitgeist-framework.googlecode.com/files/zeitgeist-framework_20100304_1_0_2.zip');
define('ZEITGEIST_SQLIMPORT_FILE', './../_additional_material/zeitgeist_administrator.sql');
define('ZEITGEIST_APPCONFIG_FILE', './../configuration/application.configuration.php');
define('ZEITGEIST_APPINI_FILE', './../configuration/application.ini');

function createMessage($message, $messagetype)
{
	$ret = '<h3 class="user'.$messagetype.'">';
	$ret .= '<img src="./images/'.$messagetype.'.png" alt="" align="left" style="margin-right:5px;" />';
	$ret .= $message;
	$ret .= '</h3>';
	return $ret;		
}


function check_zeitgeist()
{
	if (!file_exists('./../zeitgeist/zeitgeist.php'))
	{
		$message = '<b>The Zeitgeist Framework was not found in the project directory.</b><br /><br />';
		$message .= 'If you plan on using the framework outside of the project directory (for example when you use one framework installation for multiple applications), please set the ZEITGEIST_ROOTDIRECTORY in the index.php.<br /><br />';
		$message .= 'If you do not have the Zeitgeist Framework yet, <a href="javascript:contentloader(\'message_zeitgeist\', \'download_zeitgeist\');">click here to download it into the project directory</a>.<br />';
		return createMessage($message, 'warning');
	}

	$message = createMessage('Zeitgeist Framework is available', 'message');
	return $message;
}


function check_basepath()
{
	$message = '<b>Checking application basepath.</b><br /><br />';
	$basepath_URL = $_POST['basepath_URL'];

	if (empty($basepath_URL))
	{
		if ( ($_SERVER["REQUEST_URI"] != '/zeitgeist_administrator_v2/install/installer.php?contentid=check_basepath') || ($_SERVER['SERVER_NAME'] != '127.0.0.1') )
		{
			$message .= 'The current application path seems to differ from the default installation path (http://127.0.0.1/zeitgeist_administrator_v2). You need to set your application path manually in the textbox above.<br /><br />';
			$message .= 'Most likely your application path is:<br />http://'.$_SERVER['SERVER_NAME'].substr($_SERVER["REQUEST_URI"], 0, -47).'<br />';
			return createMessage($message, 'warning');
		}

		$message .= 'The current application path seems to be the default installation path (http://127.0.0.1/zeitgeist_administrator_v2) so everything should be fine<br /><br />';
		$message .= 'If you encounter problems (like broken links, templates or images) try checking the basepath again in /configuration/application.ini';
		return createMessage($message, 'message');
	}
	else
	{
		$message .= 'Checking current configuration<br />';
		if (!$configuration = file_get_contents(ZEITGEIST_APPINI_FILE))
		{
			$message .= 'Could not open application.ini configuration file<br /><br />';
			$message .= 'Please make sure that the file '.ZEITGEIST_APPINI_FILE.' exisis.';
			return createMessage($message, 'warning');
		}
		$configuration = str_replace('http://127.0.0.1/zeitgeist_administrator_v2', $basepath_URL, $configuration);

		$message .= 'Writing new basepath to configuration<br />';
		if (!$configurationHandle = fopen(ZEITGEIST_APPINI_FILE, 'w'))
		{
			$message .= 'Could not open application.ini configuration file<br /><br />';
			$message .= 'Please make sure that the file '.ZEITGEIST_APPINI_FILE.' exisis.';
			return createMessage($message, 'warning');
		}		
		fwrite($configurationHandle, $configuration);
		fclose($configurationHandle);
		$message .= 'Done writing configuration<br /><br />';

		if ( ('http://'.$_SERVER['SERVER_NAME'].substr($_SERVER["REQUEST_URI"], 0, -47)) != $basepath_URL )
		{
			$message .= 'The basepath is set to the URL you specified. However it does not match the URL that the installer is called from, so you may encounter problems.<br /><br />';
			$message .= '<b>If you do encounter problems (like broken links, templates or images) try checking the basepath again in /configuration/application.ini</b>';
		}

		$message .= '<br />Basepath set successfully.<br />';
		$message .= 'If you do encounter problems (like broken links, templates or images) try checking the basepath again in /configuration/application.ini';
	}

	$message = createMessage($message, 'message');
	return $message;
}


function download_zeitgeist()
{
	$message = '<b>Downloading a matching version of the Zeitgeist Framework</b><br /><br />';
	if (!copy(ZEITGEIST_SOURCE_LINK, './../zeitgeist.zip'))
	{
		$message .= 'Could not download the framework.<br />';
		$message .= 'Please try to <a href="'.ZEITGEIST_SOURCE_LINK.'">download it manually</a>.<br /><br />';
		$message .= 'If the file is not available anymore go to <a href="http://zeitgeist-framework.googlecode.com/files/" target="_blank">http://zeitgeist-framework.googlecode.com/files/</a> and download the latest version.';
		return createMessage($message, 'warning');
	}

	$message .= 'Download ok..<br />';
	$message .= 'Unpacking the framework..<br />';
	$zip = new ZipArchive;
	if ($zip->open('./../zeitgeist.zip') !== TRUE)
	{
		$message .= 'Could not unzip the framework. The ZIP file may be corrupt.<br />';
		$message .= 'Please try to manually unzip the archive "zeitgeist.zip" in the root directory of the Zeitgeist Administrator.';
		return createMessage($message, 'warning');
	}

	if ($zip->extractTo('./../') !== TRUE)
	{
		$message .= 'Could not unzip the framework. The ZIP file may be corrupt.<br />';
		$message .= 'Please try to manually unzip the archive "zeitgeist.zip" in the root directory of the Zeitgeist Administrator.';
		return createMessage($message, 'warning');
	}

	$zip->close();
	$message .= 'Framework files are available..<br />';

	$message .= 'Deleting temp download file..<br />';
	unlink('./../zeitgeist.zip');

	$message .= 'Done.<br /><br >The Zeitgeist Framework is not installed in the root directory of the Zeitgeist Administrator.';
	$message = createMessage($message, 'message');
	return $message;
}


function database_connection()
{
	$database_server = $_POST['database_server'];
	$database_user = $_POST['database_user'];
	$database_password = $_POST['database_password'];
	$database_database = $_POST['database_database'];
	$database_createdatabase = $_POST['database_createdatabase'];
	$database_resetdatabase = $_POST['database_resetdatabase'];

	$message = '<b>Setting up the database connection</b><br /><br />';

	$message .= 'Trying to connect to the MySQL server..<br />';
	if (!$dblink = @mysql_connect($database_server, $database_user, $database_password))
	{
		$message .= 'Could not connect to the database server<br /><br />';
		$message .= 'MySQL message was: '.mysql_error().'<br /><br />';
		$message .= 'Make sure you entered the right credentials. Also: the server has to be a MySQL server.';
		return createMessage($message, 'warning');
	}

	if ($database_createdatabase == 'true')
	{
		$message .= 'Checking if the database already exists..<br />';
		if (mysql_select_db($database_database, $dblink))
		{
			$message .= 'The database already exists, there is no need to create it first.<br /><br />';
			$message .= 'If you use this database (by unchecking the box) it\'s contents will be deleted automatically.<br />';
			return createMessage($message, 'warning');
		}

		$message .= 'Trying to create the database..<br />';
		if (!mysql_query('CREATE DATABASE '.$database_database, $dblink))
		{
			$message .= 'Could not create the database<br /><br />';
			$message .= 'MySQL message was: '.mysql_error().'<br />';
			return createMessage($message, 'warning');
		}

		$message .= 'Trying to connect to the database..<br />';
		if (!mysql_select_db($database_database, $dblink))
		{
			$message .= 'Could not connect to the database<br /><br />';
			$message .= 'MySQL message was: '.mysql_error().'<br /><br />';
			$message .= 'Make sure you entered the right database name.';
			return createMessage($message, 'warning');
		}
	}
	else
	{
		if ($database_resetdatabase == 'true')
		{
			$message .= 'Trying to reset the database..<br />';
			if (!mysql_query('DROP DATABASE '.$database_database, $dblink))
			{
				$message .= 'Could not drop the database<br /><br />';
				$message .= 'MySQL message was: '.mysql_error().'<br />';
				return createMessage($message, 'warning');
			}

			if (!mysql_query('CREATE DATABASE '.$database_database, $dblink))
			{
				$message .= 'Could not create the database<br /><br />';
				$message .= 'MySQL message was: '.mysql_error().'<br />';
				return createMessage($message, 'warning');
			}
		}
		else
		{
			$message .= 'Checking if the database already exists..<br />';
			if (mysql_select_db($database_database, $dblink))
			{
				$message .= 'The database already exists.<br /><br />';
				$message .= 'If you want to reset an existing database, please choose the "Reset Database" option. Please note that all previous data will be lost.';
				return createMessage($message, 'warning');
			}
		}

		$message .= 'Trying to connect to the database..<br />';
		if (!mysql_select_db($database_database, $dblink))
		{
			$message .= 'Could not connect to the database<br /><br />';
			$message .= 'MySQL message was: '.mysql_error().'<br /><br />';
			$message .= 'Make sure you entered the right database name.';
			return createMessage($message, 'warning');
		}
	}

	$message .= 'Reading initial SQL data..<br />';
	if (!$importHandle = fopen(ZEITGEIST_SQLIMPORT_FILE, 'r'))
	{
		$message .= 'Could not open SQL initialization file<br /><br />';
		$message .= 'Please make sure that the file '.ZEITGEIST_SQLIMPORT_FILE.' exisis.';
		return createMessage($message, 'warning');
	}

	$tempDataString = '';
	while ($sqlImportLine = fgets($importHandle))
	{
		if ( (substr($sqlImportLine, 0, 2) != '--') && (substr($sqlImportLine, 0, 2) != '/*') && (strlen($sqlImportLine) > 1) )
		{
			$endDataString = trim(substr($sqlImportLine, -2, 1));
			$tempDataString .= trim(substr($sqlImportLine, 0, -1));
			if ($endDataString == ';')
			{
				if (!mysql_query($tempDataString, $dblink))
				{
					$message .= 'Could not import the initial data to the database<br /><br />';
					$message .= 'MySQL message was: '.mysql_error().'<br />';
					return createMessage($message, 'warning');
				}
				
				$tempDataString = '';
			}
		}
	}

	fclose($importHandle);
	$message .= 'Database connection is ok and data has been imported<br />';

	$message .= 'Writing database details to configuration file..<br />';
	if (!$configurationHandle = fopen(ZEITGEIST_APPCONFIG_FILE, 'w'))
	{
		$message .= 'Could not open application configuration file<br /><br />';
		$message .= 'Please make sure that the file '.ZEITGEIST_APPCONFIG_FILE.' exisis.';
		return createMessage($message, 'warning');
	}
	
	fwrite($configurationHandle, "<?php\n\n");
	fwrite($configurationHandle, "\tdefine(ZG_DB_DBSERVER, '".$database_server."');\n");
	fwrite($configurationHandle, "\tdefine(ZG_DB_USERNAME, '".$database_user."');\n");
	fwrite($configurationHandle, "\tdefine(ZG_DB_USERPASS, '".$database_password."');\n");
	fwrite($configurationHandle, "\tdefine(ZG_DB_DATABASE, '".$database_database."');\n");
	fwrite($configurationHandle, "\tdefine(ZG_DB_CONFIGURATIONCACHE, 'configurationcache');\n");
	fwrite($configurationHandle, "\n?>");
	fclose($configurationHandle);
	$message .= 'Done writing configuration<br /><br />';

	$message .= 'The database is all set up. The connection details have been saved to <br />./configuration/application.configuration.php and will be used from now on.<br />';

	$message = createMessage($message, 'message');
	return $message;
}



// check that content id is given
if (!$contentid = $_GET['contentid']) die();

// check if a function with the content id exists
if (!function_exists($contentid)) die();

// call the function with the given id 
$content = call_user_func($contentid);

// put out content
echo $content;

?>