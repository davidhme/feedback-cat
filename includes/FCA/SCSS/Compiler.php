<?php

class FCA_SCSS_Compiler {
	/**
	 * @var string
	 */
	private $scss_file;

	/**
	 * @var array
	 */
	private $should_compile_file = array();

	/**
	 * @var scssc
	 */
	private $compiler = null;

	/**
	 * @param string $scss_file
	 */
	public function __construct( $scss_file ) {
		$this->scss_file = $scss_file;
	}

	/**
	 * @param string $css_file
	 */
	public function compile_to_file( $css_file ) {
		if ( ! $this->should_compile_file( $css_file ) ) {
			return;
		}

		file_put_contents(
			$css_file,

			"/* Generated file. Do not edit. */\n\n" .
			$this->get_compiler()->compile( file_get_contents( $this->scss_file ) )
		);

		$this->should_compile_file[ $css_file ] = false;
	}

	/**
	 * @param string $css_file
	 *
	 * @return bool
	 */
	private function should_compile_file( $css_file ) {
		if ( ! array_key_exists( $css_file, $this->should_compile_file ) ) {
			$this->should_compile_file[ $css_file ] = true;

			if ( ! file_exists( $css_file ) ) {
				return true;
			}

			$css_mtime = filemtime( $css_file );

			/**
			 * @var SplFileInfo $file_info
			 */
			$scss_dir = dirname( $this->scss_file );
			foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $scss_dir ) ) as $file_info ) {
				$path_info = pathinfo( $file_info->getBasename() );
				if ( $path_info['extension'] === 'scss' && $file_info->getMTime() > $css_mtime ) {
					return true;
				}
			}

			$this->should_compile_file[ $css_file ] = false;
		}

		return $this->should_compile_file[ $css_file ];
	}

	/**
	 * @return scssc
	 */
	private function get_compiler() {
		if ( is_null( $this->compiler ) ) {
			if ( ! class_exists( 'scssc' ) ) {
				require_once dirname( __FILE__ ) . '/Compiler/lib/scssphp/scss.inc.php';
			}

			$this->compiler = new scssc();
			$this->compiler->setImportPaths( $this->get_import_paths() );
			$this->compiler->setFormatter( 'scss_formatter_compressed' );
		}

		return $this->compiler;
	}

	/**
	 * @return array
	 */
	private function get_import_paths() {
		return array(
			dirname( $this->scss_file ),
			realpath( dirname( __FILE__ ) . '/../../..' )
		);
	}
}
