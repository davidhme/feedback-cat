<?php

if ( ! function_exists( 'fca_get_instance' ) ) {
	/**
	 * @param string $class
	 *
	 * @return mixed
	 */
	function fca_get_instance( $class ) {
		static $instances = array();

		if ( empty( $instances[ $class ] ) ) {
			$instances[ $class ] = new $class;
		}

		return $instances[ $class ];
	}
}

if ( ! function_exists( 'fca_camel_case_to_underscore' ) ) {
	/**
	 * @param $string
	 *
	 * @return string
	 */
	function fca_camel_case_to_underscore( $string ) {
		return strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $string ) );
	}
}

function fca_fbc_get_class_file( $className ) {
	return dirname( __FILE__ ) . '/' . str_replace( '_', '/', $className ) . '.php';
}
