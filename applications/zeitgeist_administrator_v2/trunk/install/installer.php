<?php

define('ZEITGEIST_SOURCE_LINK', 'http://zeitgeist-framework.googlecode.com/files/zeitgeist-framework_20100213_1_0_1.zip');
define('ZEITGEIST_SQLIMPORT_FILE', './../_additional_material/zeitgeist_administrator.sql');

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


function download_zeitgeist()
{
	$message = 'Downloading a matching version of the Zeitgeist Framework..<br />';
	if (!copy(ZEITGEIST_SOURCE_LINK, './../zeitgeist.zip'))
	{
		$message .= 'Could not download the framework.<br />';
		$message .= 'Please try to <a href="'.ZEITGEIST_SOURCE_LINK.'">download it manually</a>.<br /><br />';
		$message .= 'If the file is not available anymore go to <a href="http://zeitgeist-framework.googlecode.com/files/" target="_blank">http://zeitgeist-framework.googlecode.com/files/</a> and download the latest version.';
		return createMessage($message, 'warning');
	}

	$message .= 'Unpacking the framework..<br />';
	$zip = new ZipArchive;
	if ($zip->open('./../zeitgeist.zip') !== TRUE)
	{
		$message .= 'Could not unzip the framework. The ZIP file may be corrupt.<br />';
		$message .= 'Please try to manually unzip the archive "zeitgeist.zip" in the root directory of the Zeitgeist Administrator.';
		return createMessage($message, 'warning');
	}

	if ($zip->extractTo('./../zeitgeist/') !== TRUE)
	{
		$message .= 'Could not unzip the framework. The ZIP file may be corrupt.<br />';
		$message .= 'Please try to manually unzip the archive "zeitgeist.zip" in the root directory of the Zeitgeist Administrator.';
		return createMessage($message, 'warning');
	}

	$zip->close();

	$message .= '<br />Done. The Zeitgeist Framework is not installed in the root directory of the Zeitgeist Administrator.';
	$message = createMessage($message, 'message');
	return $message;
}


function database_connection()
{
	$database_server = $_POST['database_server'];
	$database_user = $_POST['database_user'];
	$database_userpassword = $_POST['database_userpassword'];
	$database_database = $_POST['database_database'];
	$database_createdatabase = $_POST['database_createdatabase'];

	$message = 'Trying to connect to the MySQL server..<br />';
	if (!$dblink = @mysql_connect($database_server, $database_user, $database_userpassword))
	{
		$message .= 'Could not connect to the database server<br /><br />';
		$message .= 'MySQL message was: '.mysql_error().'<br /><br />';
		$message .= 'Make sure you entered the right credentials. Also: the server has to be a MySQL server.';
		return createMessage($message, 'warning');
	}

	if ($database_createdatabase == 'true')
	{
		$message .= 'Trying to create the database..<br />';
		if (!mysql_query('CREATE DATABASE '.$database_database, $dblink))
		{
			$message .= 'Could not create the database<br /><br />';
			$message .= 'MySQL message was: '.mysql_error().'<br />';
			return createMessage($message, 'warning');
		}
	} else {
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
		$message .= 'Please make sure that the file _additional_material/zeitgeist_administrator.sql exisis.';
		return createMessage($message, 'warning');
	}
	$importData = fread($importHandle, filesize(ZEITGEIST_SQLIMPORT_FILE));
	fclose($importHandle);

	$message .= 'Importing initial SQL data to database..<br />';
	if (!mysql_query($importData, $dblink))
	{
		$message .= 'Could not import the initial data to the database<br /><br />';
		$message .= 'MySQL message was: '.mysql_error().'<br />';
		return createMessage($message, 'warning');
	}

	$message .= 'Database connection is available<br /><br />';
	$message .= 'The connection details have ben saved to ./configuration/application.configuration.php and will be used from now on.<br />';

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