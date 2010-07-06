<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgGamedata test case.
 */
class zgGamedataTest extends UnitTestCase
{
	/**
	 * @var zgGamedata
	 */
	private $zgGamedata;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgGamedata = new zgGamedata( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgGamedata = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		$this->database = new zgDatabase( );
		$this->database->connect( );
	}


	/**
	 * Tests zgGamedata->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgGamedataTest->test__construct()
		$this->zgGamedata->__construct( /* parameters */ );
	}


	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithoutEntityName( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_entities' );

		// create entity
		$entityid = $this->zgGamedata->createEntity( );
		$this->assertTrue( !empty( $entityid ) );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['entity_id'], $entityid );

		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithEntityName( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_entities' );
		$entityname = uniqid( );

		// create entity
		$entityid = $this->zgGamedata->createEntity( $entityname );
		$this->assertTrue( !empty( $entityid ) );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['entity_id'], $entityid );
		$this->assertEqual( $ret ['entity_name'], $entityname );

		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->createEntity()
	 */
	public function testCreateEntity_WithAssemblage( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_assemblage_components' );
		$testfunctions->createZeitgeistTable( 'game_assemblages' );
		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create assemblage
		$assemblagename = uniqid( );
		$assemblagedescription = uniqid( );
		$assemblageid = $gamesetup->createAssemblage( $assemblagename, $assemblagedescription );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// add component to assemblage
		$ret = $gamesetup->addComponentToAssemblage( $componentid, $assemblageid );

		// create entity
		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname, $assemblageid );
		$this->assertTrue( !empty( $entityid ) );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['entity_id'], $entityid );
		$this->assertEqual( $ret ['entity_name'], $entityname );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entity_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['entitycomponent_entity'], $entityid );
		$this->assertEqual( $ret ['entitycomponent_component'], $componentid );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_assemblage_components' );
		$testfunctions->dropZeitgeistTable( 'game_assemblages' );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->deleteEntity()
	 */
	public function testDeleteEntity_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_entities' );

		// create and delete entity
		$entityid = $this->zgGamedata->createEntity( );
		$ret = $this->zgGamedata->deleteEntity( $entityid );
		$this->assertTrue( $ret );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entities" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->addComponentToEntity()
	 */
	public function testAddComponentToEntity_Success( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create entity
		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// add component to entity
		$ret = $this->zgGamedata->addComponentToEntity( $componentid, $entityid );
		$this->assertEqual( $ret, 1 );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entity_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check component
		$res = $this->database->query( "SELECT * FROM game_component_" . $componentid );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->removeComponentFromEntity()
	 */
	public function testRemoveComponentFromEntity_Success( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create entity
		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// add and remove component to entity
		$this->zgGamedata->addComponentToEntity( $componentid, $entityid );
		$ret = $this->zgGamedata->removeComponentFromEntity( $componentid, $entityid );
		$this->assertTrue( $ret );

		// check database
		$res = $this->database->query( "SELECT * FROM game_entity_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		// check component
		$res = $this->database->query( "SELECT * FROM game_component_" . $componentid );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->addAssemblageToEntity()
	 */
	public function testAddAssemblageToEntity( )
	{
		// TODO Auto-generated zgGamedataTest->testAddAssemblageToEntity()
		//		$this->zgGamedata->addAssemblageToEntity( /* parameters */ );
	}


	/**
	 * Tests zgGamedata->getComponentData()
	 */
	public function testGetComponentData_Filtered( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// create entities and add component to it
		for ( $i = 0; $i < 5; $i++ )
		{
			$entityname = uniqid( );
			$entityid [$i] = $this->zgGamedata->createEntity( $entityname );
			$componentdataid [$i] = $this->zgGamedata->addComponentToEntity( $componentid, $entityid [$i] );
		}

		// get component data
		$filter = array('id' => $componentdataid [1]);
		$componentlist = $this->zgGamedata->getComponentData( $componentid, $filter );
		$this->assertEqual( count( $componentlist ), 1 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->getComponentDataForEntity()
	 */
	public function testGetComponentDataForEntity( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create entity
		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// add the component to the entity and alter tables
		$componentdataid = $this->zgGamedata->addComponentToEntity( $componentid, $entityid );
		$res = $this->database->query( "ALTER TABLE `game_component_" . $componentid . "` ADD `testdata1` VARCHAR( 16 ) NOT NULL " );
		$res = $this->database->query( "ALTER TABLE `game_component_" . $componentid . "` ADD `testdata2` VARCHAR( 16 ) NOT NULL " );
		$this->assertTrue( $res );

		$testdata = array();
		$testdata1 = uniqid( );
		$testdata2 = uniqid( );
		$testdata ['testdata1'] = $testdata1;
		$testdata ['testdata2'] = $testdata2;

		$ret = $this->zgGamedata->setComponentData( $componentid, $entityid, $testdata );

		$ret = $this->zgGamedata->getComponentDataForEntity( $componentid, $entityid );
		$this->assertEqual( $ret ['id'], $componentdataid );
		$this->assertEqual( $ret ['testdata1'], $testdata1 );
		$this->assertEqual( $ret ['testdata2'], $testdata2 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->setComponentData()
	 */
	public function testSetComponentData_Success( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		// add component to entity and alter tables
		$componentdataid = $this->zgGamedata->addComponentToEntity( $componentid, $entityid );
		$res = $this->database->query( "ALTER TABLE `game_component_" . $componentid . "` ADD `testdata1` VARCHAR( 16 ) NOT NULL " );
		$res = $this->database->query( "ALTER TABLE `game_component_" . $componentid . "` ADD `testdata2` VARCHAR( 16 ) NOT NULL " );
		$this->assertTrue( $res );

		$testdata = array();
		$testdata1 = uniqid( );
		$testdata2 = uniqid( );
		$testdata ['testdata1'] = $testdata1;
		$testdata ['testdata2'] = $testdata2;

		// set component data to created component
		$ret = $this->zgGamedata->setComponentData( $componentid, $entityid, $testdata );
		$this->assertTrue( $res );

		$res = $this->database->query( "SELECT * FROM game_component_" . $componentid );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['id'], $componentdataid );
		$this->assertEqual( $ret ['testdata1'], $testdata1 );
		$this->assertEqual( $ret ['testdata2'], $testdata2 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->setComponentData()
	 */
	public function testSetComponentData_NoFilter( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $gamesetup->createComponent( $componentname, $componentdescription );

		for ( $i = 0; $i < 5; $i++ )
		{
			$entityname = uniqid( );
			$entityid [$i] = $this->zgGamedata->createEntity( $entityname );
			$componentdataid [$i] = $this->zgGamedata->addComponentToEntity( $componentid, $entityid [$i] );
		}

		$componentlist = $this->zgGamedata->getComponentData( $componentid );
		$this->assertEqual( count( $componentlist ), 5 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->getComponentListForEntity()
	 */
	public function testGetComponentListForEntity( )
	{
		$this->setUp( );
		$gamesetup = new zgGamesetup( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_entities' );
		$testfunctions->createZeitgeistTable( 'game_entity_components' );

		// create entity
		$entityname = uniqid( );
		$entityid = $this->zgGamedata->createEntity( $entityname );

		// create and add component to entity
		$componentname1 = uniqid( );
		$componentdescription1 = uniqid( );
		$componentid1 = $gamesetup->createComponent( $componentname1, $componentdescription1 );
		$componentdataid1 = $this->zgGamedata->addComponentToEntity( $componentid1, $entityid );
		$this->assertNotNull( $componentdataid1 );

		// create and add component to entity
		$componentname2 = uniqid( );
		$componentdescription2 = uniqid( );
		$componentid2 = $gamesetup->createComponent( $componentname2, $componentdescription2 );
		$componentdataid2 = $this->zgGamedata->addComponentToEntity( $componentid2, $entityid );
		$this->assertNotNull( $componentdataid2 );

		$componentlist = $this->zgGamedata->getComponentListForEntity( $entityid );
		$this->assertTrue( is_array( $componentlist ) );
		$this->assertEqual( $componentlist [$componentid1], 1 );
		$this->assertEqual( $componentlist [$componentid2], 2 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid1 );
		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid2 );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_entities' );
		$testfunctions->dropZeitgeistTable( 'game_entity_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamedata->getEntityForComponent()
	 */
	public function testGetEntityForComponent( )
	{
		// TODO Auto-generated zgGamedataTest->testGetEntityForComponent()
		//		$this->zgGamedata->getEntityForComponent( /* parameters */ );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgGamedataTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgGamedataTest( ) );

	$test->run( new HtmlReporter( ) );
}