<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/Component.php';

class FCA_FBC_Poll_Component extends FCA_Component {
	const POST_TYPE = 'fca_feedback_cat';
	const NAME = 'Poll';
	const NAME_PLURAL = 'Polls';

	public $fca_fbc = array();

	/**
	 * @return FCA_FBC_Poll_Component
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function on_init() {
		parent::on_init();

		add_action( 'init', array( $this, 'init' ) );

		if ( is_admin() ) {
			add_action( 'admin_head', array( $this, 'head' ) );
		} else {
			add_action( 'wp_head', array( $this, 'head' ) );
		}
	}

	public function populate_fca_fbc() {
	}

	public function init() {
		$this->register_post_type();
	}

	public function head() {
		$this->populate_fca_fbc();

		if ( empty( $this->fca_fbc ) ) {
			$fca_fbc = '{}';
		} else {
			$fca_fbc = json_encode( $this->fca_fbc );
		}

		echo '<script>fca_fbc = ' . $fca_fbc . ';</script>';
	}

	public function register_post_type() {
		require_once dirname( __FILE__ ) . '/../PollManager.php';

		$can_create = ! FCA_FBC_PollManager::get_instance()->did_reach_maximum_number_of_polls();

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
			'has_archive'          => false,
			'hierarchical'         => false,
			'menu_position'        => 105,
			'supports'             => array( 'title' => false ),
			'register_meta_box_cb' => array( $this, 'register_meta_boxes' ),
			'capability_type'      => 'post',
			'capabilities'         => array( 'create_posts' => $can_create ),
			'map_meta_cap'         => true
		) );
	}
}
