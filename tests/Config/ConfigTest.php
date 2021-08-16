<?php
declare(strict_types=1);

namespace Tests\Config;

use Exception;
use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Clients\Generator\WP_I18n;
use TwentySixB\Translations\Clients\Service\Localise;
use TwentySixB\Translations\Config\Config;

/**
 * Testing the Config class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 *
 * @coversDefaultClass \TwentySixB\Translations\Config\Config
 */
class ConfigTest extends TestCase {

	public function tearDown() : void {
		$json = getcwd() . '/i18n-midoru.json';
		if ( file_exists( $json ) ) {
			unlink( $json );
		}
	}

	/**
	 * Test get returns what is expected.
	 *
	 * @since 0.0.0
	 * @dataProvider getData
	 * @covers ::__construct
	 * @covers ::get
	 * @covers ::prepare_config
	 * @covers ::get_client
	 * @testdox get - returns what is expected
	 *
	 * @param  array  $expected  Expected return of get.
	 * @param  string $purpose   Name of the purpose.
	 * @param  string $json_name Name of the dummy json.
	 * @return void
	 */
	public function testGet( array $expected, string $purpose, string $json_name ) : void {
		$this->use_this_json( $json_name );
		$config = new Config();
		$output = $config->get( $purpose );
		$this->assertCount( count( $expected ), $output );
		foreach ( $output as $idx => $project_config ) {
			$this->assertEquals( $expected[ $idx ], $project_config->get_config() );
		}
	}

	/**
	 * Test get when the passed client does not exist.
	 *
	 * @since  0.0.0
	 * @covers ::get
	 * @covers ::__construct
	 * @covers ::prepare_config
	 * @covers ::get_client
	 * @testdox get - client does not exist
	 *
	 * @return void
	 */
	public function testGetClientDoesntExist() : void {
		$this->use_this_json( 'client-doesnt-exist.json' );
		try {
			( new Config() )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString(
				'client_doesnt_exist',
				$e->getMessage()
			);
			$this->assertStringContainsString( 'test_project', $e->getMessage() );
			return;
		}
		$this->fail( 'Exception was not thrown.' );
	}

	/**
	 * Test get when the config does not have a 'client' value.
	 *
	 * @since  0.0.0
	 * @covers ::__construct
	 * @covers ::get
	 * @covers ::prepare_config
	 * @covers ::get_client
	 * @testdox get - config doesn't have a 'client' value
	 *
	 * @return void
	 */
	public function testGetClientKeyDoesntExist() : void {
		$this->use_this_json( 'no-client-value.json' );
		try {
			( new Config() )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString( 'client', $e->getMessage() );
			$this->assertStringContainsString( 'test_project', $e->getMessage() );
			return;
		}
		$this->fail( 'Exception was not thrown.' );
	}

	/**
	 * Test get when the config doesn't exist.
	 *
	 * @since  0.0.0
	 * @covers ::__construct
	 * @covers ::get
	 * @covers ::prepare_config
	 * @covers ::get_client
	 * @testdox get - json file doesn't exist
	 *
	 * @return void
	 */
	public function testJsonDoesntExist() : void {
		$json = getcwd() . '/i18n-midoru.json';
		if ( file_exists( $json ) ) {
			unlink( $json );
		}

		try {
			( new Config() )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString( $json, $e->getMessage() );
			return;
		}
		$this->fail( 'Exception was not thrown.' );
	}

	/**
	 * Test get when the config doesn't exist.
	 *
	 * @since  0.0.0
	 * @covers ::__construct
	 * @covers ::get
	 * @covers ::prepare_config
	 * @covers ::get_client
	 * @testdox get - json file doesn't exist
	 *
	 * @return void
	 */
	public function testNotJson() : void {
		$this->use_this_json( 'not-json.json' );
		try {
			( new Config() )->get( 'export' );
		} catch ( Exception $e ) {
			$this->assertStringContainsString( 'Syntax error', $e->getMessage() );
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
		$localise_data = [
			'locale' => [ 'pt_PT', 'fr' ],
			'domain' => 'test_domain',
			'path'   => 'test_output_path/',
			'ext'    => 'po',
			'format' => 'gettext',
			'client' => new Localise(),
			'key'    => 'test_key',
			'name'   => 'test_project',
		];
		$wp_i18n_data = [
			'client'      => new WP_I18n(),
			'domain'      => 'plugin-content',
			'destination' => './lib/languages/',
			'source'      => './lib/',
			'skip-js'     => true,
			'key'         => 'test_key',
			'name'        => 'test_project',
		];
		return [
			'No input'                         => [ [], 'export', 'empty-config.json' ],
			'Project without specific purpose' => [ [], 'import', 'config.json' ],
			'Project export localise'          => [ [ $localise_data ], 'export', 'config.json' ],
			'Project make_pots wp_i18n'        => [ [ $wp_i18n_data ], 'make_pots', 'config.json' ],
		];
	}

	private function use_this_json( $name ) {
		$base_dir = getcwd();
		if ( ! copy( "{$base_dir}/tests/DummyFiles/{$name}", "{$base_dir}/i18n-midoru.json") ) {
			$this->fail( "Failed to copy json from {$name}." );
		}
	}
}
