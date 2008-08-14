<?php

class testUserrights extends UnitTestCase
{
	function test_init()
	{
		$userrights = new zgUserrights();
		$this->assertNotNull($userrights);
		unset($userrights);
    }

	
}

?>
