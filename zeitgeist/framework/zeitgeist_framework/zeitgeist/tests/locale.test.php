<?php

class testLocale extends UnitTestCase
{

	function test_init()
	{
		$locale = zgLocale::init();
		$this->assertNotNull($locale);
		unset($locale);		
    }
	
	function test_loadLocale()
	{
		$locale = zgLocale::init();
		$ret = $locale->loadLocale('testlocale', 'false');
		$this->assertFalse($ret);
		unset($ret);

		$ret = $locale->loadLocale('testlocale', 'testdata/testlocale.ini');
		$this->assertTrue($ret);
		unset($ret);
	}

	function test_setLocale()
	{
		$locale = zgLocale::init();
		$locale->loadLocale('testlocale', 'testdata/testlocale.ini', true);

		$ret = $locale->setLocale('false');
		$this->assertFalse($ret);
		unset($ret);

		$ret = $locale->setLocale('testlocale');
		$this->assertTrue($ret);
		unset($ret);
	}

	function test_write()
	{
		$locale = zgLocale::init();
		$ret = $locale->write('notext');
		$this->assertEqual($ret, 'notext');
		unset($ret);

		$locale->loadLocale('testlocale', 'testdata/testlocale.ini', true);
		$ret = $locale->setLocale('testlocale');
		$ret = $locale->write('true');
		$this->assertEqual($ret, 'wahr');
		unset($ret);
		$ret = $locale->write('false');
		$this->assertEqual($ret, 'falsch');
		unset($ret);
		$ret = $locale->write('this is a test');
		$this->assertEqual($ret, 'das ist ein test');
		unset($ret);
	}
}

?>
