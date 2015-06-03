jQuery( function( $ ) {
	var $choices = $( '#fca_fbc_choices' );
	var $question_type = $( '#fca_fbc_question_type' );

	var choices_prepared = false;

	var update_toggle = function( $choice_toggle ) {
		var $form_field_content = $choice_toggle.parent().next( '.fca_form_field_content' );
		var $choice_state = $choice_toggle.next( '.fca_fbc_choice_state' );

		if ( $choice_state.val() === 'on' ) {
			$form_field_content.show();
			$choice_toggle.text( '(remove)' ).click( function() {
				$choice_state.val( 'off' );
				update_toggle( $choice_toggle );
			} );
		} else {
			$form_field_content.hide();
			$choice_toggle.text( '(show)' ).click( function() {
				$choice_state.val( 'on' );
				update_toggle( $choice_toggle );
			} );
		}
	};

	var prepare_choices = function() {
		if ( choices_prepared ) {
			return;
		}

		$( '.fca_fbc_choice_toggle', $choices ).each( function() {
			update_toggle( $( this ) );
		} );

		choices_prepared = true;
	};

	var show_choices = function() {
		prepare_choices();
		$choices.show();
	};

	var hide_choices = function() {
		$choices.hide();
	};

	var on_question_type_change = function() {
		if ( $question_type.val() === 'choice' ) {
			show_choices();
		} else {
			hide_choices();
		}
	};

	on_question_type_change();
	$question_type.change( on_question_type_change );
} );
