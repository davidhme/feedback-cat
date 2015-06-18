<?php

require_once dirname( __FILE__ ) . '/../Form.php';

class FCA_FBC_Poll_Admin_MetaBox_DisplaySettingsForm extends FCA_FBC_Poll_Admin_Form {
	/**
	 * @return string
	 */
	public function get_fields() {
		require_once dirname( __FILE__ ) . '/../Component.php';
		require_once dirname( __FILE__ ) . '/../../../Poll.php';

		$name = strtolower( FCA_FBC_Poll_Component::NAME );

		return $this->make_fields(
			$this->make_field_status( $name ) .
			$this->make_field_display_frequency( $name ) .
			$this->make_field_time_on_page( $name )
		);
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function make_field_status( $name ) {
		$key = FCA_FBC_Poll::FIELD_STATUS;

		return $this->make_field( 'select', $key, 'Status', array(
			'options'       => FCA_FBC_Poll::get_statuses(),
			'selected'      => $this->get_data( $key, FCA_FBC_Poll::STATUS_INACTIVE ),
			'after_content' => $this->make_info(
				'The ' . $name . ' will not appear on your site if it\'s set to inactive.'
			)
		) );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function make_field_display_frequency( $name ) {
		$key = FCA_FBC_Poll::FIELD_DISPLAY_FREQUENCY;

		return $this->make_field( 'select', $key, 'Display Frequency', array(
			'options'       => FCA_FBC_Poll::get_display_frequencies(),
			'default'       => $this->get_data( $key, 'once' ),
			'after_content' => $this->make_info(
				'Set how frequently to show the ' . $name . '.<br>' .
				'(Once the user submits the ' . $name . ', he will never see it again)'
			)
		) );
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	private function make_field_time_on_page( $name ) {
		$key = FCA_FBC_Poll::FIELD_TIME_ON_PAGE;

		return $this->make_field( 'input', $key, 'Time on page is at least', array(
			'after_content' =>
				' <span  id="fca_fbc_' . $key . '_seconds_in_field">seconds</span>' .
				$this->make_info(
					'Show the ' . $name . ' ' .
					'<span id="fca_fbc_' . $key . '_seconds">after X seconds</span>'
				)
		), array(
			'id'    => 'fca_fbc_' . $key,
			'value' => $this->get_data( $key, 10 ),
			'class' => 'fca_form_field_short'
		) );
	}
}
