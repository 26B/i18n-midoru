<?php

namespace TwentySixB\Translations;

use ArrayObject;

/**
 * Handles the lock file, reading and writing.
 */
class LockHandler extends ArrayObject {

	private static $instance = null;

	const FILENAME = 'midoru.lock';

	private $path = '';

	private $md5 = '';

	public static function get_instance() {
		return self::$instance === null ? self::$instance = new LockHandler() : self::$instance;
	}

	private function __construct() {
		$this->path = getcwd() . '/' . self::FILENAME;
		$file       = fopen( $this->path, 'r' );

		$data = [];
		if ( $file ) {
			$data = json_decode( fread( $file, filesize( $this->path ) ), true );
			fclose( $file );
		}

		// We use md5 to verify dirty state due to weird behavior of ArrayObject with indirect modification.
		$this->md5 = md5( json_encode( $data ) );

		parent::__construct( $data );
	}

	public function write() {
		$data = $this->getArrayCopy();

		// Avoid unnecessary writes.
		if ( md5( json_encode( $data ) ) === $this->md5 ) {
			return;
		}

		$file = fopen( $this->path, 'w' );
		if ( ! $file ) {
			throw new \Exception( 'Failure to write lock file.' );
		}

		// TODO: Should we save the file its empty?
		fwrite( $file, empty( $data ) ? '{}' : json_encode( $data, JSON_PRETTY_PRINT ) );
		fclose( $file );
	}
}
