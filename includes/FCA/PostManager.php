<?php

class FCA_PostManager {
	private static $nonce_field_name = 'fca_post_manager_nonce';

	/**
	 * @var callable
	 */
	private $after_save_callback;

	/**
	 * @var string
	 */
	private $post_type;

	/**
	 * @return self
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @param string $post_type
	 */
	public function register_save( $post_type ) {
		if ( is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$this->post_type = $post_type;

			add_action( 'save_post', array( $this, 'save_post' ) );
			add_filter( 'wp_insert_post_data', array( $this, 'force_published' ) );
		}
	}

	/**
	 * @param int $post_id
	 */
	public function save_post( $post_id ) {
		if ( ! empty( $this->after_save_callback ) && $this->can_save( $post_id ) ) {
			call_user_func( $this->after_save_callback, $post_id );
		}
	}

	/**
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function can_save( $post_id ) {
		return ! wp_is_post_autosave( $post_id ) &&
		       ! wp_is_post_revision( $post_id ) &&
		       $this->is_nonce_valid();
	}

	/**
	 * @return string
	 */
	public function make_nonce_field() {
		return wp_nonce_field( basename( __FILE__ ), self::$nonce_field_name, true, false );
	}

	/**
	 * @return bool
	 */
	public function is_nonce_valid() {
		return isset( $_POST[ self::$nonce_field_name ] ) &&
		       wp_verify_nonce( $_POST[ self::$nonce_field_name ], basename( __FILE__ ) );
	}

	/**
	 * @param array $post
	 *
	 * @return array
	 */
	public function force_published( $post ) {
		$status = array( 'auto-draft', 'trash' );

		if ( $post['post_type'] === $this->post_type && ! in_array( $post['post_status'], $status ) ) {
			$post['post_status'] = 'publish';
		}

		return $post;
	}

	/**
	 * @param callable $after_save_callback
	 */
	public function set_after_save_callback( $after_save_callback ) {
		$this->after_save_callback = $after_save_callback;
	}
}
