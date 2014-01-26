<?php

namespace SMW\Tests\MediaWiki;

use SMW\MediaWiki\Database;
use SMW\DBConnectionProvider;

/**
 * @covers \SMW\MediaWiki\Database
 *
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 *
 * @licence GNU GPL v2+
 * @since 1.9.0.2
 *
 * @author mwjames
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase {

	public function getClass() {
		return '\SMW\MediaWiki\Database';
	}

	public function testCanConstruct() {
		$this->assertInstanceOf( $this->getClass(), new Database( new MockDBConnectionProvider ) );
	}

	public function testNumRowsMethod() {

		$connectionProvider = new MockDBConnectionProvider();
		$database = $connectionProvider->getMockDatabase();

		$database->expects( $this->once() )
			->method( 'numRows' )
			->with( $this->equalTo( 'Fuyu' ) )
			->will( $this->returnValue( 1 ) );

		$instance = new Database( $connectionProvider );

		$this->assertEquals( 1, $instance->numRows( 'Fuyu' ) );

	}

	public function testAddQuotesMethod() {

		$connectionProvider = new MockDBConnectionProvider();
		$database = $connectionProvider->getMockDatabase();

		$database->expects( $this->once() )
			->method( 'addQuotes' )
			->with( $this->equalTo( 'Fan' ) )
			->will( $this->returnValue( 'Fan' ) );

		$instance = new Database( $connectionProvider );

		$this->assertEquals( 'Fan', $instance->addQuotes( 'Fan' ) );

	}

	/**
	 * @dataProvider dbTypeProvider
	 */
	public function testTableNameMethod( $type ) {

		$connectionProvider = new MockDBConnectionProvider();
		$database = $connectionProvider->getMockDatabase();

		$database->expects( $this->any() )
			->method( 'tableName' )
			->with( $this->equalTo( 'Foo' ) )
			->will( $this->returnValue( 'Foo' ) );

		$database->expects( $this->once() )
			->method( 'getType' )
			->will( $this->returnValue( $type ) );

		$instance = new Database( $connectionProvider );

		$this->assertEquals( 'Foo', $instance->tableName( 'Foo' ) );

	}

	public function testSelectMethod() {

		$resultWrapper = $this->getMockBuilder( 'ResultWrapper' )
			->disableOriginalConstructor()
			->getMock();

		$connectionProvider = new MockDBConnectionProvider();
		$database = $connectionProvider->getMockDatabase();

		$database->expects( $this->once() )
			->method( 'select' )
			->will( $this->returnValue( $resultWrapper ) );

		$instance = new Database( $connectionProvider );

		$this->assertInstanceOf( 'ResultWrapper', $instance->select( 'Foo', 'Bar', '', __METHOD__ ) );

	}

	public function testSelectThrowsException() {

		$this->setExpectedException( 'UnexpectedValueException' );

		$instance = new Database( new MockDBConnectionProvider );
		$this->assertInstanceOf( 'ResultWrapper', $instance->select( 'Foo', 'Bar', '', __METHOD__ ) );

	}

	public function dbTypeProvider() {
		return array(
			array( 'mysql' ),
			array( 'sqlite' ),
			array( 'postgres' )
		);
	}

}

class MockDBConnectionProvider extends \PHPUnit_Framework_TestCase implements DBConnectionProvider {

	public function getConnection() {
		return $this->createMockDBConnectionProvider()->getConnection();
	}

	public function releaseConnection() {}

	public function getMockDatabase() {

		if ( !isset( $this->database ) ) {
			$this->database = $this->getMockBuilder( 'DatabaseMysql' )
				->disableOriginalConstructor()
				->getMock();
		}

		return $this->database;
	}

	protected function createMockDBConnectionProvider() {

		$provider = $this->getMockForAbstractClass( '\SMW\DBConnectionProvider' );

		$provider->expects( $this->any() )
			->method( 'getConnection' )
			->will( $this->returnValue( $this->getMockDatabase() ) );

		return $provider;
	}

}