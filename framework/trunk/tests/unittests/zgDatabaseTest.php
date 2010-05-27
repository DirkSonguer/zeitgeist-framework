<?php

require_once 'tests/_configuration.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * zgDatabase test case.
 */
class zgDatabaseTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var zgDatabase
	 */
	private $zgDatabase;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp( )
	{
		parent::setUp( );
		$this->zgDatabase = new zgDatabase( );
	}


	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown( )
	{
		$this->zgDatabase = null;
		parent::tearDown( );
	}


	/**
	 * Constructs the test case.
	 */
	public function __construct( )
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests zgDatabase->__construct()
	 */
	public function test__construct( )
	{
		// TODO Auto-generated zgDatabaseTest->test__construct()
		$this->markTestIncomplete( "__construct test not implemented" );

		$this->zgDatabase->__construct( /* parameters */ );
	}


	/**
	 * Tests zgDatabase->__destruct()
	 */
	public function test__destruct( )
	{
		// TODO Auto-generated zgDatabaseTest->test__destruct()
		$this->markTestIncomplete( "__destruct test not implemented" );

		$this->zgDatabase->__destruct( /* parameters */ );
	}


	/**
	 * Tests zgDatabase->connect()
	 */
	public function testConnect_WrongServer( )
	{
		$this->setUp( );

		$ret = $this->zgDatabase->connect( 'false' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->connect()
	 */
	public function testConnect_WrongDatabase( )
	{
		$this->setUp( );

		$ret = $this->zgDatabase->connect( ZG_DB_DBSERVER, ZG_DB_USERNAME, ZG_DB_USERPASS, 'false' );
		$this->assertFalse( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->connect()
	 */
	public function testConnect_Success( )
	{
		$this->setUp( );

		$ret = $this->zgDatabase->connect( );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->close()
	 */
	public function testClose( )
	{
		$this->setUp( );

		$this->zgDatabase->connect( );
		$ret = $this->zgDatabase->close( );
		$this->assertTrue( $ret );

		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->setDBCharset()
	 */
	public function testSetDBCharset( )
	{
		// TODO Auto-generated zgDatabaseTest->testSetDBCharset()
		$this->markTestIncomplete( "setDBCharset test not implemented" );

		$this->zgDatabase->setDBCharset( /* parameters */ );
	}


	/**
	 * Tests zgDatabase->query()
	 */
	public function testQuery_IllegalQuery( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->query( 'false' );
		$this->assertFalse( $ret );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->query()
	 */
	public function testQuery_CreateInsertSelectDrop( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->query( "CREATE TABLE test(id INT, test VARCHAR(30))" );
		$this->assertTrue( $ret );

		$ret = $this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('1', 'test')" );
		$this->assertTrue( $ret );
		unset( $ret );

		$ret = $this->zgDatabase->query( "SELECT * FROM test" );
		$this->assertTrue( is_resource( $ret ) );
		unset( $ret );

		$ret = $this->zgDatabase->query( "DROP TABLE test" );
		$this->assertTrue( $ret );
		unset( $ret );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->fetchArray()
	 */
	public function testFetchArray_WithoutQuery( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->fetchArray( '' );
		$this->assertFalse( $ret );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->fetchArray()
	 */
	public function testFetchArray_Success( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$this->zgDatabase->query( "CREATE TABLE test(id INT, test VARCHAR(30))" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('1', 'test1')" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('2', 'test2')" );
		$res = $this->zgDatabase->query( "SELECT * FROM test" );

		$ret = $this->zgDatabase->fetchArray( $res );
		$testarray = array('test' => 'test1', 'id' => '1');
		$this->assertEquals( $ret, $testarray );
		unset( $ret );

		$ret = $this->zgDatabase->fetchArray( $res );
		$testarray = array('test' => 'test2', 'id' => '2');
		$this->assertEquals( $ret, $testarray );
		unset( $ret );

		$this->zgDatabase->query( "DROP TABLE test" );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->numRows()
	 */
	public function testNumRows_WithoutQuery( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->numRows( '' );
		$this->assertFalse( $ret );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->numRows()
	 */
	public function testNumRows_Success( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$this->zgDatabase->query( "CREATE TABLE test(id INT, test VARCHAR(30))" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('1', 'test1')" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('2', 'test2')" );
		$res = $this->zgDatabase->query( "SELECT * FROM test" );

		$ret = $this->zgDatabase->numRows( $res );
		$this->assertEquals( $ret, 2 );

		$this->zgDatabase->query( "DROP TABLE test" );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->affectedRows()
	 */
	public function testAffectedRows_WithoutQuery( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->affectedRows( '' );
		$this->assertEquals( $ret, 0 );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->affectedRows()
	 */
	public function testAffectedRows_Success( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$this->zgDatabase->query( "CREATE TABLE test(id INT, test VARCHAR(30))" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('1', 'test1')" );
		$this->zgDatabase->query( "INSERT INTO test(id, test) VALUES('2', 'test2')" );

		$this->zgDatabase->query( "DELETE FROM test" );
		$ret = $this->zgDatabase->affectedRows( );
		$this->assertEquals( $ret, 2 );
		unset( $ret );

		$this->zgDatabase->query( "DROP TABLE test" );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->insertId()
	 */
	public function testInsertId_EmptyID( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$ret = $this->zgDatabase->insertId( );
		$this->assertFalse( $ret );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}


	/**
	 * Tests zgDatabase->insertId()
	 */
	public function testInsertId( )
	{
		$this->setUp( );
		$this->zgDatabase->connect( );

		$this->zgDatabase->query( "CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), test VARCHAR(30))" );
		$this->zgDatabase->query( "INSERT INTO test(test) VALUES('test1')" );
		$this->zgDatabase->query( "INSERT INTO test(test) VALUES('test2')" );

		$ret = $this->zgDatabase->insertId( );
		$this->assertEquals( $ret, 2 );
		unset( $ret );

		$this->zgDatabase->query( "DROP TABLE test" );

		$this->zgDatabase->close( );
		$this->tearDown( );
	}
}

