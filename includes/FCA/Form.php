<?php

class FCA_Form {
	protected $data;

	/**
	 * @param string $fields
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function make_fields( $fields, $attributes = array() ) {
		$this->enqueue_media();

		$attributes_string = '';
		foreach ( $attributes as $key => $value ) {
			$attributes_string .= ' ' . $key;
			if ( $value ) {
				$attributes .= '="' . $value . '"';
			}
		}

		return
			'<div class="fca_form_field_container"' . $attributes_string . '>' .
			$fields .
			'</div>';
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param string $label
	 * @param array $content
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function make_field( $type, $name, $label = '', $content = array(), $attributes = array() ) {
		if ( ! empty( $content['after_label'] ) ) {
			$label .= $content['after_label'];
		}

		$field_content = ':' . $type;
		if ( ! empty( $content['after_content'] ) ) {
			$field_content .= $content['after_content'];
		}

		return call_user_func( 'K::' . $type, $name, $attributes, array_merge( array(
			'format' => $this->field_format( $label, $field_content ),
			'return' => true
		), $content ) );
	}

	/**
	 * @param string $message
	 *
	 * @return string
	 */
	public function make_info( $message ) {
		$this->enqueue_media();

		return '<span class="fca_form_info">' . $message . '</span>';
	}

	/**
	 * @param string $title
	 * @param string $content
	 *
	 * @return string
	 */
	private function field_format( $title, $content ) {
		$this->enqueue_media();

		return
			'<label class="fca_form_field">' .
			'<span class="fca_form_field_title">' . $title . '</span>' .
			'<span class="fca_form_field_content">' . $content . '</span>' .
			'</label>';
	}

	private function enqueue_media() {
		require_once dirname( __FILE__ ) . '/Media.php';

		FCA_Media::get_instance()->enqueue( __FILE__ );
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_data( $key = null, $default = null ) {
		if ( is_null( $key ) ) {
			return empty( $this->data ) ? $default : $this->data;
		} else {
			if ( empty( $this->data ) || ! array_key_exists( $key, $this->data ) ) {
				return $default;
			} else {
				return $this->data[ $key ];
			}
		}
	}

	/**
	 * @param array $data
	 */
	public function set_data( $data ) {
		$this->data = $data;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_data( $key = null ) {
		if ( is_null( $key ) ) {
			$value = $this->get_data( null, null );
		} else {
			$value = $this->get_data( $key, null );
		}

		return ! is_null( $value );
	}
}
