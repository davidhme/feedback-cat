<?php

require_once dirname( __FILE__ ) . '/../Form.php';

class FCA_FBC_Poll_MetaBox_SaveButtonForm extends FCA_FBC_Poll_Form {
	/**
	 * @return string
	 */
	public function get_fields() {
		return '<input type="submit" class="button button-primary button-large" value="Save">';
	}
}
