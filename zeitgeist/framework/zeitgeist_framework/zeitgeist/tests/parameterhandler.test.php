<?php

class testParameterhandler extends UnitTestCase
{

	function test_init()
	{
		$parameterhandler = new zgParameterhandler();
		$this->assertNotNull($parameterhandler);
		unset($parameterhandler);
    }
	
}

?>
