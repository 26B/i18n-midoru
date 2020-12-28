<?php
declare(strict_types=1);

namespace Tests\Config;

use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Clients\Service\Localise;
use TwentySixB\Translations\Config\Project;
use TwentySixB\Translations\Exceptions\NoFilenameAvailableForPotFile;
use TwentySixB\Translations\Exceptions\NoApiKeyAvailable;

/**
 * Testing the Project class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class ProjectTest extends TestCase {

	/**
	 * Test getting the api key for a project, be it via the environment variable or the key field
	 * in the project's config.
	 *
	 * @since 0.0.0
	 * @dataProvider getApiKeyData
	 * @covers ::get_api_key
	 * @testdox get_api_key - returns what is expected
	 *
	 * @param array  $config             Array config.
	 * @param string $expected           Expected api key.
	 * @param string $api_key_prefix     Prefix for the name of the env variable.
	 * @param bool   $make_env           Whether to make an env variable.
	 * @param string $project_key_suffix Suffix for the name of the env variable.
	 * @return void
	 */
	public function testGetApiKey(
		array $config,
		string $expected,
		string $api_key_prefix,
		bool $make_env = false,
		string $project_key_suffix = ''
	) : void {
		// Set env var.
		if ( $make_env ) {
			putenv( $api_key_prefix . "{$project_key_suffix}={$expected}" );
		}

		$this->assertEquals( $expected, ( new Project( $config ) )->get_api_key( $api_key_prefix ) );

		// Clear env var.
		if ( $make_env ) {
			putenv( $api_key_prefix . $project_key_suffix );
		}
	}

	/**
	 * Test whether an expcetion is thrown when the project api key is not avaiable.
	 *
	 * @since 0.0.0
	 * @covers ::get_api_key
	 * @testdox get_api_key - exception is thrown when key isn't available
	 *
	 * @return void
	 */
	public function testGetApiKeyNoKeyAvailable() : void {
		$project = new Project( [ 'name' => 'test-name' ] );
		try {
			$project->get_api_key( 'PREFIX' );
		} catch ( NoApiKeyAvailable $e ) {
			$this->assertStringContainsString(
				'test-name',
				$e->getMessage()
			);
		}
	}

	/**
	 * Test get_locales returns what is expected.
	 *
	 * @since 0.0.0
	 * @dataProvider getLocalesData
	 * @covers ::get_locales
	 * @testdox get_locales - returns what is expected
	 *
	 * @param  array $config   Config for Project.
	 * @param  array $expected Expected locales.
	 * @return void
	 */
	public function testGetLocales( array $config, array $expected ) : void {
		$this->assertEquals( $expected, ( new Project( $config ) )->get_locales() );
	}

	/**
	 * Test get_config returns the entire config passed to the constructor.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::get_config
	 * @testdox get_config - returns the entire config
	 *
	 * @return void
	 */
	public function testGetConfig() : void {
		$config = [ 'name' => 'test-config', 'locale' => 'test' ];
		$this->assertEquals( $config, ( new Project( $config ) )->get_config() );
	}

	/**
	 * Test get_name returns the name field inside the config passed to the constructor.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::get_name
	 * @testdox get_name - returns the name in the config
	 *
	 * @return void
	 */
	public function testGetName() : void {
		$config = [ 'name' => 'test-config' ];
		$this->assertEquals( $config['name'], ( new Project( $config ) )->get_name() );
	}

	/**
	 * Test get_domain returns the domain field inside the config passed to the constructor.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::get_domain
	 * @testdox get_domain - returns the domain in the config
	 *
	 * @return void
	 */
	public function testGetDomain() : void {
		$config = [ 'name' => 'name', 'domain' => 'test-domain' ];
		$this->assertEquals( $config['domain'], ( new Project( $config ) )->get_domain() );
	}

	/**
	 * Test get_format returns the format field inside the config passed to the constructor.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::get_format
	 * @testdox get_format - returns the format in the config
	 *
	 * @return void
	 */
	public function testGetFormat() : void {
		$config = [ 'name' => 'name', 'format' => 'test-format' ];
		$this->assertEquals( $config['format'], ( new Project( $config ) )->get_format() );
	}

	/**
	 * Test get_client returns the value for 'client' inside the config passed to the constructor.
	 *
	 * @since 0.0.0
	 * @covers ::get_client
	 * @testdox get_client - returns the client in the config
	 *
	 * @return void
	 */
	public function testGetClient() : void {
		$config = [ 'name' => 'name', 'client' => new Localise() ];
		$this->assertEquals( $config['client'], ( new Project( $config ) )->get_client() );
	}

	/**
	 * Test get_skip_js returns the value for 'skip-js' inside the config passed to the constructor
	 * or the default value.
	 *
	 * @since 0.0.0
	 * @dataProvider getSkipJsData
	 * @covers ::get_skip_js
	 * @testdox get_skip_js - returns the skip-js in the config
	 *
	 * @param  array $config
	 * @param  bool  $expected
	 * @return void
	 */
	public function testGetSkipJs( array $config, bool $expected ) : void {
		$this->assertEquals( $expected, ( new Project( $config ) )->get_skip_js() );
	}

	/**
	 * Test get_wrap_jed returns the value for 'wrap-jed' inside the config passed to the
	 * constructor or the default value.
	 *
	 * @since 0.0.0
	 * @dataProvider getWrapJedData
	 * @covers ::get_wrap_jed
	 * @testdox get_wrap_jed - returns the wrap-jed in the config
	 *
	 * @param  array $config
	 * @param  bool  $expected
	 * @return void
	 */
	public function testGetWrapJed( array $config, bool $expected ) : void {
		$this->assertEquals( $expected, ( new Project( $config ) )->get_wrap_jed() );
	}

	/**
	 * Test get_path returs the path correctly given the config passed.
	 *
	 * @since 0.0.0
	 * @dataProvider getPathData
	 * @covers ::get_path
	 * @testdox get_path - returns what is expected
	 *
	 * @param  array  $config   Config for Project.
	 * @param  string $locale   Locale for output path.
	 * @param  string $expected Expected path.
	 * @return void
	 */
	public function testGetPath( array $config, string $locale, string $expected ) : void {
		$this->assertEquals( $expected, ( new Project( $config ) )->get_path( $locale ) );
	}

	//TODO: test exception thrown in get_path

	/**
	 * Test get_source_path returns the source_path in the config.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::get_source_path
	 * @testdox get_source_path - returns the source_path value in the config
	 *
	 * @return void
	 */
	public function testGetSourcePath() : void {
		$source_path = 'source/path/';
		$this->assertEquals(
			$source_path,
			( new Project( [ 'source' => $source_path ] ) )->get_source_path()
		);
	}

	/**
	 * Test get_pot_path returns whats is expected given the config.
	 *
	 * @since 0.0.0
	 * @dataProvider getPotPathData
	 * @covers ::get_pot_path
	 * @testdox get_pot_path - returns what is expected
	 *
	 * @return void
	 */
	public function testGetPotPath( array $config, string $expected ) : void {
		$this->assertEquals( $expected, ( new Project( $config ) )->get_pot_path() );
	}

	/**
	 * Test exception is thrown when filename or domain is not set in config.
	 *
	 * @since 0.0.0
	 * @covers ::get_pot_path
	 * @testdox get_pot_path - no filename/domain available
	 *
	 * @return void
	 */
	public function testGetPotPathNoFilename() : void {
		$this->expectException( NoFilenameAvailableForPotFile::class );
		( new Project( [ 'name' => 'name', 'destination' => 'path/' ] ) )->get_pot_path();
	}

	/**
	 * Data for testing get_api_key.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function getApiKeyData() : array {
		return [
			'No env key variable set, but config has key' => [
				[ 'name' => 'test-name', 'key' => 'test-key' ],
				'test-key',
				'TEST_PREFIX',
			],
			'Env key variable set'                        => [
				[ 'name' => 'test-name' ],
				'test-key',
				'TEST_PREFIX',
				true,
				'TEST_NAME'
			],
		];
	}

	/**
	 * Data for testing get_locales.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function getLocalesData() : array {
		return [
			'String'         => [ [ 'locale' => 'pt_PT' ], [ 'pt_PT' ] ],
			'Array with one' => [ [ 'locale' => [ 'pt_PT' ] ], [ 'pt_PT' ] ],
			'Array with two' => [ [ 'locale' => [ 'pt_PT', 'fr_FR' ] ], [ 'pt_PT', 'fr_FR' ] ],
		];
	}

	/**
	 * Data for testing get_path.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function getPathData() : array {
		$path      = 'output/path/';
		$domain    = 'test_domain';
		$js_handle = 'test_handle';
		$ext       = 'po';
		return [
			'Default path '                                  => [
				[
					'ext'  => $ext,
				],
				'pt_PT',
				"./pt_PT.{$ext}",
			],
			'No filename or domain'                          => [
				[
					'path' => $path,
					'ext'  => $ext,
				],
				'pt_PT',
				"{$path}pt_PT.{$ext}",
			],
			'Filename with no arguments'                                       => [
				[
					'path'     => $path,
					'ext'      => $ext,
					'filename' => 'simple_filename',
				],
				'pt_PT',
				"{$path}simple_filename",
			],
			'Filename with arguments'                                       => [
				[
					'path'     => $path,
					'ext'      => $ext,
					'filename' => 'test-{$locale}.{$ext}',
				],
				'pt_PT',
				"{$path}test-pt_PT.{$ext}",
			],
			'Domain'                                         => [
				[
					'path'   => $path,
					'ext'    => $ext,
					'domain' => $domain,
				],
				'pt_PT',
				"{$path}{$domain}-pt_PT.{$ext}",
			],
			'Domain and Filename, filename takes precedence' => [
				[
					'path'     => $path,
					'ext'      => $ext,
					'domain'   => $domain,
					'filename' => 'test-{$domain}-{$locale}.{$ext}',
				],
				'pt_PT',
				"{$path}test-{$domain}-pt_PT.{$ext}",
			],
			'With js-handle' => [
				[
					'path'      => $path,
					'ext'       => $ext,
					'domain'    => $domain,
					'filename'  => 'test-{$domain}-{$locale}-{$js-handle}.{$ext}',
					'js-handle' => $js_handle,
				],
				'pt_PT',
				"{$path}test-{$domain}-pt_PT-{$js_handle}.{$ext}",
			],
			'Theme' => [
				[
					'path' => $path,
					'type' => 'theme',
					'ext'  => $ext,
				],
				'pt_PT',
				"{$path}pt_PT.{$ext}",
			]
		];
	}

	/**
	 * Data for testing get_pot_path.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function getPotPathData(): array {
		$destination = 'output/path/';
		$domain      = 'test_domain';
		return [
			'Filename without any arguments'                                       => [
				[
					'destination' => $destination,
					'filename'    => 'simple_filename',
				],
				"{$destination}simple_filename",
			],
			'Filename with arguments'                                       => [
				[
					'destination' => $destination,
					'filename'    => 'simple_filename.{$ext}',
				],
				"{$destination}simple_filename.pot",
			],
			'Domain'                                         => [
				[
					'destination' => $destination,
					'domain'      => $domain,
				],
				"{$destination}{$domain}.pot",
			],
			'Domain and Filename, filename takes precedence' => [
				[
					'destination' => $destination,
					'domain'      => $domain,
					'filename'    => 'test-{$domain}.{$ext}',
				],
				"{$destination}test-{$domain}.pot",
			],
		];
	}

	/**
	 * Data for testing get_wrap_jed.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function getWrapJedData() : array {
		return [
			'Default value when format is not jed' => [ [ 'name' => 'name', 'format' => 'gettext' ],                  false ],
			'Default value when format is jed'     => [ [ 'name' => 'name', 'format' => 'jed' ],                      true ],
			'Not a boolean value'                  => [ [ 'name' => 'name', 'format' => 'jed', 'wrap-jed' => 'test'], true ],

			// TODO: Empty string gets converted to false, maybe we don't want this behavior.
			'Empty string'                         => [ [ 'name' => 'name', 'format' => 'jed', 'wrap-jed' => ''], false ],
			'Boolean true'                         => [ [ 'name' => 'name', 'format' => 'jed', 'wrap-jed' => true ],  true ],
			'Boolean false'                        => [ [ 'name' => 'name', 'format' => 'jed', 'wrap-jed' => false ], false ],
		];
	}

	/**
	 * Data for testing get_skip_js.
	 *
	 * @since  0.0.0
	 * @return array
	 */
	public function GetSkipJsData() : array {
		return [
			'Default value '      => [ [ 'name' => 'name' ],                     true ],
			'Not a boolean value' => [ [ 'name' => 'name', 'skip-js' => 'test'], true ],

			// TODO: Empty string gets converted to false, maybe we don't want this behavior.
			'Empty String'  => [ [ 'name' => 'name', 'skip-js' => ''], false ],
			'Boolean true'  => [ [ 'name' => 'name', 'skip-js' => true ],  true ],
			'Boolean false' => [ [ 'name' => 'name', 'skip-js' => false ], false ],
		];
	}
}
