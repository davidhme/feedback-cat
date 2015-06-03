<?php

class FCA_Media {
	public static $includes_dir;
	public static $plugin_url;

	private $media = array();
	private $media_enqueued = array();

	/**
	 * @return self
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @param string $handle
	 * @param string $type
	 */
	public function enqueue( $handle, $type = null ) {
		if ( empty( $type ) ) {
			$this->enqueue( $handle, 'css' );
			$this->enqueue( $handle, 'js' );

			return;
		}

		$enqueue_key = $handle . '_' . $type;
		if ( ! empty( $this->media_enqueued[ $enqueue_key ] ) ) {
			return;
		}

		if ( strpos( $handle, '/' ) === 0 ) {
			$this->enqueue_plugin_local( $handle, $type );
		} else {
			$this->enqueue_media( $handle, $type );
		}


		$this->media_enqueued[ $enqueue_key ] = true;
	}

	/**
	 * @param array $media
	 */
	public function enqueue_all( $media ) {
		foreach ( $media as $data ) {
			if ( is_string( $data ) ) {
				$this->enqueue( $data );
			} elseif ( is_array( $data ) && count( $data ) === 2 ) {
				$this->enqueue( $data[0], $data[1] );
			}
		}
	}

	/**
	 * @param string $handle
	 * @param string $type
	 */
	private function enqueue_media( $handle, $type ) {
		if ( empty( $this->media[ $handle ] ) || empty( $this->media[ $handle ][ $type ] ) ) {
			return;
		}

		$details = $this->media[ $handle ][ $type ];
		$src     = empty( $details['src'] ) ? false : $this->resolve_src( $details['src'] );
		$deps    = empty( $details['deps'] ) ? array() : $details['deps'];
		$ver     = empty( $details['ver'] ) ? false : $details['ver'];

		if ( $type === 'css' ) {
			wp_enqueue_style( $handle, $src, $deps, $ver, empty( $details['media'] ) ? 'all' : $details['media'] );
		} elseif ( $type === 'js' ) {
			wp_enqueue_script( $handle, $src, $deps, $ver, empty( $details['in_footer'] ) ? false : $details['in_footer'] );
		}
	}

	/**
	 * @param string $type
	 * @param string $file
	 */
	private function enqueue_plugin_local( $file, $type = null ) {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		$base_name  = basename( $file );
		$class_name = str_replace( array( self::$includes_dir, '.php', '/' ), array( '', '', '_' ), $file );
		$handle     = fca_camel_case_to_underscore( $class_name );
		$src        = plugins_url( '', $file ) . '/' . str_replace( '.php', '.' . $type, $base_name );

		if ( $type === 'css' ) {
			wp_enqueue_style( $handle, $src );
		} elseif ( $type === 'js' ) {
			wp_enqueue_script( $handle, $src );
		}
	}

	/**
	 * @param string $src
	 *
	 * @return string
	 */
	private function resolve_src( $src ) {
		return str_replace( '{plugin_url}', self::$plugin_url, $src );
	}
}
