<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/MetaBox.php';

abstract class FCA_FBC_Poll_Admin_FormMetaBox extends FCA_MetaBox {
	/**
	 * @return FCA_FBC_Poll_Admin_Form
	 */
	abstract public function get_form();

	public function display_content() {
		parent::display_content();

		echo $this->get_form()->get_fields();
	}

	public function enqueue_class_file( $type = null ) {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';
		require_once dirname( __FILE__ ) . '/../../Loader.php';

		FCA_FBC_Loader::get_instance()->load( fca_fbc_get_class_file( get_class( $this ) ), $type );
	}

	public function get_post_type() {
		require_once dirname( __FILE__ ) . '/Component.php';

		return FCA_FBC_Poll_Component::POST_TYPE;
	}
}
