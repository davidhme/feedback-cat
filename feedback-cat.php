<?php

/**
 * Plugin Name: Feedback Cat
 * Plugin URI: https://fatcatapps.com/
 * Version: 1.0
 * Author: Fatcat Apps
 * Author URI: https://fatcatapps.com/
 * Description: Lets you build feedback forms
 */

require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
$fca_fbc_plugin_data = get_plugin_data( __FILE__ );

define( 'FCA_FBC_PLUGIN_FILE', __FILE__ );
define( 'FCA_FBC_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'FCA_FBC_INCLUDES_DIR', FCA_FBC_PLUGIN_DIR . '/includes' );
define( 'FCA_FBC_LIB_DIR', FCA_FBC_PLUGIN_DIR . '/lib' );
define( 'FCA_FBC_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'FCA_FBC_PLUGIN_NAME', $fca_fbc_plugin_data['Name'] );

function fca_fbc_init() {
	require_once FCA_FBC_LIB_DIR . '/skelet/core/lib/K/K.php';

	require_once FCA_FBC_INCLUDES_DIR . '/FCA/Media.php';
	FCA_Media::$includes_dir = FCA_FBC_INCLUDES_DIR;
	FCA_Media::$plugin_url   = FCA_FBC_PLUGIN_URL;

	require_once FCA_FBC_INCLUDES_DIR . '/FCA/ComponentManager.php';
	$component_manager = FCA_ComponentManager::get_instance();

	require_once FCA_FBC_INCLUDES_DIR . '/FCA/FBC/Poll/Component.php';
	$component_manager->register( FCA_FBC_Poll_Component::get_instance() );

	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
		$component_manager->run_stage( FCA_ComponentManager::STAGE_INIT );
	} else {
		function fca_fbc_activate() {
			$component_manager = FCA_ComponentManager::get_instance();
			$component_manager->run_stage( FCA_ComponentManager::STAGE_ACTIVATE );
			$component_manager->run_stage( FCA_ComponentManager::STAGE_INIT );
		}

		register_activation_hook( __FILE__, 'fca_fbc_activate' );
	}
}

fca_fbc_init();
