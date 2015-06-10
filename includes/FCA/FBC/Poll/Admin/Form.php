<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/Form.php';

require_once dirname( __FILE__ ) . '/../../PollManager.php';

abstract class FCA_FBC_Poll_Admin_Form extends FCA_Form {
	private static $post_meta;

	/**
	 * @return string
	 */
	abstract public function get_fields();

	public function __construct() {
		parent::__construct();

		require_once dirname( __FILE__ ) . '/../../Loader.php';
		$this->set_loader( FCA_FBC_Loader::get_instance() );
	}

	public function make_field( $type, $name, $label = '', $content = array(), $attributes = array() ) {
		if ( strpos( $name, '[]' ) !== false ) {
			$name = $this->meta_field( str_replace( '[]', '', $name ) ) . '[]';
		} else {
			$name = $this->meta_field( $name );
		}

		return parent::make_field( $type, $name, $label, $content, $attributes );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	public function meta_field( $field ) {
		return FCA_FBC_PollManager::META_KEY_DATA . '[' . $field . ']';
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_data( $key = null, $default = null ) {
		if ( empty( $this->data ) ) {
			$this->setup_data();
		}

		return parent::get_data( $key, $default );
	}

	private function setup_data() {
		if ( empty( self::$post_meta ) && ! empty( $GLOBALS['post'] ) ) {
			self::$post_meta = get_post_meta( $GLOBALS['post']->ID, FCA_FBC_PollManager::META_KEY_DATA, true );
		}

		$this->set_data( self::$post_meta );
	}
}
