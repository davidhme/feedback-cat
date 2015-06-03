<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/Component.php';

class FCA_FBC_Poll_Component implements FCA_Component {
	const POST_TYPE = 'fca_feedback_cat';
	const NAME = 'Poll';
	const NAME_PLURAL = 'Polls';

	/**
	 * @return self
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function on_activate() {
		$this->setup_layout_columns();
	}

	public function on_init() {
		add_action( 'init', array( $this, 'init' ) );

		if ( is_admin() ) {
			require_once FCA_FBC_INCLUDES_DIR . '/FCA/PostManager.php';

			$post_manager = FCA_PostManager::get_instance();
			$post_manager->register_save( self::POST_TYPE );
			$post_manager->set_after_save_callback( array( $this, 'after_save' ) );

			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}
	}

	public function init() {
		$this->register_post_type();
	}

	public function admin_head() {
		if ( get_current_screen()->id !== self::POST_TYPE ) {
			return;
		}

		$this->remove_publish_box();
		$this->remove_screen_options();
		$this->disable_auto_save();

		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ) );
	}

	private function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'menu_icon'            => 'dashicons-feedback',
			'labels'               => array(
				'name'               => self::NAME_PLURAL,
				'singular_name'      => self::NAME,
				'add_new'            => 'Create',
				'add_new_item'       => 'Create ' . self::NAME,
				'edit_item'          => 'Edit ' . self::NAME,
				'new_item'           => 'New ' . self::NAME,
				'all_items'          => 'All ' . self::NAME_PLURAL,
				'view_item'          => 'View ' . self::NAME,
				'search_items'       => 'Search ' . self::NAME,
				'not_found'          => 'No ' . self::NAME . ' Found',
				'not_found_in_trash' => 'No ' . self::NAME . ' in Trash',
				'parent_item_colon'  => '',
				'menu_name'          => FCA_FBC_PLUGIN_NAME
			),
			'public'               => false,
			'exclude_from_search'  => true,
			'publicly_queryable'   => true,
			'show_ui'              => true,
			'show_in_menu'         => true,
			'query_var'            => true,
			'rewrite'              => array( 'slug' => self::POST_TYPE ),
			'capability_type'      => 'post',
			'has_archive'          => false,
			'hierarchical'         => false,
			'menu_position'        => 105,
			'supports'             => array( 'title' ),
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' )
		) );
	}

	public function enter_title_here() {
		return 'Enter Name Here';
	}

	public function register_meta_boxes() {
		require_once dirname( __FILE__ ) . '/MetaBox/SaveButton.php';
		require_once dirname( __FILE__ ) . '/MetaBox/Builder.php';
		require_once dirname( __FILE__ ) . '/MetaBox/DisplaySettings.php';

		$save_button_top = new FCA_FBC_Poll_MetaBox_SaveButton();
		$save_button_top->register();

		$builder = new FCA_FBC_Poll_MetaBox_Builder();
		$builder->register();

		$display_settings = new FCA_FBC_Poll_MetaBox_DisplaySettings();
		$display_settings->register();

		$save_button_bottom = new FCA_FBC_Poll_MetaBox_SaveButton();
		$save_button_bottom->register();
	}

	public function after_save( $post_id ) {
		require_once dirname( __FILE__ ) . '/../Poll.php';

		$meta_key = FCA_FBC_Poll::META_KEY;

		if ( ! empty( $_POST[ $meta_key ] ) ) {
			update_post_meta( $post_id, $meta_key, FCA_FBC_Poll::cast_fields( $_POST[ $meta_key ] ) );
		}
	}

	private function setup_layout_columns() {
		update_user_option( get_current_user_id(), 'screen_layout_' . self::POST_TYPE, 1 );
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
}
