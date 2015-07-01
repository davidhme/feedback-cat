<?php
/**
 * Plugin Name: Feedback Cat
 * Plugin URI: https://fatcatapps.com/feedbackcat
 * Version: 1.1
 * Author: Fatcat Apps
 * Author URI: https://fatcatapps.com/feedbackcat
 * Description: Build onpage survey & feedback forms in minutes.
 */

define( 'FCA_FBC_PLUGIN_FILE', __FILE__ );
define( 'FCA_FBC_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'FCA_FBC_INCLUDES_DIR', FCA_FBC_PLUGIN_DIR . '/includes' );
define( 'FCA_FBC_LIB_DIR', FCA_FBC_PLUGIN_DIR . '/lib' );
define( 'FCA_FBC_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'FCA_FBC_PLUGIN_NAME', 'Feedback Cat' );

function fca_fbc_init() {
	if ( is_admin() ) {
		require_once FCA_FBC_INCLUDES_DIR . '/FCA/FBC/Poll/Admin/Component.php';
		$component = FCA_FBC_Poll_Admin_Component::get_instance();
	} else {
		require_once FCA_FBC_INCLUDES_DIR . '/FCA/FBC/Poll/FrontEnd/Component.php';
		$component = FCA_FBC_Poll_FrontEnd_Component::get_instance();
	}

	require_once FCA_FBC_INCLUDES_DIR . '/FCA/ComponentManager.php';
	$component_manager = FCA_ComponentManager::get_instance();
	$component_manager->register( $component );

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
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
