<?php

namespace TwentySixB\Translations;

use ArrayObject;

/**
 * Handles the lock file, reading and writing.
 *
 * @since      0.0.0
 * @package    TODO:
 * @subpackage TODO:
 * @author     TODO:
 */
class LockHandler extends ArrayObject {

	/**
	 * Singleton instance.
	 *
	 * @since 0.0.0
	 * @var   null|LockHandler
	 */
	private static $instance = null;

	/**
	 * Lock file name.
	 *
	 * @since 0.0.0
	 * @var   string
	 */
	const FILENAME = 'i18n-midoru.lock';

	/**
	 * File path to lock.
	 *
	 * @since 0.0.0
	 * @var   string
	 */
	private $path = '';

	/**
	 * MD5 of the data read on the first instance. Used for dirty state handling.
	 *
	 * @since 0.0.0
	 * @var   string
	 */
	private $md5 = '';

	/**
	 * Get the singleton instance or create one if it doesn't exist.
	 *
	 * @since  0.0.0
	 * @return LockHandler
	 */
	public static function get_instance() : LockHandler {
		return self::$instance === null ? self::$instance = new LockHandler() : self::$instance;
	}

	/**
	 * Read the data from lock file if it exists.
	 *
	 * @since 0.0.0
	 */
	private function __construct() {
		$this->path = getcwd() . '/' . self::FILENAME;
		$file       = @fopen( $this->path, 'r' );

		$data = [];
		if ( $file ) {
			$data = json_decode( fread( $file, filesize( $this->path ) ), true );
			fclose( $file );
		}

		// We use md5 to verify dirty state due to weird behavior of ArrayObject with indirect modification.
		$this->md5 = md5( json_encode( $data ) );

		parent::__construct( $data );
	}

	/**
	 * Write the currently loaded data to the lock file, if there are any changes.
	 *
	 * @since  0.0.0
	 * @return void
	 * @throws Exception When file open fails.
	 */
	public function write() : void {
		$data = $this->getArrayCopy();

		// Avoid unnecessary writes.
		$new_md5 = md5( json_encode( $data ) );
		if ( $new_md5 === $this->md5 ) {
			return;
		}

		$file = fopen( $this->path, 'w' );
		if ( ! $file ) {
			throw new \Exception( 'Failure to open lock file for writing.' );
		}

		$this->md5 = $new_md5;

		// TODO: Should we save the file its empty?
		fwrite( $file, empty( $data ) ? '{}' : json_encode( $data, JSON_PRETTY_PRINT ) );
		fclose( $file );
	}
}
