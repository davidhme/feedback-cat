<?php

abstract class FCA_Loader {
	const LIB_JQUERY = 'jquery';
	const LIB_SKELET_FORMS = 'skelet_forms';

	private $php_library_paths = array();
	private $loaded = array();
	private $registered = false;
	private $enqueued_calls = array();

	/**
	 * @return string
	 */
	abstract public function get_libraries_dir();

	/**
	 * @return string
	 */
	abstract public function get_includes_dir();

	/**
	 * @return FCA_Loader
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @param string $handle
	 * @param string $type
	 * @param array $local_deps
	 */
	public function load( $handle, $type = null, $local_deps = array() ) {
		if ( empty( $type ) ) {
			$type = array( 'css', 'js', 'php' );
		}

		if ( is_array( $type ) ) {
			foreach ( $type as $type_to_load ) {
				$this->load( $handle, $type_to_load, $local_deps );
			}

			return;
		}

		if ( ! $this->registered ) {
			$this->register_libraries( $this->php_library_paths );
			$this->registered = true;
		}

		$load_key = $handle . '_' . $type;
		if ( ! empty( $this->loaded[ $load_key ] ) ) {
			return;
		}

		if ( $type === 'php' ) {
			$this->require_php_library( $handle );
		} elseif ( strpos( $handle, '/' ) === 0 ) {
			$this->prepare_to_enqueue_plugin_local( $handle, $type, $local_deps );
		} else {
			$this->prepare_to_enqueue_media( $handle, $type );
		}

		$this->loaded[ $load_key ] = true;
	}

	/**
	 * @param array $media
	 */
	public function load_all( $media ) {
		foreach ( $media as $data ) {
			if ( is_string( $data ) ) {
				$this->load( $data );
			} elseif ( is_array( $data ) ) {
				call_user_func_array( array( $this, 'load' ), $data );
			}
		}
	}

	/**
	 * @param string $handle
	 * @param string $type
	 */
	private function prepare_to_enqueue_media( $handle, $type ) {
		$this->enqueue_call( $type, array( $handle ) );
	}

	/**
	 * @param string $file
	 * @param string $type
	 * @param array $deps
	 */
	private function prepare_to_enqueue_plugin_local( $file, $type = null, $deps = array() ) {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		$base_name  = basename( $file );
		$class_name = str_replace( array( $this->get_includes_dir(), '.php', '/' ), array( '', '', '_' ), $file );
		$handle     = trim( fca_camel_case_to_underscore( $class_name ), '_' );
		$src        = plugins_url( '', $file ) . '/' . str_replace( '.php', '.' . $type, $base_name );

		$this->enqueue_call( $type, array( $handle, $src, $deps ) );
	}

	/**
	 * @param string $handle
	 */
	protected function require_php_library( $handle ) {
		if ( ! empty( $this->php_library_paths[ $handle ] ) ) {
			require_once $this->php_library_paths[ $handle ];
		}
	}

	/**
	 * @param array $php_library_paths
	 */
	protected function register_libraries( &$php_library_paths ) {
		if ( ! class_exists( 'K' ) ) {
			$php_library_paths[ self::LIB_SKELET_FORMS ] = $this->get_libraries_dir() . '/skelet/core/lib/K/K.php';
		}

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * @param string $type
	 * @param array $params
	 */
	protected function register_media( $type, $params = array() ) {
		$this->enqueue_call( $type, $params, true );
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @param bool $register
	 */
	private function enqueue_call( $type, $params = array(), $register = false ) {
		if ( $type === 'css' ) {
			$call = array( $register ? 'wp_register_style' : 'wp_enqueue_style', $params );
		} elseif ( $type === 'js' ) {
			$call = array( $register ? 'wp_register_script' : 'wp_enqueue_script', $params );
		} else {
			return;
		}

		if ( is_admin() ) {
			call_user_func_array( $call[0], $call[1] );
		} else {
			$this->enqueued_calls[] = $call;
		}
	}

	public function enqueue() {
		foreach ( $this->enqueued_calls as $enqueue_call ) {
			call_user_func_array( $enqueue_call[0], $enqueue_call[1] );
		}
	}

	public function should_compress() {
		return ! defined( 'WP_DEBUG' ) || ! WP_DEBUG;
	}

	public function min() {
		return $this->should_compress() ? '.min' : '';
	}
}
