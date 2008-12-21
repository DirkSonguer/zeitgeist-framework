<?php
/**
 * Lineracer
 *
 * Based on the Zeitgeist Application Framework
 * http://www.zeitgeist-framework.com
 *
 * @author Dirk Songür <songuer@zeitgeist-framework.com>
 *
 * @copyright http://www.zeitgeist-framework.com
 * @license http://www.zeitgeist-framework.com/zeitgeist/license.txt
 *
 * @package LINERACER
 * @subpackage LINERACER TOOLS
 */

	define('LINERACER_ACTIVE', true);
	define('DEBUGMODE', true);

	if (!defined('ZEITGEIST_ROOTDIRECTORY')) define('ZEITGEIST_ROOTDIRECTORY', '../zeitgeist/');
	if (!defined('APPLICATION_ROOTDIRECTORY')) define('APPLICATION_ROOTDIRECTORY', './tools');
	include('../zeitgeist/zeitgeist.php');

	require_once('classes/imagetocircuit.class.php');

	include('../configuration/lineracer.config.php');

	$debug = zgDebug::init();
	$message = zgMessages::init();
	$configuration = zgConfiguration::init();
	$error = zgErrorhandler::init();
	$user = zgUserhandler::init();
	$eventhandler = new zgEventhandler();

	// load configuration
	$configuration->loadConfiguration('lineracer', '../configuration/lineracer.ini');

	// start importing
	$imagetocircuit = new imagetocircuit();
	$ret = $imagetocircuit->import('Teststrecke', '../data/circuits/circuit1_negative.png', 1);

	$database = new zgDatabase();
	$database->connect();

	$sql = "SELECT * FROM circuits c LEFT JOIN circuit_data cd ON c.circuit_id = cd.circuitdata_circuit WHERE c.circuit_name='Teststrecke'";
	$res = $database->query($sql);
	$row = $database->fetchArray($res);
	$circuitData = $row['circuitdata_data'];
	
	echo "<p style='font-size:5px;'>";
	for($j=1;$j<620;$j++)
	{
			for($i=1;$i<800;$i++)
			{
				echo substr($circuitData, $j*800+$i, 1);
			}
			echo "<br />";
	}
	echo "</p>";

	$debug->loadStylesheet('debug.css');
	$debug->showInnerLoops = true;
	$debug->showMiscInformation();
	$debug->showDebugMessages();
	$debug->showQueryMessages();
	$debug->showGuardMessages();

?>
