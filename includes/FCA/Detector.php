<?php

class FCA_Detector {
	/**
	 * @return FCA_Detector
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	/**
	 * @return bool
	 */
	public function is_mobile() {
		require_once dirname( __FILE__ ) . '/Detector/Mobile.php';

		$detector = new FCA_Detector_Mobile();

		return $detector->is_mobile();
	}

	/**
	 * @return bool
	 */
	public function is_robot() {
		require_once dirname( __FILE__ ) . '/Detector/Robot.php';

		$detector = new FCA_Detector_Robot();

		return $detector->is_robot();
	}
}
