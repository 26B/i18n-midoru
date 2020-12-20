<?php
declare(strict_types=1);

namespace Tests\Input;

use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Input\Dataset;

/**
 * Testing the Dataset class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class DatasetTest extends TestCase {

	/**
	 * Test get returns the array given in constructor.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::__construct
	 * @covers ::get
	 * @testdox get - returns the array that was passed in the constructor
	 *
	 * @return void
	 */
	public function testGet() : void {
		$array = [ 'this' => 'is a', 'test' => 'array' ];
		$this->assertEquals( $array, ( new Dataset( $array ) )->get() );
	}
}
