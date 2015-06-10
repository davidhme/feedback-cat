<?php

require_once dirname( __FILE__ ) . '/../Component.php';

class FCA_FBC_Poll_Admin_Component extends FCA_FBC_Poll_Component {
	/**
	 * @return FCA_FBC_Poll_Admin_Component
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function on_activate() {
		parent::on_activate();

		$this->setup_layout_columns();
	}

	public function on_init() {
		parent::on_init();

		require_once FCA_FBC_INCLUDES_DIR . '/FCA/PostManager.php';

		$post_manager = FCA_PostManager::get_instance();
		$post_manager->register_save( self::POST_TYPE );
		$post_manager->set_after_save_callback( array( $this, 'after_save' ) );
	}

	public function head() {
		parent::head();

		if ( get_current_screen()->id !== self::POST_TYPE ) {
			return;
		}

		$this->remove_publish_box();
		$this->remove_screen_options();
		$this->disable_auto_save();

		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
	}

	public function enter_title_here() {
		return 'Enter Name Here';
	}

	public function register_meta_boxes() {
		require_once dirname( __FILE__ ) . '/MetaBox/SaveButton.php';
		require_once dirname( __FILE__ ) . '/MetaBox/Builder.php';
		require_once dirname( __FILE__ ) . '/MetaBox/DisplaySettings.php';

		$save_button_top = new FCA_FBC_Poll_Admin_MetaBox_SaveButton();
		$save_button_top->register();

		$builder = new FCA_FBC_Poll_Admin_MetaBox_Builder();
		$builder->register();

		$display_settings = new FCA_FBC_Poll_Admin_MetaBox_DisplaySettings();
		$display_settings->register();

		$save_button_bottom = new FCA_FBC_Poll_Admin_MetaBox_SaveButton();
		$save_button_bottom->register();
	}

	public function after_save( $poll_id ) {
		require_once dirname( __FILE__ ) . '/../../PollManager.php';

		$meta_key = FCA_FBC_PollManager::META_KEY_DATA;

		if ( ! empty( $_POST[ $meta_key ] ) ) {
			FCA_FBC_PollManager::get_instance()->save_meta( $poll_id, $_POST[ $meta_key ] );
		}
	}

	private function remove_publish_box() {
		remove_meta_box( 'submitdiv', self::POST_TYPE, 'side' );
	}

	private function remove_screen_options() {
		echo '<style>.screen-meta-toggle { display: none; }</style>';
	}

	private function disable_auto_save() {
		wp_dequeue_script( 'autosave' );
	}

	private function setup_layout_columns() {
		update_user_option( get_current_user_id(), 'screen_layout_' . self::POST_TYPE, 1 );
	}
}
