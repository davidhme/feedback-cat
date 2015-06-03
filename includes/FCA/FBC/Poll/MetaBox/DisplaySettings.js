jQuery( function( $ ) {
	var $time_on_page = $( '#fca_fbc_time_on_page' );
	var $time_on_page_seconds = $( '#fca_fbc_time_on_page_seconds' );

	var update_seconds_info = function() {
		var value = parseInt( $time_on_page.val(), 10 );
		var text;

		if ( value > 0 ) {
			text = 'after ' + value + ' second' + ( value === 1 ? '' : 's' );
		} else {
			text = 'immediately';
		}

		$time_on_page_seconds.text( text );
	};

	fca_form_field_on_update( $time_on_page, update_seconds_info, true );
} );
