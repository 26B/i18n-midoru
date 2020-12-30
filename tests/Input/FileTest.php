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
 * @coversDefaultClass TwentySixB\Translations\Input\File
 */
class FileTest extends TestCase {

	public function setUp() :void {
		$this->basedir = \dirname( __DIR__ );
	}

	public function testGetFileDoesntExist() {
		$this->expectException( ConfigFileNotFound::class );
		( new File( 'file/that/most/definitely/doesnt/exist.json' ) )->get();
	}

	public function testGetSuccess() {
		$config = json_decode( file_get_contents( "{$this->basedir}/DummyFiles/config.json" ), true );
		$this->assertEquals( $config, ( new File( "{$this->basedir}/DummyFiles/config.json" ) )->get() );
	}

	public function testGetNotValidJson() {
		$this->expectException( ConfigFileNotValid::class );
		( new File( "{$this->basedir}/DummyFiles/not-json.json" ) )->get();
	}
}
