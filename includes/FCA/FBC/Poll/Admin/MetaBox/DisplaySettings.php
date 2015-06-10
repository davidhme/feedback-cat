<?php

require_once dirname( __FILE__ ) . '/../FormMetaBox.php';

class FCA_FBC_Poll_Admin_MetaBox_DisplaySettings extends FCA_FBC_Poll_Admin_FormMetaBox {
	public function get_title() {
		return 'Display Settings';
	}

	/**
	 * @return FCA_FBC_Poll_Admin_Form
	 */
	public function get_form() {
		require_once dirname( __FILE__ ) . '/DisplaySettingsForm.php';

		return new FCA_FBC_Poll_Admin_MetaBox_DisplaySettingsForm();
	}

	public function on_display() {
		$this->enqueue_class_file( 'js' );
	}
}
