jQuery( function( $ ) {
	var $time_on_page = $( '#fca_fbc_poll_time_on_page' );
	var $time_on_page_seconds = $( '#fca_fbc_poll_time_on_page_seconds' );
	var $time_on_page_seconds_in_field = $( '#fca_fbc_poll_time_on_page_seconds_in_field' );

	var update_seconds_info = function() {
		var value = parseInt( $time_on_page.val(), 10 );
		var seconds_text = 'second' + ( value === 1 ? '' : 's' );
		var text;

		if ( value > 0 ) {
			text = 'after ' + value + ' ' + seconds_text;
		} else {
			text = 'immediately';
		}

		$time_on_page_seconds.text( text );
		$time_on_page_seconds_in_field.text( seconds_text );
	};

	fca_form_field_on_update( $time_on_page, update_seconds_info, true );
} );
