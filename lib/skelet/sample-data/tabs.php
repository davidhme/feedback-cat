<?php

/**
 * Tabs definitions
 *
 * @package skelet
 */

// Make sure our temporary variable is empty
$tabs = array();

$tabs[ 'tab_1'] = array(
	'title'         => __( 'Tab one' ),
	'menu_title'    => __( 'Tab 1' ),
	'page'          => __( 'page_a' ),
	'submit_button' => __( 'Save Tab 1 settings' ),
	'success'       => __( 'Saved' ),
);
$tabs[ 'tab_2'] = array(
	'title'      => __( 'Tab two' ),
	'menu_title' => __( 'Tab 2' ),
	'page'       => __( 'page_a' ),
);

// Register tabs
paf_tabs( $tabs );
