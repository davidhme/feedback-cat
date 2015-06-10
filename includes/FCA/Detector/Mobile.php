<?php

class FCA_Detector_Mobile {
	public function is_mobile() {
		return $this->is_mobile_wp() ||
		       $this->is_mobile_lib_detect_mobile_browser() ||
		       $this->is_mobile_lib_mobile_detect();
	}

	/**
	 * @return bool
	 */
	private function is_mobile_wp() {
		return wp_is_mobile();
	}

	/**
	 * @return bool
	 */
	private function is_mobile_lib_detect_mobile_browser() {
		if ( ! class_exists( 'DetectMobileBrowser' ) ) {
			require_once dirname( __FILE__ ) . '/Mobile/lib/DetectMobileBrowser/DetectMobileBrowser.php';
		}

		$detector = new DetectMobileBrowser();

		return $detector->is_mobile();
	}

	/**
	 * @return bool
	 */
	private function is_mobile_lib_mobile_detect() {
		if ( ! class_exists( 'Mobile_Detect' ) ) {
			require_once dirname( __FILE__ ) . '/Mobile/lib/Mobile-Detect-2.8.13/Mobile_Detect.php';
		}

		$detector = new Mobile_Detect();

		return $detector->isMobile();
	}
}
