<?php

require_once dirname( __FILE__ ) . '/../Component.php';

class FCA_FBC_Poll_FrontEnd_Component extends FCA_FBC_Poll_Component {
	/**
	 * @var Mustache_Engine
	 */
	private $mustache_engine = null;

	/**
	 * @var scssc
	 */
	private $scss_compiler = null;

	/**
	 * @var bool
	 */
	private $should_compile_scss = null;

	/**
	 * @var array
	 */
	private $polls = null;

	/**
	 * @return FCA_FBC_Poll_FrontEnd_Component
	 */
	public static function get_instance() {
		require_once FCA_FBC_INCLUDES_DIR . '/functions.php';

		return fca_get_instance( __CLASS__ );
	}

	public function init() {
		parent::init();

		if ( ! $this->should_display_polls() ) {
			return;
		}

		$libraries = array(
			array( FCA_FBC_PLUGIN_DIR . '/includes/FCA/Form.js', 'js' ),
			array( __FILE__, 'js', array( FCA_FBC_Loader::LIB_JQUERY, FCA_FBC_Loader::LIB_FCA_DELAY ) ),
			array( __FILE__, 'css' ),
			array( FCA_FBC_Loader::LIB_FONT_AWESOME, 'css' )
		);

		$this->get_loader()->load_all( $libraries );

		if ( $this->should_compile_scss() ) {
			$this->compile_scss();
		}

		$this->handle_submit();
	}

	public function populate_fca_fbc() {
		parent::populate_fca_fbc();

		if ( $this->should_display_polls() ) {
			$this->fca_fbc['polls']                = $this->get_polls();
			$this->fca_fbc['fca_wp_cookie_path']   = COOKIEPATH;
			$this->fca_fbc['fca_wp_cookie_domain'] = COOKIE_DOMAIN;
		}
	}

	private function compile_scss() {
		$this->get_loader()->load( FCA_FBC_Loader::LIB_SCSS, 'php' );

		file_put_contents(
			$this->get_css_file_path(),

			"/* Generated file. Do not edit. */\n\n" .
			$this->get_scss_compiler()->compile( file_get_contents( $this->get_scss_file_path() ) )
		);

		$this->should_compile_scss = false;
	}

	/**
	 * @param $poll_data
	 *
	 * @return string
	 */
	private function render_poll( $poll_data ) {
		$mustache_engine = $this->get_mustache_engine();

		$poll_data = $this->prepare_poll_data( $poll_data );

		return $mustache_engine->render( 'Poll',
			array_merge( array(
				'poll_answer_partial' => $mustache_engine->render(
					'Poll' . ucfirst( $poll_data['poll_question_type'] ), $poll_data
				)
			), $poll_data )
		);
	}

	/**
	 * @param array $poll_data
	 *
	 * @return array
	 */
	private function prepare_poll_data( $poll_data ) {
		if ( ! empty( $poll_data['poll_choices'] ) && ! empty( $poll_data['poll_choice_states'] ) ) {
			$choices = array();
			for ( $i = 0, $len = count( $poll_data['poll_choice_states'] ); $i < $len; $i ++ ) {
				if ( ! empty( $poll_data['poll_choice_states'][ $i ] ) ) {
					$choices[] = array(
						'index' => $i,
						'text'  => $poll_data['poll_choices'][ $i ]
					);
				}
			}
			$poll_data['poll_choices'] = $choices;
			unset( $poll_data['poll_choice_states'] );
		}

		return $poll_data;
	}

	private function handle_submit() {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['fca_fbc_poll_id'] ) && ! empty( $_POST['fca_fbc_poll_answer'] ) ) {
			require_once FCA_FBC_INCLUDES_DIR . '/FCA/Detector.php';

			if ( ! FCA_Detector::get_instance()->is_robot() ) {
				require_once dirname( __FILE__ ) . '/../../PollManager.php';
				FCA_FBC_PollManager::get_instance()->save_answer(
					(int) $_POST['fca_fbc_poll_id'],
					stripslashes( $_POST['fca_fbc_poll_answer'] ) );
			}

			exit;
		}
	}

	/**
	 * @return array
	 */
	private function get_polls() {
		if ( is_null( $this->polls ) ) {
			require_once dirname( __FILE__ ) . '/../../PollManager.php';
			$poll_manager = FCA_FBC_PollManager::get_instance();

			$polls = array();
			foreach ( $poll_manager->find_all_active() as $poll_id => $poll_data ) {
				$polls[ $poll_id ] = array(
					'data' => $poll_data,
					'html' => $this->compress_html( $this->render_poll( $poll_data ) )
				);
			}

			$this->polls = $polls;
		}

		return $this->polls;
	}

	/**
	 * @return bool
	 */
	private function should_display_polls() {
		require_once FCA_FBC_INCLUDES_DIR . '/FCA/Detector.php';

		$detector  = new FCA_Detector();
		$is_mobile = $detector->is_mobile();

		return ! $is_mobile && count( $this->get_polls() ) > 0;
	}

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	private function compress_html( $html ) {
		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);

		$replace = array(
			'>',
			'<',
			'\\1'
		);

		return preg_replace( $search, $replace, $html );
	}

	/**
	 * @return Mustache_Engine
	 */
	private function get_mustache_engine() {
		if ( is_null( $this->mustache_engine ) ) {
			$this->get_loader()->load( FCA_FBC_Loader::LIB_MUSTACHE, 'php' );

			$loader = new Mustache_Loader_FilesystemLoader( dirname( __FILE__ ) . '/Template' );

			$this->mustache_engine = new Mustache_Engine();
			$this->mustache_engine->setLoader( $loader );
			$this->mustache_engine->setPartialsLoader( $loader );
		}

		return $this->mustache_engine;
	}

	/**
	 * @return bool
	 */
	private function should_compile_scss() {
		if ( is_null( $this->should_compile_scss ) ) {
			$scss = $this->get_scss_file_path();
			$css  = $this->get_css_file_path();

			$this->should_compile_scss = ! file_exists( $css ) || filemtime( $scss ) > filemtime( $css );
		}

		return $this->should_compile_scss;
	}

	/**
	 * @return scssc
	 */
	private function get_scss_compiler() {
		if ( is_null( $this->scss_compiler ) ) {
			$loader = $this->get_loader();

			$loader->load( FCA_FBC_Loader::LIB_SCSS, 'php' );

			$this->scss_compiler = new scssc();
			$this->scss_compiler->setImportPaths( array( dirname( __FILE__ ) ) );

			if ( $loader->should_compress() ) {
				$this->scss_compiler->setFormatter( 'scss_formatter_compressed' );
			} else {
				$this->scss_compiler->setFormatter( 'scss_formatter' );
			}
		}

		return $this->scss_compiler;
	}

	/**
	 * @return string
	 */
	private function get_scss_file_path() {
		return dirname( __FILE__ ) . '/Component.scss';
	}

	/**
	 * @return string
	 */
	private function get_css_file_path() {
		return dirname( __FILE__ ) . '/Component.css';
	}

	/**
	 * @return FCA_FBC_Loader
	 */
	private function get_loader() {
		require_once dirname( __FILE__ ) . '/../../Loader.php';

		return FCA_FBC_Loader::get_instance();
	}
}
