<?php
declare(strict_types=1);

namespace Tests\Input;

use Exception;
use PHPUnit\Framework\TestCase;
use TwentySixB\Translations\Exceptions\ConfigFileNotFound;
use TwentySixB\Translations\Exceptions\ConfigFileNotValid;
use TwentySixB\Translations\Input\File;

/**
 * Testing the File class.
 *
 * @since 0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 *
 * @coversDefaultClass \TwentySixB\Translations\Input\File
 */
class FileTest extends TestCase {

	/**
	 * Set up tests.
	 *
	 * @since 0.0.0
	 * @return void
	 */
	public function setUp() :void {
		$this->basedir = \dirname( __DIR__ );
	}

	/**
	 * Test get when file doesn't exist.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::__construct
	 * @covers ::get
	 * @testdox get- file doesn't exist
	 *
	 * @return void
	 */
	public function testGetFileDoesntExist() : void {
		$this->expectException( ConfigFileNotFound::class );
		( new File( 'file/that/most/definitely/doesnt/exist.json' ) )->get();
	}

	/**
	 * Test get when file is not valid json.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::__construct
	 * @covers ::get
	 * @testdox get- file is not valid json.
	 *
	 * @return void
	 */
	public function testGetNotValidJson() : void {
		$this->expectException( ConfigFileNotValid::class );
		( new File( "{$this->basedir}/DummyFiles/not-json.json" ) )->get();
	}

	/**
	 * Test get when file exists and is valid json.
	 *
	 * @since 0.0.0
	 *
	 * @covers ::__construct
	 * @covers ::get
	 * @testdox get- file exists and is valid json.
	 *
	 * @return void
	 */
	public function testGetSuccess() : void {
		$config = json_decode( file_get_contents( "{$this->basedir}/DummyFiles/config.json" ), true );
		$this->assertEquals( $config, ( new File( "{$this->basedir}/DummyFiles/config.json" ) )->get() );
	}
}
