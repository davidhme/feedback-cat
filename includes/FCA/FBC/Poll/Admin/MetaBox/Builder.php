<?php

require_once dirname( __FILE__ ) . '/../FormMetaBox.php';

class FCA_FBC_Poll_Admin_MetaBox_Builder extends FCA_FBC_Poll_Admin_FormMetaBox {
	public function get_title() {
		require_once dirname( __FILE__ ) . '/../Component.php';

		return FCA_FBC_Poll_Component::NAME . ' Builder';
	}

	/**
	 * @return FCA_FBC_Poll_Admin_Form
	 */
	public function get_form() {
		require_once dirname( __FILE__ ) . '/BuilderForm.php';

		return new FCA_FBC_Poll_Admin_MetaBox_BuilderForm();
	}

	public function on_display() {
		$this->enqueue_class_file( 'js' );
	}
}
