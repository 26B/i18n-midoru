<?php
declare(strict_types=1);

namespace Tests\Transformers;

use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Config\Project;
use TwentySixB\Translations\Transformers\Jed;

/**
 * Testing the Jed class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class JedTest extends TestCase {

	/**
	 * Testing transform returns what is expected.
	 *
	 * @since 0.0.0
	 *
	 * @dataProvider transformProvider
	 * @covers ::transform
	 * @testdox transform - returns what is expected
	 *
	 * @return void
	 */
	public function testTransform( $data, $config, $expected ) : void {
		$this->assertSame( $expected, ( new Jed() )->transform( $data, $config ) );
	}

	/**
	 * Data for testing transform.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function transformProvider() : array {
		return [
			'Empty data' => [
				[],
				new Project( [ 'format' => 'jed' ] ),
				[],
			],
			'Empty data with wrap' => [
				[],
				new Project(
					[
						'format'   => 'jed',
						'wrap-jed' => true,
					]
				),
				[],
			],
			'Empty json data, default wrap' => [
				[ json_encode( [] ) ],
				new Project( [ 'format' => 'jed' ] ),
				[ json_encode( [ 'locale_data' => [] ], JSON_PRETTY_PRINT ) ],
			],
			'Empty json data with wrap' => [
				[ json_encode( [] ) ],
				new Project(
					[
						'format'   => 'jed',
						'wrap-jed' => true,
					]
				),
				[ json_encode( [ 'locale_data' => [] ], JSON_PRETTY_PRINT ) ],
			],
			'Empty json data with wrap off' => [
				[ json_encode( [] ) ],
				new Project(
					[
						'format'   => 'jed',
						'wrap-jed' => False,
					]
				),
				[ json_encode( [], JSON_PRETTY_PRINT ) ],
			],
			'Data with two jsons with wrap' => [
				[
					json_encode( [ 'test' => '1' ] ),
					json_encode( [ 'test' => '2' ] )
				],
				new Project(
					[
						'format'   => 'jed',
						'wrap-jed' => True,
					]
				),
				[
					json_encode( [ 'locale_data' => [ 'test' => '1' ] ], JSON_PRETTY_PRINT ),
					json_encode( [ 'locale_data' => [ 'test' => '2' ] ], JSON_PRETTY_PRINT ),
				],
			],
		];
	}
}
