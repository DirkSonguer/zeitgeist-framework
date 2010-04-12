<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgGamedata test case.
 */
class zgGamedataTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var zgGamedata
	 */
	private $zgGamedata;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->zgGamedata = new zgGamedata();
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->zgGamedata = null;
		parent::tearDown();
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		$this->database = new zgDatabase();
		$ret = $this->database->connect();
	}


	/**
	 * Tests zgGamedata->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated zgGamedataTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );
		
		$this->zgGamedata->__construct(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithoutEntityName()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_entities' );
		
		$entityid = $this->zgGamedata->createEntity();
		$this->assertTrue( ! empty( $entityid ) );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['entity_id'], $entityid );
		
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithEntityName()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$entityname = uniqid();
		
		$entityid = $this->zgGamedata->createEntity( $entityname );
		$this->assertTrue( ! empty( $entityid ) );
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 1 );
		
		$ret = $this->database->fetchArray( $res );
		$this->assertEquals( $ret ['entity_id'], $entityid );
		$this->assertEquals( $ret ['entity_name'], $entityname );
		
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown();
	}



	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithAssemblage()
	{
		$this->setUp();
		$gamedata = new zgGamedata();
		$gamesetup = new zgGamesetup();
		$testfunctions = new testFunctions();

		$testfunctions->createZeitgeistTable('game_entities');
		$testfunctions->createZeitgeistTable('game_assemblage_components');
		$testfunctions->createZeitgeistTable('game_assemblages');
		$testfunctions->createZeitgeistTable('game_components');
		$testfunctions->createZeitgeistTable('game_entity_components');

		$assemblagename = uniqid();
		$assemblagedescription = uniqid();
		$assemblageid = $gamesetup->createAssemblage($assemblagename, $assemblagedescription);

		$componentname = uniqid();
		$componentdescription = uniqid();
		$componentid = $gamesetup->createComponent($componentname, $componentdescription);

		$ret = $gamesetup->addComponentToAssemblage($componentid, $assemblageid);

		$entityname = uniqid();
		$entityid = $this->zgGamedata->createEntity($entityname, $assemblageid);
		$this->assertTrue( ! empty( $entityid ) );
		
		// check database
		$res = $this->database->query("SELECT * FROM game_entities");
		$ret = $this->database->numRows($res);
		$this->assertEquals($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEquals($ret['entity_id'], $entityid);
		$this->assertEquals($ret['entity_name'], $entityname);

		$res = $this->database->query("SELECT * FROM game_entity_components");
		$ret = $this->database->numRows($res);
		$this->assertEquals($ret, 1);

		$ret = $this->database->fetchArray($res);
		$this->assertEquals($ret['entitycomponent_entity'], $entityid);
		$this->assertEquals($ret['entitycomponent_component'], $componentid);

		$testfunctions->dropZeitgeistTable('game_component_'.$componentid);
		$testfunctions->dropZeitgeistTable('game_entities');
		$testfunctions->dropZeitgeistTable('game_assemblage_components');
		$testfunctions->dropZeitgeistTable('game_assemblages');
		$testfunctions->dropZeitgeistTable('game_components');
		$testfunctions->dropZeitgeistTable('game_entity_components');
		$this->tearDown();
	}


	/**
	 * Tests zgGamedata->deleteEntity()
	 */
	public function testDeleteEntity_Success()
	{
		$this->setUp();
		$testfunctions = new testFunctions();
		
		$testfunctions->createZeitgeistTable( 'game_entities' );
		
		$entityid = $this->zgGamedata->createEntity();
		$ret = $this->zgGamedata->deleteEntity($entityid);
		$this->assertTrue($ret);
		
		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEquals( $ret, 0 );
		
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown();
	}


	/**
	 * Tests zgGamedata->addComponentToEntity()
	 */
	public function testAddComponentToEntity()
	{
		// TODO Auto-generated zgGamedataTest->testAddComponentToEntity()
		$this->markTestIncomplete( "addComponentToEntity test not implemented" );
		
		$this->zgGamedata->addComponentToEntity(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->removeComponentFromEntity()
	 */
	public function testRemoveComponentFromEntity()
	{
		// TODO Auto-generated zgGamedataTest->testRemoveComponentFromEntity()
		$this->markTestIncomplete( "removeComponentFromEntity test not implemented" );
		
		$this->zgGamedata->removeComponentFromEntity(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->addAssemblageToEntity()
	 */
	public function testAddAssemblageToEntity()
	{
		// TODO Auto-generated zgGamedataTest->testAddAssemblageToEntity()
		$this->markTestIncomplete( "addAssemblageToEntity test not implemented" );
		
		$this->zgGamedata->addAssemblageToEntity(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->getComponentData()
	 */
	public function testGetComponentData()
	{
		// TODO Auto-generated zgGamedataTest->testGetComponentData()
		$this->markTestIncomplete( "getComponentData test not implemented" );
		
		$this->zgGamedata->getComponentData(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->getComponentDataForEntity()
	 */
	public function testGetComponentDataForEntity()
	{
		// TODO Auto-generated zgGamedataTest->testGetComponentDataForEntity()
		$this->markTestIncomplete( "getComponentDataForEntity test not implemented" );
		
		$this->zgGamedata->getComponentDataForEntity(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->setComponentData()
	 */
	public function testSetComponentData()
	{
		// TODO Auto-generated zgGamedataTest->testSetComponentData()
		$this->markTestIncomplete( "setComponentData test not implemented" );
		
		$this->zgGamedata->setComponentData(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->getComponentListForEntity()
	 */
	public function testGetComponentListForEntity()
	{
		// TODO Auto-generated zgGamedataTest->testGetComponentListForEntity()
		$this->markTestIncomplete( "getComponentListForEntity test not implemented" );
		
		$this->zgGamedata->getComponentListForEntity(/* parameters */);
	
	}


	/**
	 * Tests zgGamedata->getEntityForComponent()
	 */
	public function testGetEntityForComponent()
	{
		// TODO Auto-generated zgGamedataTest->testGetEntityForComponent()
		$this->markTestIncomplete( "getEntityForComponent test not implemented" );
		
		$this->zgGamedata->getEntityForComponent(/* parameters */);
	
	}

}

