<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/Loader.php';

class FCA_FBC_Loader extends FCA_Loader {
	const LIB_MUSTACHE = 'fca_fbc_mustache';
	const LIB_FONT_AWESOME = 'fca_fbc_font_awesome';
	const LIB_SCSS = 'fca_fbc_scss';
	const LIB_FCA_DELAY = 'fca_fbc_delay';

	/**
	 * @return FCA_FBC_Loader
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @param array $php_library_paths
	 */
	protected function register_libraries( &$php_library_paths ) {
		parent::register_libraries( $php_library_paths );

		if ( ! class_exists( 'Mustache_Autoloader' ) ) {
			$php_library_paths[ self::LIB_MUSTACHE ] = $this->get_libraries_dir() . '/mustache.php-2.8.0/src/Mustache/Autoloader.php';
		}

		if ( ! class_exists( 'scssc' ) ) {
			$php_library_paths[ self::LIB_SCSS ] = $this->get_libraries_dir() . '/scssphp/scss.inc.php';
		}

		$this->register_media( 'css', array(
			self::LIB_FONT_AWESOME, FCA_FBC_PLUGIN_URL . '/lib/font-awesome-4.3.0/css/font-awesome' . $this->min() . '.css'
		) );

		$this->register_media( 'js', array( self::LIB_FCA_DELAY, FCA_FBC_PLUGIN_URL . '/lib/fca_delay/fca_delay.js' ) );
	}

	/**
	 * @param string $handle
	 */
	protected function require_php_library( $handle ) {
		parent::require_php_library( $handle );

		if ( $handle === self::LIB_MUSTACHE ) {
			Mustache_Autoloader::register();
		}
	}

	/**
	 * @return string
	 */
	public function get_libraries_dir() {
		return FCA_FBC_LIB_DIR;
	}

	/**
	 * @return string
	 */
	public function get_includes_dir() {
		return FCA_FBC_INCLUDES_DIR;
	}
}
