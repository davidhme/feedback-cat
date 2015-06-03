<?php

class FCA_FBC_Poll {
	const NUMBER_OF_CHOICES = 4;

	const META_KEY = 'fca_fbc_meta';

	const FIELD_QUESTION = 'question';
	const FIELD_QUESTION_TYPE = 'question_type';
	const FIELD_CHOICES = 'choices';
	const FIELD_CHOICE_STATES = 'choice_states';
	const FIELD_THANK_YOU_MESSAGE = 'thank_you_message';
	const FIELD_STATUS = 'status';
	const FIELD_DISPLAY_FREQUENCY = 'display_frequency';
	const FIELD_TIME_ON_PAGE = 'time_on_page';

	const TYPE_LONG = 'long';
	const TYPE_CHOICE = 'choice';

	const FREQUENCY_ALWAYS = 'always';
	const FREQUENCY_SESSION = 'session';
	const FREQUENCY_DAY = 'day';
	const FREQUENCY_MONTH = 'month';
	const FREQUENCY_ONCE = 'once';

	private static $types = array(
		self::TYPE_LONG   => 'Long Text Answer',
		self::TYPE_CHOICE => 'Multiple Choice'
	);

	private static $display_frequencies = array(
		self::FREQUENCY_ALWAYS  => 'On every page view',
		self::FREQUENCY_SESSION => 'Once per visit',
		self::FREQUENCY_DAY     => 'Once per day',
		self::FREQUENCY_MONTH   => 'Once per month',
		self::FREQUENCY_ONCE    => 'Only once'
	);

	/**
	 * @return array
	 */
	public static function get_types() {
		return self::$types;
	}

	/**
	 * @return array
	 */
	public static function get_display_frequencies() {
		return self::$display_frequencies;
	}

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public static function cast_fields( $fields ) {
		if ( ! empty( $fields[ self::FIELD_CHOICE_STATES ] ) ) {
			foreach ( $fields[ self::FIELD_CHOICE_STATES ] as $index => $value ) {
				$fields[ self::FIELD_CHOICE_STATES ][ $index ] = $value === 'on';
			}
		}

		foreach ( array( self::FIELD_TIME_ON_PAGE, self::FIELD_STATUS ) as $key ) {
			if ( array_key_exists( $key, $fields ) ) {
				$fields[ $key ] = (int) $fields[ $key ];
			}
		}

		return $fields;
	}
}
