<?php

require_once dirname( __FILE__ ) . '/../Form.php';

class FCA_FBC_Poll_Admin_MetaBox_BuilderForm extends FCA_FBC_Poll_Admin_Form {
	/**
	 * @return string
	 */
	public function get_fields() {
		require_once dirname( __FILE__ ) . '/../../../Poll.php';

		return $this->make_fields(
			'<div class="fca_form_field_group">' .
			$this->make_field_question() .
			$this->make_field_question_type() .
			'</div>' .

			'<div class="fca_form_field_group" id="fca_fbc_poll_choices" style="display: none">' .
			$this->make_field_choice() .
			'</div>' .

			'<div class="fca_form_field_group">' .
			$this->make_field_thank_you_message() .
			'</div>'
		);
	}

	/**
	 * @return string
	 */
	private function make_field_question() {
		$key = FCA_FBC_Poll::FIELD_QUESTION;

		return $this->make_field( 'textarea', $key, 'Question', array(
			'value'         => $this->get_data( $key, '' ),
			'after_content' => $this->make_info( 'The question you’re asking your visitors.' )
		), array(
			'placeholder' => 'Was this blog post informative?'
		) );
	}

	/**
	 * @return string
	 */
	private function make_field_question_type() {
		$key = FCA_FBC_Poll::FIELD_QUESTION_TYPE;

		$types      = FCA_FBC_Poll::get_types();
		$first_type = current( $types );

		return $this->make_field( 'select', $key, 'Question Type', array(
			'options'       => $types,
			'selected'      => $this->get_data( $key, $first_type ),
			'after_content' => $this->make_info( $this->make_field_question_type_info() )
		), array(
			'id' => 'fca_fbc_' . $key
		) );
	}

	/**
	 * @return string
	 */
	private function make_field_question_type_info() {
		$choice_range = FCA_FBC_Poll::MINIMUM_NUMBER_OF_CHOICES . '-' . FCA_FBC_Poll::NUMBER_OF_CHOICES;
		$key          = FCA_FBC_Poll::FIELD_QUESTION_TYPE;
		$prefix  = 'fca_fbc_' . $key . '_info';

		return
			'<span class="' . $prefix . '" id="' . $prefix . '_' . FCA_FBC_Poll::TYPE_LONG . '">' .
			'Let visitors answer using a textbox.' .
			'</span>' .

			'<span class="' . $prefix . '" id="' . $prefix . '_' . FCA_FBC_Poll::TYPE_CHOICE . '">' .
			'Let visitors answer using a multiple choice with ' . $choice_range . ' options.' .
			'</span>';
	}

	/**
	 * @return string
	 */
	private function make_field_choice() {
		$key_choices = FCA_FBC_Poll::FIELD_CHOICES;
		$key_states  = FCA_FBC_Poll::FIELD_CHOICE_STATES;

		$placeholders = array( 'Yes, I loved it', 'No', 'So-so' );

		$values = $this->get_data( $key_choices, array() );
		$states = $this->get_data( $key_states, null );

		$has_states = ! is_null( $states );

		$field = '';

		for ( $i = 0; $i < FCA_FBC_Poll::NUMBER_OF_CHOICES; $i ++ ) {
			if ( $has_states ) {
				$state = $states[ $i ] ? 'on' : 'off';
			} else {
				$state = $i > FCA_FBC_Poll::MINIMUM_NUMBER_OF_CHOICES - 1 ? 'off' : 'on';
			}

			$name = $this->meta_field( $key_states );

			$label =
				'Choice ' . ( $i + 1 ) . ' ' .
				'<a href="javascript:void(0)" class="fca_fbc_poll_choice_toggle"></a>' .
				'<input type="hidden" class="fca_fbc_poll_choice_state" name="' . $name . '[]" value="' . $state . '">';

			$field .= $this->make_field( 'input', $key_choices . '[]', $label, array(), array(
				'placeholder' => empty( $placeholders[ $i ] ) ? '' : $placeholders[ $i ],
				'value'       => empty( $values[ $i ] ) ? '' : $values[ $i ]
			) );
		}

		return $field;
	}

	/**
	 * @return string
	 */
	private function make_field_thank_you_message() {
		$key = FCA_FBC_Poll::FIELD_THANK_YOU_MESSAGE;

		return $this->make_field( 'textarea', $key, 'Thank You Message', array(
			'value' => $this->get_data( $key, 'Thanks for answering this poll!' )
		) );
	}
}
