/**
 * @var object fca_fbc
 */

jQuery( function( $ ) {
	if ( typeof fca_fbc !== 'object' || typeof fca_fbc[ 'polls' ] !== 'object' ) {
		return;
	}

	var animation_speed_poll_move = 150;
	var animation_speed_close = 250;

	var class_component = 'fca_fbc_poll_frontend_component';
	var class_question = class_component + '_question';
	var class_message = class_component + '_message';
	var class_handle = class_component + '_handle';
	var class_button = class_component + '_button';
	var class_button_send = class_button + '_send';
	var class_button_close = class_button + '_close';
	var class_button_enabled = class_button + '_enabled';
	var class_button_disabled = class_button + '_disabled';
	var class_content = class_component + '_content';
	var class_choice = class_component + '_choice';
	var class_choice_checked = class_choice + '_checked';
	var class_choice_text = class_choice + '_text';
	var class_long = class_component + '_long';

	var data_prefix = class_component;
	var data_height = data_prefix + '_height';
	var data_position = data_prefix + '_position';
	var data_poll_type = data_prefix + '_poll_type';

	var poll_type_choice = 'choice';
	var poll_type_long = 'long';

	var icon_up = 'fa-angle-up';
	var icon_down = 'fa-angle-down';
	var icon_set = function( $element, icon_name ) {
		$( 'i.fa', $element ).attr( 'class', 'fa ' + icon_name );
	};

	var position_up = 'up';
	var position_down = 'down';
	var position_reverse = function( position ) {
		return position === position_down ? position_up : position_down;
	};

	var button_send_is_enabled = function( $poll ) {
		return $( '.' + class_button_send, $poll ).hasClass( class_button_enabled );
	};

	var button_send_set_enabled = function( $poll, enabled ) {
		var $button = $( '.' + class_button_send );

		if ( enabled ) {
			$button.removeClass( class_button_disabled ).addClass( class_button_enabled );
		} else {
			$button.removeClass( class_button_enabled ).addClass( class_button_disabled );
		}
	};

	var poll_move = function( $poll, position, animated ) {
		var bottom;

		if ( animated === undefined ) {
			animated = true;
		}

		if ( position === position_up ) {
			bottom = 0;
		} else if ( position === position_down ) {
			bottom = '-' + $poll.data( data_height ) + 'px';
		} else {
			return;
		}

		if ( animated ) {
			$poll.animate( { 'bottom': bottom }, animation_speed_poll_move );
		} else {
			$poll.css( 'bottom', bottom );
		}
	};

	var poll_handle_clicked = function( $poll, $handle ) {
		var current_position = $poll.data( data_position ) || position_down;
		var new_position = position_reverse( current_position );

		if ( current_position === position_down ) {
			icon_set( $handle, icon_down );
			poll_move( $poll, position_up );
		} else {
			icon_set( $handle, icon_up );
			poll_move( $poll, position_down );
		}

		$poll.data( data_position, new_position );
	};

	var poll_button_send_clicked = function( poll_id, $poll, delay_id ) {
		if ( ! button_send_is_enabled( $poll ) ) {
			return;
		}

		$.post( window.location.href, {
			'fca_fbc_poll_id': poll_id,
			'fca_fbc_poll_answer': poll_get_data( $poll )
		} );

		var delay_descriptor = {};
		delay_descriptor[ fca_delay.key_id ] = delay_id;
		delay_descriptor[ fca_delay.key_frequency ] = fca_delay.frequency_only_once;

		fca_delay.update( delay_descriptor );

		$( '.' + class_question ).remove();
		$( '.' + class_handle ).remove();
		$( '.' + class_message ).show();
	};

	var poll_button_close_clicked = function( $poll ) {
		$poll.fadeOut( animation_speed_close, function() {
			$poll.remove();
		} );
	};

	var poll_choice_clicked = function( $poll, $choice, $choices ) {
		var was_checked = $choice.hasClass( class_choice_checked );

		$choices.removeClass( class_choice_checked );
		button_send_set_enabled( $poll, ! was_checked );

		if ( ! was_checked ) {
			$choice.addClass( class_choice_checked );
		}
	};

	var poll_choice_get_text = function( $poll ) {
		var text = null;

		$( '.' + class_choice, $poll ).each( function() {
			var $choice = $( this );

			if ( $choice.hasClass( class_choice_checked ) ) {
				text = $( '.' + class_choice_text, $choice ).text();
			}
		} );

		return text;
	};

	var poll_setup_type_choice = function( $poll ) {
		var $choices = $( '.' + class_choice, $poll );

		if ( $choices.length === 0 ) {
			return;
		}

		$poll.data( data_poll_type, poll_type_choice );

		button_send_set_enabled( $poll, false );

		$choices.click( function() {
			poll_choice_clicked( $poll, $( this ), $choices );
		} );
	};

	var poll_long_get_textarea = function( $poll ) {
		return $( '.' + class_long + ' textarea', $poll );
	};

	var poll_long_get_text = function( $poll ) {
		return poll_long_get_textarea( $poll ).val();
	};

	var poll_setup_type_long = function( $poll ) {
		var $textarea = poll_long_get_textarea( $poll );

		if ( $textarea.length === 0 ) {
			return;
		}

		$poll.data( data_poll_type, poll_type_long );

		fca_form_field_on_update( $textarea, function() {
			button_send_set_enabled( $poll, $textarea.val().replace( /^\s+|\s+$/g, '' ).length > 0 );
		}, true );
	};

	var poll_get_data = function( $poll ) {
		var poll_type = $poll.data( data_poll_type );

		if ( poll_type === poll_type_choice ) {
			return poll_choice_get_text( $poll );
		} else if ( poll_type === poll_type_long ) {
			return poll_long_get_text( $poll );
		}

		return null;
	};

	var poll_setup = function( poll_id, $poll, should_animate, delay_id ) {
		$( document.body ).append( $poll );

		$( '.' + class_handle, $poll ).click( function() {
			poll_handle_clicked( $poll, $( this ) );
		} );

		$( '.' + class_button_send, $poll ).click( function() {
			poll_button_send_clicked( poll_id, $poll, delay_id );
		} );

		$( '.' + class_button_close, $poll ).click( function() {
			poll_button_close_clicked( $poll );
		} );

		poll_setup_type_choice( $poll );
		poll_setup_type_long( $poll );

		$poll.css( 'visibility', 'hidden' ).show();
		$poll.data( data_height, $( '.' + class_content, $poll ).outerHeight() );
		poll_move( $poll, position_down, false );

		if ( should_animate ) {
			$poll.hide().css( 'visibility', 'visible' ).fadeIn( animation_speed_poll_move );
		} else {
			$poll.css( 'visibility', 'visible' );
		}
	};

	Object.keys( fca_fbc[ 'polls' ] ).forEach( function( poll_id ) {
		var poll = fca_fbc[ 'polls' ][ poll_id ];

		var data = poll[ 'data' ];
		var html = poll[ 'html' ];

		var delay_id = 'poll_' + poll_id;

		var time_on_page_in_seconds = parseInt( data[ 'poll_time_on_page' ], 10 );
		var should_animate = time_on_page_in_seconds > 0;

		var delay_descriptor = {};
		delay_descriptor[ fca_delay.key_id ] = delay_id;
		delay_descriptor[ fca_delay.key_frequency ] = data[ 'poll_display_frequency' ];
		delay_descriptor[ fca_delay.key_time_on_page_in_seconds ] = time_on_page_in_seconds;

		delay_descriptor[ fca_delay.key_cookie_path ] = fca_fbc[ 'fca_wp_cookie_path' ];
		delay_descriptor[ fca_delay.key_cookie_domain ] = fca_fbc[ 'fca_wp_cookie_domain' ];

		fca_delay.run( delay_descriptor, function() {
			poll_setup( poll_id, $( html ), should_animate, delay_id );
		} );
	} );
} );
