<?php

defined('LINERACER_ACTIVE') or die();

// TODO: Zusammenfassen der ganzen Dateiaufrufe

	function lrEventoverride($class)
	{
		$debug = zgDebug::init();
		$message = zgMessages::init();

		$debug->guard();

		if (file_exists(APPLICATION_ROOTDIRECTORY . 'gamecards/' . $class . '.gamecard.php'))
		{
			require_once(APPLICATION_ROOTDIRECTORY . 'gamecards/' . $class . '.gamecard.php');
			$debug->unguard('Class '.APPLICATION_ROOTDIRECTORY . 'gamecards/' . $class . '.gamecard.php loaded');
			return;
		}

		$expected = APPLICATION_ROOTDIRECTORY . 'gamecards/' . $class . '.gamecard.php';
		$debug->write('Error autoloading class: Class ' . $class . ' not found in '.$expected, 'error');
		$message->setMessage('Error autoloading class: Class ' . $class . ' not found in '.$expected, 'error');

		$debug->unguard(false);
	}


?>
