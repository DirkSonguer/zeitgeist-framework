<?php

if ( !defined( 'MULTITEST' ) )
{
	include( dirname( __FILE__ ) . '/../_configuration.php' );
}

/**
 * zgGamesetup test case.
 */
class zgGamesetupTest extends UnitTestCase
{
	/**
	 * @var zgGamesetup
	 */
	private $zgGamesetup;
	private $database;


	/**
	 * Prepares the environment before running a test.
	 */
	public function setUp( )
	{
		parent::setUp( );
		$this->zgGamesetup = new zgGamesetup( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	public function tearDown( )
	{
		$this->zgGamesetup = null;
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
	 * Tests zgGamesetup->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgGamesetupTest->test__construct()
		$this->zgGamesetup->__construct( /* parameters */ );
	}


	/**
	 * Tests zgGamesetup->createComponent()
	 */
	public function testCreateComponent_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );

		$componentname = uniqid( );
		$componentdescription = uniqid( );

		// create new component
		$componentid = $this->zgGamesetup->createComponent( $componentname, $componentdescription );
		$this->assertTrue( ( $componentid > 0 ) );

		// check database
		$res = $this->database->query( "SELECT * FROM game_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['component_id'], $componentid );
		$this->assertEqual( $ret ['component_name'], $componentname );
		$this->assertEqual( $ret ['component_description'], $componentdescription );

		// check database
		$res = $this->database->query( "SELECT * FROM game_component_" . $componentid );
		$this->assertTrue( is_resource( $res ) );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamesetup->deleteComponent()
	 */
	public function testDeleteComponent_ComponentDoesNotExist( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );

		$componentid = uniqid( );
		$ret = $this->zgGamesetup->deleteComponent( $componentid );
		$this->assertFalse( $ret );

		$testfunctions->dropZeitgeistTable( 'game_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamesetup->deleteComponent()
	 */
	public function testDeleteComponent_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_components' );

		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $this->zgGamesetup->createComponent( $componentname, $componentdescription );

		$ret = $this->zgGamesetup->deleteComponent( $componentid );
		$this->assertTrue( $ret );

		// check database
		$res = $this->database->query( "SELECT * FROM game_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$res = $this->database->query( "SELECT * FROM game_component_" . $componentid );
		$this->assertFalse( is_resource( $res ) );

		$testfunctions->dropZeitgeistTable( 'game_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamesetup->createAssemblage()
	 */
	public function testCreateAssemblage_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_assemblages' );

		$assemblagename = uniqid( );
		$assemblagedescription = uniqid( );

		$assemblageid = $this->zgGamesetup->createAssemblage( $assemblagename, $assemblagedescription );
		$this->assertTrue( ( $assemblageid > 0 ) );

		// check database
		$res = $this->database->query( "SELECT * FROM game_assemblages" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['assemblage_id'], $assemblageid );
		$this->assertEqual( $ret ['assemblage_name'], $assemblagename );
		$this->assertEqual( $ret ['assemblage_description'], $assemblagedescription );

		$testfunctions->dropZeitgeistTable( 'game_assemblages' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamesetup->addComponentToAssemblage()
	 */
	public function testAddComponentToAssemblage_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_assemblages' );
		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_assemblage_components' );

		// create assemblage
		$assemblagename = uniqid( );
		$assemblagedescription = uniqid( );
		$assemblageid = $this->zgGamesetup->createAssemblage( $assemblagename, $assemblagedescription );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $this->zgGamesetup->createComponent( $componentname, $componentdescription );

		// test adding the component to the assemblage
		$ret = $this->zgGamesetup->addComponentToAssemblage( $componentid, $assemblageid );
		$this->assertTrue( $ret );

		// check database
		$res = $this->database->query( "SELECT * FROM game_assemblage_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 1 );

		// check database content
		$ret = $this->database->fetchArray( $res );
		$this->assertEqual( $ret ['assemblagecomponent_assemblage'], $assemblageid );
		$this->assertEqual( $ret ['assemblagecomponent_component'], $componentid );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_assemblages' );
		$testfunctions->dropZeitgeistTable( 'game_assemblage_components' );
		$this->tearDown( );
	}


	/**
	 * Tests zgGamesetup->removeComponentFromAssemblage()
	 */
	public function testRemoveComponentFromAssemblage_Success( )
	{
		$this->setUp( );
		$testfunctions = new testFunctions( );

		$testfunctions->createZeitgeistTable( 'game_assemblages' );
		$testfunctions->createZeitgeistTable( 'game_components' );
		$testfunctions->createZeitgeistTable( 'game_assemblage_components' );

		// create assemblage
		$assemblagename = uniqid( );
		$assemblagedescription = uniqid( );
		$assemblageid = $this->zgGamesetup->createAssemblage( $assemblagename, $assemblagedescription );

		// create component
		$componentname = uniqid( );
		$componentdescription = uniqid( );
		$componentid = $this->zgGamesetup->createComponent( $componentname, $componentdescription );

		// test adding and removing the component for the assemblage
		$this->zgGamesetup->addComponentToAssemblage( $componentid, $assemblageid );
		$ret = $this->zgGamesetup->removeComponentFromAssemblage( $componentid, $assemblageid );
		$this->assertTrue( $ret );

		// check database
		$res = $this->database->query( "SELECT * FROM game_assemblage_components" );
		$ret = $this->database->numRows( $res );
		$this->assertEqual( $ret, 0 );

		$testfunctions->dropZeitgeistTable( 'game_component_' . $componentid );
		$testfunctions->dropZeitgeistTable( 'game_components' );
		$testfunctions->dropZeitgeistTable( 'game_assemblages' );
		$testfunctions->dropZeitgeistTable( 'game_assemblage_components' );
		$this->tearDown( );
	}
}

if ( !defined( 'MULTITEST' ) )
{
	$test = &new TestSuite( 'zgGamesetupTest Unit Tests' );

	$testfunctions = new testFunctions( );
	$test->addTestCase( new zgGamesetupTest( ) );

	$test->run( new HtmlReporter( ) );
}