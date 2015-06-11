<?php

class FCA_AdminLocation {
	const TYPE_POST_LIST = 'edit.php';
	const TYPE_POST_CREATE = 'post-new.php';
	const TYPE_POST_EDIT = 'post.php';

	const KEY_POST_TYPE = 'post_type';
	const KEY_POST_ID = 'post';

	/**
	 * @return FCA_AdminLocation
	 */
	public static function get_current() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @param string $location_type
	 * @param string $post_type
	 *
	 * @return bool
	 */
	public function is( $location_type, $post_type = null ) {
		$is_post_list =
			$_SERVER['REQUEST_METHOD'] === 'GET'
			&& basename( $_SERVER['SCRIPT_NAME'] ) === $location_type
			&& array_key_exists( 'post_type', $_GET );

		if ( is_null( $post_type ) ) {
			return $is_post_list;
		} else {
			return $is_post_list && $_GET['post_type'] === $post_type;
		}
	}

	/**
	 * @param string $location_type
	 * @param string[]|string $parameters
	 *
	 * @return string
	 */
	public static function compose( $location_type, $parameters = array() ) {
		$location = $location_type;

		$url_parameters = array();
		if ( $location_type === self::TYPE_POST_EDIT ) {
			$url_parameters[] = 'action=edit';
		}

		foreach ( $parameters as $key => $value ) {
			$url_parameters[] = urlencode( $key ) . '=' . urlencode( $value );
		}

		if ( ! empty( $url_parameters ) ) {
			$location .= '?' . implode( '&', $url_parameters );
		}

		return $location;
	}

	/**
	 * @param string $url
	 */
	public static function redirect( $url ) {
		header( 'Location: ' . $url );
		exit;
	}

	/**
	 * @param string $location
	 */
	public static function redirect_to_admin_location( $location ) {
		self::redirect( admin_url( $location ) );
	}
}
