function fca_form_field_on_update( $field, callback, run_on_load ) {
	$field
		.change( callback )
		.mouseup( callback )
		.keyup( callback )
		.on( 'paste', callback )
		.on( 'contextmenu', callback );

	if (run_on_load) {
		callback();
	}
}
