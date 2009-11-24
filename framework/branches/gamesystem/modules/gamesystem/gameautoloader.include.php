<?php

	function zgGameautoloader($class)
	{
		$debug = zgDebug::init();
		$message = zgMessages::init();

		$debug->guard();

		if (file_exists(APPLICATION_ROOTDIRECTORY . $class . '.action.php'))
		{
			require_once(APPLICATION_ROOTDIRECTORY . $class . '.action.php');
			$debug->unguard('Class '.APPLICATION_ROOTDIRECTORY . 'gamecards/' . $class . '.gamecard.php loaded');
			return;
		}

		$expected = APPLICATION_ROOTDIRECTORY . $class . '.action.php';
		$debug->write('Error autoloading class: Class ' . $class . ' not found in '.$expected, 'warning');
		$message->setMessage('Error autoloading class: Class ' . $class . ' not found in '.$expected, 'warning');

		$debug->unguard(false);
	}

?>
