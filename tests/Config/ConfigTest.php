<?php
declare(strict_types=1);

namespace Tests\Config;

use Exception;
use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Clients\Generator\WP_I18n;
use TwentySixB\Translations\Clients\Service\Localise;
use TwentySixB\Translations\Config\Config;
use TwentySixB\Translations\Input\Dataset;
use TwentySixB\Translations\Input\File;
use TwentySixB\Translations\Input\Input;

/**
 * Testing the Config class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 *
 * @coversDefaultClass TwentySixB\Translations\Config\Config
 */
class ConfigTest extends TestCase {

	/**
	 * Test get returns what is expected.
	 *
	 * @since 0.0.0
	 * @dataProvider getData
	 * @covers ::get
	 * @testdox get - returns what is expected
	 *
	 * @param  array  $expected   Expected return of get.
	 * @param  string $purpose    Name of the purpose.
	 * @param  Input  $inputs,... The various optional inputs for the config.
	 * @return void
	 */
	public function testGet( array $expected, string $purpose, Input ...$inputs ) : void {
		$config = new Config( ...$inputs );
		$output = $config->get( $purpose );
		$this->assertCount( count( $expected ), $output );
		foreach ( $output as $idx => $config ) {
			$this->assertEquals( $expected[ $idx ], $config->get_config() );
		}
	}

	/**
	 * Test get when the passed client does not exist.
	 *
	 * @since  0.0.0
	 * @covers ::get
	 * @testdox get - client does not exist
	 *
	 * @return void
	 */
	public function testGetClientDoesntExist() : void {
		$data = new Dataset( [
			'project-name' => [
				'export' => [
					"client" => 'some_client_that_doesnt_exist',
				]
			]
		] );
		try {
			( new Config( $data ) )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString(
				$data->get()['project-name']['export']['client'],
				$e->getMessage()
			);
			$this->assertStringContainsString( 'project-name', $e->getMessage() );
			return;
		}
		$this->fail( 'Exception was not thrown.' );
	}

	/**
	 * Test get when the config does not have a 'client' value.
	 *
	 * @since  0.0.0
	 * @covers ::get
	 * @testdox get - config doesn't have a 'client' value
	 *
	 * @return void
	 */
	public function testGetClientKeyDoesntExist() : void {
		$data = new Dataset( [ 'project-name' => [ 'export' => [ ] ] ] );
		try {
			( new Config( $data ) )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString( 'client', $e->getMessage() );
			$this->assertStringContainsString( 'project-name', $e->getMessage() );
			return;
		}
		$this->fail( 'Exception was not thrown.' );
	}

	/**
	 * Data for testing get.
	 *
	 * TODO: no way to override getopt for CLI, so we can't test it.
	 *
	 * @since 0.0.0
	 * @return array
	 */
	public function getData() : array {
		$basedir = \dirname( __DIR__ );
		$export_config = [
			"locale" => [ "fr" ],
			"domain" => "export_domain",
			"path"   => "export_path/",
			"ext"    => "po",
			"format" => "gettext",
			// Test client via path.
			"client" => Localise::class
		];

		// Test client via name.
		$other_export_config = $export_config;
		$other_export_config['client'] = 'localise';

		// File
		$file                                    = new File( "{$basedir}/../tests/DummyFiles/config.json" );
		$empty_file                              = new File( "{$basedir}/../tests/DummyFiles/empty-config.json" );
		$expected_file                           = $file->get();
		$key                                     = $expected_file['test_project']['key'];
		$expected_file['test_project']           = $expected_file['test_project']['export'];
		$expected_file['test_project']['name']   = 'test_project';
		$expected_file['test_project']['key']    = $key;
		$expected_file['test_project']['client'] = new Localise();

		// Dataset
		$dataset    = new Dataset( [
			'dataset_project' => [
				'export' => $export_config,
			],
			'test_project' => [
				'export' => $other_export_config,
			]
		] );

		$empty_dataset = new Dataset( [] );
		$expected_dataset = $dataset->get();
		$expected_dataset['dataset_project'] = $expected_dataset['dataset_project']['export'];
		$expected_dataset['dataset_project']['name'] = 'dataset_project';
		$expected_dataset['dataset_project']['client'] = new $expected_dataset['dataset_project']['client']();
		$expected_dataset['test_project'] = $expected_dataset['test_project']['export'];
		$expected_dataset['test_project']['name'] = 'test_project';
		$expected_dataset['test_project']['client'] = new Localise();

		$export_config['client'] = WP_I18n::class;
		$other_dataset = new Dataset ( [
			'generator_client' => [
				'make_pots' => $export_config,
			]
		] );
		$expected_other_dataset = $other_dataset->get();
		$expected_other_dataset['generator_client'] = $expected_other_dataset['generator_client']['make_pots'];
		$expected_other_dataset['generator_client']['name'] = 'generator_client';
		$expected_other_dataset['generator_client']['client'] = new $expected_other_dataset['generator_client']['client']();

		// TODO: test for generator clients.
		return [
			'No input'               => [ [], 'export' ],
			'Empty dataset'          => [ [], 'export', $empty_dataset ],
			'Empty file'             => [ [], 'export', $empty_file ],
			'Empty file and dataset' => [ [], 'export', $empty_file, $empty_dataset ],
			'Dataset'                => [ array_values( $expected_dataset ), 'export', $dataset ],
			'File'                   => [ array_values( $expected_file ), 'export', $file ],
			'Dataset and File, File\'s configs take precedence'       => [
				array_values( array_merge( $expected_dataset, $expected_file ) ),
				'export',
				$dataset,
				$file
			],
			'File and Dataset, Dataset\'s configs take precedence'       => [
				array_values( array_merge( $expected_file, $expected_dataset ) ),
				'export',
				$file,
				$dataset
			],
			'Generator Client' => [
				array_values( $expected_other_dataset ),
				'make_pots',
				$other_dataset
			],
		];
	}
}
