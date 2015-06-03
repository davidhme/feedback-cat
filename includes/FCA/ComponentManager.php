<?php

require_once dirname( __FILE__ ) . '/Component.php';

class FCA_ComponentManager {
	const STAGE_INIT = 'init';
	const STAGE_ACTIVATE = 'activate';

	/**
	 * @var FCA_Component[]
	 */
	private $registered_components = array();

	/**
	 * @return self
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function register( FCA_Component $component ) {
		$this->registered_components[] = $component;
	}

	public function run_stage( $stage ) {
		foreach ( $this->registered_components as $component ) {
			if ( $stage === self::STAGE_INIT ) {
				$component->on_init();
			} elseif ( $stage === self::STAGE_ACTIVATE ) {
				$component->on_activate();
			}
		}
	}
}
