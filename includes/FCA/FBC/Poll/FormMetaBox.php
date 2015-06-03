<?php

require_once FCA_FBC_INCLUDES_DIR . '/FCA/MetaBox.php';

abstract class FCA_FBC_Poll_FormMetaBox extends FCA_MetaBox {
	/**
	 * @return FCA_FBC_Poll_Form
	 */
	abstract public function get_form();

	public function display_content() {
		parent::display_content();

		echo $this->get_form()->get_fields();
	}

	public function enqueue_class_file( $type = null ) {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';
		require_once FCA_FBC_INCLUDES_DIR . '/FCA/Media.php';

		FCA_Media::get_instance()->enqueue( fca_fbc_get_class_file( get_class( $this ) ), $type );
	}

	public function get_post_type() {
		require_once dirname( __FILE__ ) . '/Component.php';

		return FCA_FBC_Poll_Component::POST_TYPE;
	}
}
