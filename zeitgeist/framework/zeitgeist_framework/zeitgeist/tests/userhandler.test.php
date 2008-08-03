<?php

class testUserhandler extends UnitTestCase
{
	function test_init()
	{
		$userhandler = zgUserhandler::init();
		$this->assertNotNull($userhandler);
		unset($userhandler);
    }
	
}

?>
