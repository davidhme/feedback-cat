<?php

abstract class FCA_MetaBox {
	private static $number_of_instances = array();
	private static $nonce_field_added = false;

	private $id;
	private $css_class_name;

	abstract public function get_post_type();

	abstract public function get_title();

	public function get_id() {
		if ( ! empty( $this->id ) ) {
			return $this->id;
		}

		$this->id = $this->get_css_class_name();

		if ( empty( self::$number_of_instances[ $this->id ] ) ) {
			self::$number_of_instances[ $this->id ] = 1;
		} else {
			++self::$number_of_instances[ $this->id ];
			$this->id .= '_' . self::$number_of_instances[ $this->id ];
		}

		return $this->id;
	}

	private function get_css_class_name() {
		if ( empty( $this->css_class_name ) ) {
			require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

			$this->css_class_name = fca_camel_case_to_underscore( get_class( $this ) );
		}

		return $this->css_class_name;
	}

	public function register() {
		$callback = array( $this, 'display_content' );
		add_meta_box( $this->get_id(), $this->get_title(), $callback, $this->get_post_type(), 'normal' );
		add_filter( 'postbox_classes_' . $this->get_post_type() . '_' . $this->get_id(), array( $this, 'postbox_classes' ) );
	}

	public function on_display() {
	}

	public function postbox_classes( $classes ) {
		$classes[] = $this->get_css_class_name();
		return $classes;
	}

	public function display_content() {
		$this->on_display();

		if ( ! self::$nonce_field_added ) {
			require_once FCA_FBC_INCLUDES_DIR . '/FCA/PostManager.php';
			echo FCA_PostManager::get_instance()->make_nonce_field();

			self::$nonce_field_added = true;
		}
	}
}
