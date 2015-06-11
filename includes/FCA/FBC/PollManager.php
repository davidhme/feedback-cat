<?php

class FCA_FBC_PollManager {
	const MAXIMUM_NUMBER_OF_POLLS = 1;

	const META_KEY_DATA = 'fca_fbc_meta';

	/**
	 * @return FCA_FBC_PollManager
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function save_meta( $poll_id, $meta ) {
		require_once dirname( __FILE__ ) . '/Poll.php';

		update_post_meta( $poll_id, self::META_KEY_DATA, FCA_FBC_Poll::prepare( $meta ) );
	}

	public function save_answer( $poll_id, $answer ) {
		$post_meta = get_post_meta( $poll_id, self::META_KEY_DATA, true );

		$recipient = get_option( 'admin_email' );
		$subject   = 'Poll answered';
		$message   = 'Question: ' . $post_meta[ FCA_FBC_Poll::FIELD_QUESTION ] . "\n" .
		             'Answer: ' . $answer;

		wp_mail( $recipient, $subject, $message );

		exit;
	}

	/**
	 * @return object
	 */
	public function did_reach_maximum_number_of_polls() {
		return $this->count_all_posts() >= self::MAXIMUM_NUMBER_OF_POLLS;
	}

	/**
	 * @return array
	 */
	public function find_all_active() {
		require_once dirname( __FILE__ ) . '/../Component.php';
		require_once dirname( __FILE__ ) . '/Poll.php';

		$polls = array();

		foreach ( $this->find_all_posts() as $post ) {
			$post_meta = get_post_meta( $post->ID, self::META_KEY_DATA, true );

			if ( $post_meta[ FCA_FBC_Poll::FIELD_STATUS ] === FCA_FBC_Poll::STATUS_ACTIVE ) {
				$polls[ $post->ID ] = array_merge( array(
					'id'    => $post->ID,
					'title' => $post->post_title
				), $post_meta );
			}
		}

		return $polls;
	}

	/**
	 * @return array
	 */
	public function find_all_posts() {
		return get_posts( array(
			'post_type'      => FCA_FBC_Poll_Component::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		) );
	}

	/**
	 * @return int
	 */
	private function count_all_posts() {
		global $wpdb;

		require_once dirname( __FILE__ ) . '/Poll/Component.php';
		$post_type = FCA_FBC_Poll_Component::POST_TYPE;

		$query = "
			SELECT COUNT(*)
			FROM $wpdb->posts
			WHERE post_type = '$post_type' AND ( post_status = 'publish' OR post_status = 'trash' )
		";

		return (int) $wpdb->get_var( $query, 0, 0 );
	}
}
