<?php

if (!defined('MULTITEST')) include(dirname(__FILE__).'/../test_configuration.php');

class testLocalisation extends UnitTestCase
{

	function test_init()
	{
		$locale = new zgLocalisation();
		$this->assertNotNull($locale);
		unset($locale);		
    }


	// Try to load invalid locale
	function test_loadLocale_invalidlocale()
	{
		$locale = new zgLocalisation();
		
		$ret = $locale->loadLocale('testlocale', 'false');
		$this->assertFalse($ret);

		unset($ret);
		unset($locale);		
	}
	
	
	// Load valid locale
	function test_loadLocale_success()
	{
		$locale = new zgLocalisation();
		
		$ret = $locale->loadLocale('testlocale', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testlocale.ini');
		$this->assertTrue($ret);
		
		unset($ret);
		unset($locale);		
	}


	// Try to set invalid locale
	function test_setLocale_invalid()
	{
		$locale = new zgLocalisation();
		
		$locale->loadLocale('testlocale', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testlocale.ini');
		$ret = $locale->setLocale('false');
		$this->assertFalse($ret);
		
		unset($ret);
		unset($locale);		
	}


	// Set valid locale
	function test_setLocale_success()
	{
		$locale = new zgLocalisation();
		
		$locale->loadLocale('testlocale', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testlocale.ini');
		$ret = $locale->setLocale('testlocale');
		$this->assertTrue($ret);
		
		unset($ret);
		unset($locale);		
	}
	

	// Try to write text without setting the locale
	function test_write_nolocale()
	{
		$locale = new zgLocalisation();
		
		$locale->loadLocale('testlocale', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testlocale.ini');
		$ret = $locale->write('hello world');
		$this->assertEqual($ret, 'hello world');
		
		unset($ret);
		unset($locale);		
	}


	// Write and translate text
	function test_write_success()
	{
		$locale = new zgLocalisation();
		
		$locale->loadLocale('testlocale', ZEITGEIST_ROOTDIRECTORY.'tests/testdata/testlocale.ini');
		$locale->setLocale('testlocale');

		$ret = $locale->write('this is a test');
		$this->assertEqual($ret, 'das ist ein test');
		
		unset($ret);
		unset($locale);		
	}

}

?>
