<?php

namespace SMW\Tests\Page;

use SMW\Page\PageFactory;

/**
 * @covers \SMW\Page\PageFactory
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class PageFactoryTest extends \PHPUnit_Framework_TestCase {

	private $store;

	protected function setUp() {
		parent::setUp();

		$this->store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function testCanConstruct() {

		$this->assertInstanceOf(
			PageFactory::class,
			new PageFactory( $this->store )
		);
	}

	public function testNewPageFromNotRegisteredNamespaceThrowsException() {

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->atLeastOnce() )
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$instance = new PageFactory( $this->store );

		$this->setExpectedException( 'RuntimeException' );
		$instance->newPageFromTitle( $title );
	}

	/**
	 * @dataProvider titleProvider
	 */
	public function testNewPageFromTitle( $namespace, $expected ) {

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->atLeastOnce() )
			->method( 'getNamespace' )
			->will( $this->returnValue( $namespace ) );

		$instance = new PageFactory( $this->store );

		$this->assertInstanceOf(
			$expected,
			$instance->newPageFromTitle( $title )
		);
	}

	public function titleProvider() {

		$provider[] = array(
			SMW_NS_PROPERTY,
			'SMW\Page\PropertyPage'
		);

		$provider[] = array(
			SMW_NS_CONCEPT,
			'SMW\Page\ConceptPage'
		);

		return $provider;
	}

}
