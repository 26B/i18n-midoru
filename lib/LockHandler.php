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
class LockHandler {

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
	 * Lock data.
	 *
	 * @since 0.0.0
	 * @var array
	 */
	private $data = [];

	/**
	 * File path to lock.
	 *
	 * @since 0.0.0
	 * @var   string
	 */
	private $path = '';

	/**
	 * If data has been changed since the last writing.
	 *
	 * @since 0.0.0
	 * @var boolean
	 */
	private $is_dirty = false;

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

		if ( $file ) {
			$this->data = json_decode( fread( $file, filesize( $this->path ) ), true );
			fclose( $file );
		}
	}

	/**
	 * Set property value for a project.
	 *
	 * @since 0.0.0
	 * @param string $project
	 * @param string $property
	 * @param mixed  $value
	 * @return void
	 */
	public function set( string $project, string $property, $value ) : void {
		if ( $property === 'md5' ) {
			throw new \Exception( 'Property md5 is protected for the lock file.' );
		}
		$this->is_dirty                      = true;
		$this->data[ $project ][ $property ] = $value;
	}

	/**
	 * Get the property value for a project.
	 *
	 * @since 0.0.0
	 * @param string $project
	 * @param string $property
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get( string $project, string $property, $default = '' ) {
		return $this->data[ $project ][ $property ] ?? $default;
	}

	/**
	 * Write the currently loaded data to the lock file, if there are any changes.
	 *
	 * @since  0.0.0
	 * @return void
	 * @throws Exception When file open fails.
	 */
	public function write() : void {
		if ( ! $this->is_dirty ) {
			return;
		}

		$file = fopen( $this->path, 'w' );
		if ( ! $file ) {
			throw new \Exception( 'Failure to open lock file for writing.' );
		}

		// TODO: Should we save the file its empty?
		fwrite( $file, empty( $this->data ) ? '{}' : json_encode( $this->data, JSON_PRETTY_PRINT ) );
		fclose( $file );

		$this->is_dirty = false;
	}

	/**
	 * Validate if project configs changed with md5.
	 *
	 * If a project config changed, then its data will be emptied except for its new md5.
	 *
	 * @since  0.0.0
	 * @param  array $config
	 * @return void
	 */
	public function validate( array $config ) : void {
		foreach ( $config as $project => $proj_config ) {
			$md5     = md5( json_encode( $proj_config ) );
			$old_md5 = '';
			if ( isset( $this->data[ $project ]['md5'] ) ) {
				$old_md5 = $this->data[ $project ]['md5'];
			}

			if ( $md5 !== $old_md5 ) {
				unset( $this->data[ $project ] );
			}
			$this->data[ $project ]['md5'] = $md5;
		}
	}
}
